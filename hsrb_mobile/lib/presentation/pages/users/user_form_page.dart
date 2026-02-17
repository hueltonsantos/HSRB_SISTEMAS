import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '../../../data/repositories/user_repository.dart';
import '../../../core/services/auth_service.dart';
import '../../bloc/user_bloc.dart';

/// Página de Formulário de Usuário (Criar/Editar)
class UserFormPage extends StatelessWidget {
  final int? userId;

  const UserFormPage({Key? key, this.userId}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return BlocProvider(
      create: (context) {
        final bloc = UserBloc(
          repository: UserRepository(
            authService: context.read<AuthService>(),
          ),
        );
        if (userId != null) {
          bloc.add(LoadUserDetail(userId!));
        }
        return bloc;
      },
      child: _UserFormView(userId: userId),
    );
  }
}

class _UserFormView extends StatefulWidget {
  final int? userId;

  const _UserFormView({Key? key, this.userId}) : super(key: key);

  @override
  State<_UserFormView> createState() => _UserFormViewState();
}

class _UserFormViewState extends State<_UserFormView> {
  final _formKey = GlobalKey<FormState>();
  final _nomeController = TextEditingController();
  final _emailController = TextEditingController();
  final _senhaController = TextEditingController();
  
  bool _obscurePassword = true;
  int? _selectedPerfilId;
  int? _selectedClinicaId;
  String _selectedNivelAcesso = 'recepcionista';
  bool _isDataLoaded = false;

  bool get isEditing => widget.userId != null;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(isEditing ? 'Editar Usuário' : 'Novo Usuário'),
      ),
      body: BlocConsumer<UserBloc, UserState>(
        listener: (context, state) {
          if (state is UserDetailLoaded && !_isDataLoaded) {
            // Preencher formulário com dados do usuário
            _nomeController.text = state.user.nome;
            _emailController.text = state.user.email;
            _selectedNivelAcesso = state.user.nivelAcesso;
            _selectedPerfilId = state.user.perfilId;
            _selectedClinicaId = state.user.clinicaId;
            _isDataLoaded = true;
          } else if (state is UserOperationSuccess) {
            ScaffoldMessenger.of(context).showSnackBar(
              SnackBar(
                content: Text(state.message),
                backgroundColor: Colors.green,
              ),
            );
            Navigator.pop(context, true); // Retorna true para indicar sucesso
          } else if (state is UserError) {
            ScaffoldMessenger.of(context).showSnackBar(
              SnackBar(
                content: Text(state.message),
                backgroundColor: Colors.red,
              ),
            );
          }
        },
        builder: (context, state) {
          if (state is UserLoading && !_isDataLoaded) {
            return const Center(child: CircularProgressIndicator());
          }

          final isProcessing = state is UserLoading && _isDataLoaded;

          return Form(
            key: _formKey,
            child: ListView(
              padding: const EdgeInsets.all(16),
              children: [
                TextFormField(
                  controller: _nomeController,
                  decoration: const InputDecoration(
                    labelText: 'Nome *',
                    border: OutlineInputBorder(),
                    prefixIcon: Icon(Icons.person),
                  ),
                  enabled: !isProcessing,
                  validator: (value) {
                    if (value == null || value.isEmpty) {
                      return 'Nome é obrigatório';
                    }
                    return null;
                  },
                ),
                const SizedBox(height: 16),
                TextFormField(
                  controller: _emailController,
                  decoration: const InputDecoration(
                    labelText: 'Email *',
                    border: OutlineInputBorder(),
                    prefixIcon: Icon(Icons.email),
                  ),
                  keyboardType: TextInputType.emailAddress,
                  enabled: !isProcessing,
                  validator: (value) {
                    if (value == null || value.isEmpty) {
                      return 'Email é obrigatório';
                    }
                    if (!value.contains('@')) {
                      return 'Email inválido';
                    }
                    return null;
                  },
                ),
                const SizedBox(height: 16),
                TextFormField(
                  controller: _senhaController,
                  decoration: InputDecoration(
                    labelText: isEditing ? 'Nova Senha (opcional)' : 'Senha *',
                    border: const OutlineInputBorder(),
                    prefixIcon: const Icon(Icons.lock),
                    suffixIcon: IconButton(
                      icon: Icon(
                        _obscurePassword
                            ? Icons.visibility
                            : Icons.visibility_off,
                      ),
                      onPressed: () {
                        setState(() => _obscurePassword = !_obscurePassword);
                      },
                    ),
                  ),
                  obscureText: _obscurePassword,
                  enabled: !isProcessing,
                  validator: (value) {
                    if (!isEditing && (value == null || value.isEmpty)) {
                      return 'Senha é obrigatória';
                    }
                    if (value != null && value.isNotEmpty && value.length < 6) {
                      return 'Senha deve ter no mínimo 6 caracteres';
                    }
                    return null;
                  },
                ),
                const SizedBox(height: 16),
                DropdownButtonFormField<String>(
                  value: _selectedNivelAcesso,
                  decoration: const InputDecoration(
                    labelText: 'Nível de Acesso *',
                    border: OutlineInputBorder(),
                    prefixIcon: Icon(Icons.security),
                  ),
                  items: const [
                    DropdownMenuItem(
                      value: 'admin',
                      child: Text('Administrador'),
                    ),
                    DropdownMenuItem(
                      value: 'recepcionista',
                      child: Text('Recepcionista'),
                    ),
                    DropdownMenuItem(
                      value: 'medico',
                      child: Text('Médico'),
                    ),
                  ],
                  onChanged: isProcessing
                      ? null
                      : (value) {
                          setState(() => _selectedNivelAcesso = value!);
                        },
                ),
                const SizedBox(height: 16),
                DropdownButtonFormField<int>(
                  value: _selectedPerfilId,
                  decoration: const InputDecoration(
                    labelText: 'Perfil',
                    border: OutlineInputBorder(),
                    prefixIcon: Icon(Icons.badge),
                    helperText: 'Opcional - Define permissões do usuário',
                  ),
                  items: const [
                    // TODO: Carregar perfis da API
                    DropdownMenuItem(
                      value: null,
                      child: Text('Nenhum'),
                    ),
                    DropdownMenuItem(
                      value: 1,
                      child: Text('Administrador'),
                    ),
                  ],
                  onChanged: isProcessing
                      ? null
                      : (value) {
                          setState(() => _selectedPerfilId = value);
                        },
                ),
                const SizedBox(height: 24),
                ElevatedButton(
                  onPressed: isProcessing ? null : _saveUser,
                  style: ElevatedButton.styleFrom(
                    padding: const EdgeInsets.all(16),
                  ),
                  child: isProcessing
                      ? const SizedBox(
                          height: 20,
                          width: 20,
                          child: CircularProgressIndicator(strokeWidth: 2),
                        )
                      : Text(
                          isEditing ? 'Atualizar' : 'Criar Usuário',
                          style: const TextStyle(fontSize: 16),
                        ),
                ),
              ],
            ),
          );
        },
      ),
    );
  }

  void _saveUser() {
    if (!_formKey.currentState!.validate()) return;

    if (isEditing) {
      // Atualizar usuário existente
      context.read<UserBloc>().add(
            UpdateUser(
              id: widget.userId!,
              nome: _nomeController.text,
              email: _emailController.text,
              senha: _senhaController.text.isEmpty ? null : _senhaController.text,
              nivelAcesso: _selectedNivelAcesso,
              perfilId: _selectedPerfilId,
            ),
          );
    } else {
      // Criar novo usuário
      context.read<UserBloc>().add(
            CreateUser(
              nome: _nomeController.text,
              email: _emailController.text,
              senha: _senhaController.text,
              nivelAcesso: _selectedNivelAcesso,
              perfilId: _selectedPerfilId,
            ),
          );
    }
  }

  @override
  void dispose() {
    _nomeController.dispose();
    _emailController.dispose();
    _senhaController.dispose();
    super.dispose();
  }
}

