import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '../../../data/repositories/user_repository.dart';
import '../../../core/services/auth_service.dart';
import '../../bloc/user_bloc.dart';

/// Página de Detalhes do Usuário
class UserDetailPage extends StatelessWidget {
  final int userId;

  const UserDetailPage({Key? key, required this.userId}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return BlocProvider(
      create: (context) => UserBloc(
        repository: UserRepository(
          authService: context.read<AuthService>(),
        ),
      )..add(LoadUserDetail(userId)),
      child: _UserDetailView(userId: userId),
    );
  }
}

class _UserDetailView extends StatelessWidget {
  final int userId;

  const _UserDetailView({Key? key, required this.userId}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Detalhes do Usuário'),
        actions: [
          IconButton(
            icon: const Icon(Icons.edit),
            onPressed: () async {
              final result = await Navigator.pushNamed(
                context,
                '/users/form',
                arguments: userId,
              );
              if (result == true) {
                // Recarregar detalhes após edição
                context.read<UserBloc>().add(LoadUserDetail(userId));
              }
            },
          ),
          IconButton(
            icon: const Icon(Icons.delete),
            onPressed: () => _showDeleteDialog(context),
          ),
        ],
      ),
      body: BlocConsumer<UserBloc, UserState>(
        listener: (context, state) {
          if (state is UserOperationSuccess) {
            ScaffoldMessenger.of(context).showSnackBar(
              SnackBar(
                content: Text(state.message),
                backgroundColor: Colors.green,
              ),
            );
            // Voltar para lista após deletar
            Navigator.pop(context, true);
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
          if (state is UserLoading) {
            return const Center(child: CircularProgressIndicator());
          }

          if (state is UserDetailLoaded) {
            final user = state.user;
            
            return SingleChildScrollView(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Center(
                    child: CircleAvatar(
                      radius: 50,
                      backgroundImage: user.foto != null
                          ? NetworkImage(user.foto!)
                          : null,
                      child: user.foto == null
                          ? Text(
                              user.nome[0].toUpperCase(),
                              style: const TextStyle(fontSize: 32),
                            )
                          : null,
                    ),
                  ),
                  const SizedBox(height: 24),
                  _buildInfoCard('Informações Básicas', [
                    _buildInfoRow('Nome', user.nome),
                    _buildInfoRow('Email', user.email),
                    _buildInfoRow('Nível de Acesso', _formatNivelAcesso(user.nivelAcesso)),
                    _buildInfoRow(
                      'Status',
                      user.isActive ? 'Ativo' : 'Inativo',
                      color: user.isActive ? Colors.green : Colors.red,
                    ),
                  ]),
                  const SizedBox(height: 16),
                  _buildInfoCard('Perfil e Clínica', [
                    _buildInfoRow('Perfil', user.perfilNome ?? 'Não atribuído'),
                    _buildInfoRow('Clínica', user.clinicaNome ?? 'Não atribuída'),
                  ]),
                  if (user.permissoes.isNotEmpty) ...[
                    const SizedBox(height: 16),
                    _buildInfoCard('Permissões', [
                      Wrap(
                        spacing: 8,
                        runSpacing: 8,
                        children: user.permissoes.map((perm) {
                          return Chip(
                            label: Text(perm),
                            backgroundColor: Colors.blue.shade50,
                          );
                        }).toList(),
                      ),
                    ]),
                  ],
                  const SizedBox(height: 16),
                  _buildInfoCard('Informações Adicionais', [
                    _buildInfoRow('Data de Cadastro', user.dataCadastro ?? 'N/A'),
                    if (user.dataAtualizacao != null)
                      _buildInfoRow('Última Atualização', user.dataAtualizacao!),
                  ]),
                ],
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
                      context.read<UserBloc>().add(LoadUserDetail(userId));
                    },
                    child: const Text('Tentar Novamente'),
                  ),
                ],
              ),
            );
          }

          return const Center(child: Text('Carregue os detalhes do usuário'));
        },
      ),
    );
  }

  String _formatNivelAcesso(String nivel) {
    switch (nivel) {
      case 'admin':
        return 'Administrador';
      case 'recepcionista':
        return 'Recepcionista';
      case 'medico':
        return 'Médico';
      default:
        return nivel;
    }
  }

  Widget _buildInfoCard(String title, List<Widget> children) {
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              title,
              style: const TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.bold,
              ),
            ),
            const Divider(),
            ...children,
          ],
        ),
      ),
    );
  }

  Widget _buildInfoRow(String label, String value, {Color? color}) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 8),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          SizedBox(
            width: 120,
            child: Text(
              label,
              style: const TextStyle(
                fontWeight: FontWeight.w500,
                color: Colors.grey,
              ),
            ),
          ),
          Expanded(
            child: Text(
              value,
              style: TextStyle(color: color),
            ),
          ),
        ],
      ),
    );
  }

  void _showDeleteDialog(BuildContext context) {
    showDialog(
      context: context,
      builder: (dialogContext) => AlertDialog(
        title: const Text('Confirmar Exclusão'),
        content: const Text('Deseja realmente excluir este usuário?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(dialogContext),
            child: const Text('Cancelar'),
          ),
          TextButton(
            onPressed: () {
              Navigator.pop(dialogContext);
              context.read<UserBloc>().add(DeleteUser(userId));
            },
            style: TextButton.styleFrom(foregroundColor: Colors.red),
            child: const Text('Excluir'),
          ),
        ],
      ),
    );
  }
}
