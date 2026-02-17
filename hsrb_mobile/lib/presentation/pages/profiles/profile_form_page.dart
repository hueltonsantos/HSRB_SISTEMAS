import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '../../bloc/profile_bloc.dart';
import '../../../data/repositories/profile_repository.dart';
import '../../../core/services/auth_service.dart';

class ProfileFormPage extends StatefulWidget {
  final Map<String, dynamic>? profile;

  const ProfileFormPage({Key? key, this.profile}) : super(key: key);

  @override
  State<ProfileFormPage> createState() => _ProfileFormPageState();
}

class _ProfileFormPageState extends State<ProfileFormPage> {
  final _formKey = GlobalKey<FormState>();
  late TextEditingController _nomeController;
  late TextEditingController _descricaoController;
  
  List<dynamic> _allPermissions = [];
  final List<int> _selectedPermissions = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _nomeController = TextEditingController(text: widget.profile?['nome'] ?? '');
    _descricaoController = TextEditingController(text: widget.profile?['descricao'] ?? '');
    
    // Initialize selected permissions if editing (will be updated when detail loads)
    // Actually, we wait for DetailLoaded to get current permissions
  }

  @override
  void dispose() {
    _nomeController.dispose();
    _descricaoController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return BlocProvider(
      create: (context) {
        final bloc = ProfileBloc(
          repository: ProfileRepository(authService: context.read<AuthService>()),
        );
        
        if (widget.profile != null) {
          bloc.add(LoadProfileDetail(widget.profile!['id']));
        } else {
          bloc.add(LoadPermissions());
        }
        return bloc;
      },
      child: BlocConsumer<ProfileBloc, ProfileState>(
        listener: (context, state) {
          if (state is ProfileOperationSuccess) {
            ScaffoldMessenger.of(context).showSnackBar(
              SnackBar(content: Text(state.message), backgroundColor: Colors.green),
            );
            Navigator.pop(context, true); // Return true to refresh list
          } else if (state is ProfileError) {
            ScaffoldMessenger.of(context).showSnackBar(
              SnackBar(content: Text(state.message), backgroundColor: Colors.red),
            );
          } else if (state is ProfileDetailWithPermissionsLoaded) {
            setState(() {
              _allPermissions = state.allPermissions;
              _selectedPermissions.clear();
              
              // Populate selected permissions from profile
              // Note: profile['permissoes'] might be List<Map> or List<int> depending on API
              // Based on get.php, it returns List<Map> (id, nome, chave)
              if (state.profile['permissoes'] != null) {
                for (var p in state.profile['permissoes']) {
                  if (p is Map && p['id'] != null) {
                    _selectedPermissions.add(p['id'] as int);
                  } else if (p is int) {
                    _selectedPermissions.add(p);
                  }
                }
              }
              
              // Update controllers if not set
              if (_nomeController.text.isEmpty) _nomeController.text = state.profile['nome'] ?? '';
              if (_descricaoController.text.isEmpty) _descricaoController.text = state.profile['descricao'] ?? '';
              
              _isLoading = false;
            });
          } else if (state is PermissionsLoaded) {
            setState(() {
              _allPermissions = state.permissions;
              _isLoading = false;
            });
          } else if (state is ProfileLoading) {
            setState(() => _isLoading = true);
          }
        },
        builder: (context, state) {
          return Scaffold(
            appBar: AppBar(
              title: Text(widget.profile == null ? 'Novo Perfil' : 'Editar Perfil'),
            ),
            body: _isLoading && _allPermissions.isEmpty
                ? const Center(child: CircularProgressIndicator())
                : Form(
                    key: _formKey,
                    child: ListView(
                      padding: const EdgeInsets.all(16),
                      children: [
                        TextFormField(
                          controller: _nomeController,
                          decoration: const InputDecoration(
                            labelText: 'Nome do Perfil *',
                            border: OutlineInputBorder(),
                          ),
                          validator: (value) {
                            if (value == null || value.isEmpty) {
                              return 'Campo obrigatório';
                            }
                            return null;
                          },
                        ),
                        const SizedBox(height: 16),
                        TextFormField(
                          controller: _descricaoController,
                          decoration: const InputDecoration(
                            labelText: 'Descrição',
                            border: OutlineInputBorder(),
                          ),
                          maxLines: 2,
                        ),
                        const SizedBox(height: 24),
                        const Text(
                          'Permissões',
                          style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
                        ),
                        const SizedBox(height: 8),
                        if (_allPermissions.isEmpty)
                          const Text('Nenhuma permissão disponível')
                        else
                          ..._buildPermissionList(),
                          
                        const SizedBox(height: 24),
                        ElevatedButton(
                          onPressed: _isLoading ? null : () {
                            if (_formKey.currentState!.validate()) {
                              final bloc = context.read<ProfileBloc>();
                              if (widget.profile == null) {
                                bloc.add(CreateProfile(
                                  nome: _nomeController.text,
                                  descricao: _descricaoController.text,
                                  permissoes: _selectedPermissions,
                                ));
                              } else {
                                bloc.add(UpdateProfile(
                                  id: widget.profile!['id'],
                                  nome: _nomeController.text,
                                  descricao: _descricaoController.text,
                                  permissoes: _selectedPermissions,
                                  status: widget.profile!['status'], // Preserve status
                                ));
                              }
                            }
                          },
                          style: ElevatedButton.styleFrom(
                            padding: const EdgeInsets.symmetric(vertical: 16),
                          ),
                          child: Text(
                            widget.profile == null ? 'Criar Perfil' : 'Atualizar Perfil',
                            style: const TextStyle(fontSize: 16),
                          ),
                        ),
                      ],
                    ),
                  ),
          );
        },
      ),
    );
  }

  List<Widget> _buildPermissionList() {
    // Group permissions by module if possible, strictly by name prefix or dedicated field?
    // API returns flat list. Let's just list them properly.
    return _allPermissions.map((perm) {
      final id = perm['id'] as int;
      final nome = perm['nome'] as String;
      final descricao = perm['descricao'] as String?;
      
      final isSelected = _selectedPermissions.contains(id);
      
      return CheckboxListTile(
        title: Text(nome),
        subtitle: descricao != null ? Text(descricao) : null,
        value: isSelected,
        onChanged: (bool? value) {
          setState(() {
            if (value == true) {
              _selectedPermissions.add(id);
            } else {
              _selectedPermissions.remove(id);
            }
          });
        },
        dense: true,
      );
    }).toList();
  }
}
