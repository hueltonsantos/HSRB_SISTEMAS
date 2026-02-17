import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '../../bloc/profile_bloc.dart';
import '../../../data/repositories/profile_repository.dart';
import '../../../core/services/auth_service.dart';
import 'profile_form_page.dart';

/// Página de Listagem de Perfis
class ProfilesListPage extends StatelessWidget {
  const ProfilesListPage({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return BlocProvider(
      create: (context) => ProfileBloc(
        repository: ProfileRepository(authService: context.read<AuthService>()),
      )..add(LoadProfiles()),
      child: const ProfilesListView(),
    );
  }
}

class ProfilesListView extends StatelessWidget {
  const ProfilesListView({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Perfis e Permissões'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: () {
              context.read<ProfileBloc>().add(LoadProfiles());
            },
          ),
        ],
      ),
      body: BlocConsumer<ProfileBloc, ProfileState>(
        listener: (context, state) {
          if (state is ProfileOperationSuccess) {
            ScaffoldMessenger.of(context).showSnackBar(
              SnackBar(content: Text(state.message), backgroundColor: Colors.green),
            );
            context.read<ProfileBloc>().add(LoadProfiles());
          } else if (state is ProfileError) {
            ScaffoldMessenger.of(context).showSnackBar(
              SnackBar(content: Text(state.message), backgroundColor: Colors.red),
            );
          }
        },
        builder: (context, state) {
          if (state is ProfileLoading) {
            return const Center(child: CircularProgressIndicator());
          } else if (state is ProfilesLoaded) {
            final profiles = state.profiles;
            
            if (profiles.isEmpty) {
              return const Center(child: Text('Nenhum perfil encontrado'));
            }

            return ListView.builder(
              itemCount: profiles.length,
              padding: const EdgeInsets.all(16),
              itemBuilder: (context, index) {
                final profile = profiles[index];
                return Card(
                  margin: const EdgeInsets.only(bottom: 12),
                  child: ListTile(
                    leading: CircleAvatar(
                      child: Text(profile['nome'].substring(0, 1).toUpperCase()),
                    ),
                    title: Text(profile['nome']),
                    subtitle: Text('Usuários: ${profile['total_usuarios'] ?? 0}'),
                    trailing: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        IconButton(
                          icon: const Icon(Icons.edit, color: Colors.blue),
                          onPressed: () async {
                            final result = await Navigator.push(
                              context,
                              MaterialPageRoute(
                                builder: (context) => ProfileFormPage(profile: profile),
                              ),
                            );
                            if (result == true) {
                              // ignore: use_build_context_synchronously
                              context.read<ProfileBloc>().add(LoadProfiles());
                            }
                          },
                        ),
                        IconButton(
                          icon: const Icon(Icons.delete, color: Colors.red),
                          onPressed: () {
                            _showDeleteDialog(context, profile);
                          },
                        ),
                      ],
                    ),
                    onTap: () async {
                      final result = await Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (context) => ProfileFormPage(profile: profile),
                        ),
                      );
                      if (result == true) {
                        // ignore: use_build_context_synchronously
                        context.read<ProfileBloc>().add(LoadProfiles());
                      }
                    },
                  ),
                );
              },
            );
          }
          return const Center(child: Text('Iniciando...'));
        },
      ),
      floatingActionButton: FloatingActionButton(
        onPressed: () async {
          final result = await Navigator.push(
            context,
            MaterialPageRoute(
              builder: (context) => const ProfileFormPage(),
            ),
          );
          if (result == true) {
            // ignore: use_build_context_synchronously
            context.read<ProfileBloc>().add(LoadProfiles());
          }
        },
        child: const Icon(Icons.add),
      ),
    );
  }

  void _showDeleteDialog(BuildContext context, Map<String, dynamic> profile) {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Confirmar exclusão'),
        content: Text('Tem certeza que deseja excluir o perfil "${profile['nome']}"?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx),
            child: const Text('Cancelar'),
          ),
          TextButton(
            onPressed: () {
              Navigator.pop(ctx);
              context.read<ProfileBloc>().add(DeleteProfile(profile['id']));
            },
            child: const Text('Excluir', style: TextStyle(color: Colors.red)),
          ),
        ],
      ),
    );
  }
}
