import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '../../../data/repositories/user_repository.dart';
import '../../../core/services/auth_service.dart';
import '../../bloc/user_bloc.dart';

/// Página de Listagem de Usuários
class UsersListPage extends StatelessWidget {
  const UsersListPage({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return BlocProvider(
      create: (context) => UserBloc(
        repository: UserRepository(
          authService: context.read<AuthService>(),
        ),
      )..add(LoadUsers()),
      child: const _UsersListView(),
    );
  }
}

class _UsersListView extends StatefulWidget {
  const _UsersListView({Key? key}) : super(key: key);

  @override
  State<_UsersListView> createState() => _UsersListViewState();
}

class _UsersListViewState extends State<_UsersListView> {
  final TextEditingController _searchController = TextEditingController();
  String _searchQuery = '';

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Usuários'),
        actions: [
          IconButton(
            icon: const Icon(Icons.filter_list),
            onPressed: () {
              // TODO: Implementar filtros avançados
            },
          ),
        ],
      ),
      body: Column(
        children: [
          Padding(
            padding: const EdgeInsets.all(16.0),
            child: TextField(
              controller: _searchController,
              decoration: InputDecoration(
                hintText: 'Buscar por nome ou email...',
                prefixIcon: const Icon(Icons.search),
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                ),
                filled: true,
              ),
              onChanged: (value) {
                setState(() => _searchQuery = value);
                // Debounce search
                Future.delayed(const Duration(milliseconds: 500), () {
                  if (_searchQuery == value) {
                    context.read<UserBloc>().add(
                          LoadUsers(search: value.isEmpty ? null : value),
                        );
                  }
                });
              },
            ),
          ),
          Expanded(
            child: BlocConsumer<UserBloc, UserState>(
              listener: (context, state) {
                if (state is UserError) {
                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(
                      content: Text(state.message),
                      backgroundColor: Colors.red,
                    ),
                  );
                }
              },
              builder: (context, state) {
                if (state is UserLoading) {
                  return const Center(child: CircularProgressIndicator());
                }

                if (state is UsersLoaded) {
                  if (state.users.isEmpty) {
                    return const Center(
                      child: Text('Nenhum usuário encontrado'),
                    );
                  }

                  return RefreshIndicator(
                    onRefresh: () async {
                      context.read<UserBloc>().add(
                            LoadUsers(
                              search: _searchQuery.isEmpty ? null : _searchQuery,
                            ),
                          );
                    },
                    child: ListView.builder(
                      itemCount: state.users.length,
                      padding: const EdgeInsets.all(16),
                      itemBuilder: (context, index) {
                        final user = state.users[index];
                        return Card(
                          margin: const EdgeInsets.only(bottom: 12),
                          child: ListTile(
                            leading: CircleAvatar(
                              backgroundImage: user.foto != null
                                  ? NetworkImage(user.foto!)
                                  : null,
                              child: user.foto == null
                                  ? Text(user.nome[0].toUpperCase())
                                  : null,
                            ),
                            title: Text(user.nome),
                            subtitle: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(user.email),
                                if (user.perfilNome != null)
                                  Text(
                                    user.perfilNome!,
                                    style: TextStyle(
                                      fontSize: 12,
                                      color: Colors.grey[600],
                                    ),
                                  ),
                              ],
                            ),
                            trailing: Icon(
                              Icons.circle,
                              size: 12,
                              color: user.isActive ? Colors.green : Colors.red,
                            ),
                            isThreeLine: user.perfilNome != null,
                            onTap: () async {
                              final result = await Navigator.pushNamed(
                                context,
                                '/users/detail',
                                arguments: user.id,
                              );
                              // Recarregar se houve alteração
                              if (result == true) {
                                context.read<UserBloc>().add(LoadUsers());
                              }
                            },
                          ),
                        );
                      },
                    ),
                  );
                }

                if (state is UserError) {
                  return Center(
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        const Icon(Icons.error, size: 64, color: Colors.red),
                        const SizedBox(height: 16),
                        Text(state.message),
                        const SizedBox(height: 16),
                        ElevatedButton(
                          onPressed: () {
                            context.read<UserBloc>().add(LoadUsers());
                          },
                          child: const Text('Tentar Novamente'),
                        ),
                      ],
                    ),
                  );
                }

                return const Center(child: Text('Carregue os usuários'));
              },
            ),
          ),
        ],
      ),
      floatingActionButton: FloatingActionButton(
        onPressed: () async {
          final result = await Navigator.pushNamed(context, '/users/form');
          // Recarregar se criou novo usuário
          if (result == true) {
            context.read<UserBloc>().add(LoadUsers());
          }
        },
        child: const Icon(Icons.add),
      ),
    );
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }
}

