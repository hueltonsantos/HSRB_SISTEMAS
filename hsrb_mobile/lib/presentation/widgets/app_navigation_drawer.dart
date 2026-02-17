import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '../../data/models/user_model.dart';
import '../bloc/auth/auth_bloc.dart';
import '../bloc/auth/auth_event.dart';
import '../pages/patients/patients_list_page.dart';
import '../pages/specialties/specialties_list_page.dart';
import '../pages/clinics/clinics_list_page.dart';
import '../pages/appointments/appointments_list_page.dart';
import '../pages/guides/guides_list_page.dart';

/// Navigation Drawer com todos os modulos do sistema
class AppNavigationDrawer extends StatelessWidget {
  final UserModel? user;

  const AppNavigationDrawer({Key? key, this.user}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Drawer(
      child: ListView(
        padding: EdgeInsets.zero,
        children: [
          UserAccountsDrawerHeader(
            decoration: BoxDecoration(
              color: Theme.of(context).primaryColor,
            ),
            accountName: Text(user?.nome ?? 'Usuario'),
            accountEmail: Text(user?.email ?? ''),
            currentAccountPicture: CircleAvatar(
              backgroundColor: Colors.white,
              backgroundImage:
                  user?.foto != null ? NetworkImage(user!.foto!) : null,
              child: user?.foto == null
                  ? Text(
                      user?.nome.isNotEmpty == true
                          ? user!.nome[0].toUpperCase()
                          : 'U',
                      style: TextStyle(
                        fontSize: 32,
                        color: Theme.of(context).primaryColor,
                      ),
                    )
                  : null,
            ),
          ),
          _buildDrawerItem(
            context,
            icon: Icons.dashboard,
            title: 'Dashboard',
            onTap: () {
              Navigator.pop(context);
            },
          ),
          const Divider(),
          _buildSectionHeader('Cadastros'),
          _buildDrawerItem(
            context,
            icon: Icons.people,
            title: 'Pacientes',
            onTap: () {
              Navigator.pop(context);
              Navigator.push(
                context,
                MaterialPageRoute(builder: (_) => const PatientsListPage()),
              );
            },
          ),
          _buildDrawerItem(
            context,
            icon: Icons.person,
            title: 'Usuarios',
            onTap: () {
              Navigator.pop(context);
              Navigator.pushNamed(context, '/users');
            },
          ),
          _buildDrawerItem(
            context,
            icon: Icons.local_hospital,
            title: 'Especialidades',
            onTap: () {
              Navigator.pop(context);
              Navigator.push(
                context,
                MaterialPageRoute(
                    builder: (_) => const SpecialtiesListPage()),
              );
            },
          ),
          _buildDrawerItem(
            context,
            icon: Icons.business,
            title: 'Clinicas',
            onTap: () {
              Navigator.pop(context);
              Navigator.push(
                context,
                MaterialPageRoute(builder: (_) => const ClinicsListPage()),
              );
            },
          ),
          const Divider(),
          _buildSectionHeader('Operacional'),
          _buildDrawerItem(
            context,
            icon: Icons.calendar_today,
            title: 'Agendamentos',
            onTap: () {
              Navigator.pop(context);
              Navigator.push(
                context,
                MaterialPageRoute(
                    builder: (_) => const AppointmentsListPage()),
              );
            },
          ),
          _buildDrawerItem(
            context,
            icon: Icons.description,
            title: 'Guias',
            onTap: () {
              Navigator.pop(context);
              Navigator.push(
                context,
                MaterialPageRoute(builder: (_) => const GuidesListPage()),
              );
            },
          ),
          const Divider(),
          _buildSectionHeader('Administracao'),
          _buildDrawerItem(
            context,
            icon: Icons.badge,
            title: 'Perfis e Permissoes',
            onTap: () {
              Navigator.pop(context);
              Navigator.pushNamed(context, '/profiles');
            },
          ),
          _buildDrawerItem(
            context,
            icon: Icons.attach_money,
            title: 'Tabela de Precos',
            onTap: () {
              Navigator.pop(context);
              Navigator.pushNamed(context, '/prices');
            },
          ),
          _buildDrawerItem(
            context,
            icon: Icons.settings,
            title: 'Configuracoes',
            onTap: () {
              Navigator.pop(context);
              Navigator.pushNamed(context, '/settings');
            },
          ),
          const Divider(),
          _buildSectionHeader('Relatorios'),
          _buildDrawerItem(
            context,
            icon: Icons.assessment,
            title: 'Relatorios',
            onTap: () {
              Navigator.pop(context);
              Navigator.pushNamed(context, '/reports');
            },
          ),
          _buildDrawerItem(
            context,
            icon: Icons.history,
            title: 'Logs do Sistema',
            onTap: () {
              Navigator.pop(context);
              Navigator.pushNamed(context, '/logs');
            },
          ),
          const Divider(),
          _buildDrawerItem(
            context,
            icon: Icons.logout,
            title: 'Sair',
            textColor: Colors.red,
            onTap: () {
              Navigator.pop(context);
              _showLogoutDialog(context);
            },
          ),
        ],
      ),
    );
  }

  Widget _buildSectionHeader(String title) {
    return Padding(
      padding: const EdgeInsets.fromLTRB(16, 16, 16, 8),
      child: Text(
        title,
        style: const TextStyle(
          fontSize: 12,
          fontWeight: FontWeight.bold,
          color: Colors.grey,
        ),
      ),
    );
  }

  Widget _buildDrawerItem(
    BuildContext context, {
    required IconData icon,
    required String title,
    required VoidCallback onTap,
    Color? textColor,
  }) {
    return ListTile(
      leading: Icon(icon, color: textColor),
      title: Text(
        title,
        style: TextStyle(color: textColor),
      ),
      onTap: onTap,
    );
  }

  void _showLogoutDialog(BuildContext context) {
    showDialog(
      context: context,
      builder: (dialogContext) => AlertDialog(
        title: const Text('Confirmar Saida'),
        content: const Text('Deseja realmente sair do aplicativo?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(dialogContext),
            child: const Text('Cancelar'),
          ),
          TextButton(
            onPressed: () {
              Navigator.pop(dialogContext);
              context.read<AuthBloc>().add(AuthLogoutRequested());
            },
            style: TextButton.styleFrom(foregroundColor: Colors.red),
            child: const Text('Sair'),
          ),
        ],
      ),
    );
  }
}
