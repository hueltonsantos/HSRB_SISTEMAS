import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '../../../core/constants/app_colors.dart';
import '../../../core/constants/app_strings.dart';
import '../../../core/services/dashboard_service.dart';
import '../../../data/models/dashboard_stats_model.dart';
import '../../../data/models/user_model.dart';
import '../../widgets/app_navigation_drawer.dart';

class DashboardPage extends StatefulWidget {
  final UserModel user;

  const DashboardPage({super.key, required this.user});

  @override
  State<DashboardPage> createState() => _DashboardPageState();
}

class _DashboardPageState extends State<DashboardPage> {
  DashboardStatsModel? _stats;
  bool _isLoading = true;
  String? _error;

  @override
  void initState() {
    super.initState();
    _loadStats();
  }

  Future<void> _loadStats() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    try {
      final dashboardService = context.read<DashboardService>();
      final stats = await dashboardService.getStats();
      setState(() {
        _stats = stats;
        _isLoading = false;
      });
    } catch (e) {
      setState(() {
        _error = e.toString().replaceAll('Exception: ', '');
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text(AppStrings.dashboard),
        actions: [
          Padding(
            padding: const EdgeInsets.only(right: 8),
            child: CircleAvatar(
              radius: 18,
              backgroundColor: AppColors.primary,
              child: Text(
                widget.user.nome.isNotEmpty
                    ? widget.user.nome[0].toUpperCase()
                    : 'U',
                style: const TextStyle(
                  color: Colors.white,
                  fontWeight: FontWeight.w700,
                ),
              ),
            ),
          ),
        ],
      ),
      drawer: AppNavigationDrawer(user: widget.user),
      body: RefreshIndicator(
        onRefresh: _loadStats,
        child: _buildBody(),
      ),
    );
  }

  Widget _buildBody() {
    if (_isLoading) {
      return const Center(child: CircularProgressIndicator());
    }

    if (_error != null) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.all(24),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Icon(Icons.error_outline, size: 64, color: AppColors.danger),
              const SizedBox(height: 16),
              Text(
                _error!,
                textAlign: TextAlign.center,
                style: const TextStyle(color: AppColors.textSecondary),
              ),
              const SizedBox(height: 16),
              ElevatedButton.icon(
                onPressed: _loadStats,
                icon: const Icon(Icons.refresh),
                label: const Text('Tentar novamente'),
              ),
            ],
          ),
        ),
      );
    }

    final stats = _stats!;

    return SingleChildScrollView(
      physics: const AlwaysScrollableScrollPhysics(),
      padding: const EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Saudacao
          Text(
            'Olá, ${widget.user.nome.split(' ').first}!',
            style: Theme.of(context).textTheme.headlineSmall?.copyWith(
                  fontWeight: FontWeight.w700,
                ),
          ),
          const SizedBox(height: 4),
          Text(
            widget.user.perfilNome ?? 'Usuário',
            style: const TextStyle(color: AppColors.textSecondary),
          ),
          const SizedBox(height: 20),

          // Cards de estatísticas
          _buildStatsCards(stats.cards),
          const SizedBox(height: 24),

          // Agendamentos recentes
          Text(
            AppStrings.recentAppointments,
            style: Theme.of(context).textTheme.titleMedium?.copyWith(
                  fontWeight: FontWeight.w700,
                ),
          ),
          const SizedBox(height: 12),
          _buildRecentAppointments(stats.recentAppointments),
        ],
      ),
    );
  }

  Widget _buildStatsCards(StatsCards cards) {
    return GridView.count(
      crossAxisCount: 2,
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      mainAxisSpacing: 12,
      crossAxisSpacing: 12,
      childAspectRatio: 1.5,
      children: [
        _buildStatCard(
          title: AppStrings.totalPatients,
          value: '${cards.totalPacientes}',
          icon: Icons.people,
          gradient: AppColors.primaryGradient,
        ),
        _buildStatCard(
          title: AppStrings.appointmentsToday,
          value: '${cards.agendamentosHoje}',
          icon: Icons.calendar_today,
          gradient: AppColors.successGradient,
        ),
        _buildStatCard(
          title: AppStrings.pendingAppointments,
          value: '${cards.agendamentosPendentes}',
          icon: Icons.pending_actions,
          gradient: AppColors.warningGradient,
        ),
        _buildStatCard(
          title: AppStrings.totalSpecialties,
          value: '${cards.totalEspecialidades}',
          icon: Icons.medical_services,
          gradient: AppColors.infoGradient,
        ),
      ],
    );
  }

  Widget _buildStatCard({
    required String title,
    required String value,
    required IconData icon,
    required LinearGradient gradient,
  }) {
    return Container(
      decoration: BoxDecoration(
        gradient: gradient,
        borderRadius: BorderRadius.circular(12),
        boxShadow: AppColors.defaultShadow,
      ),
      padding: const EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Icon(icon, color: Colors.white.withOpacity(0.8), size: 32),
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                value,
                style: const TextStyle(
                  color: Colors.white,
                  fontSize: 28,
                  fontWeight: FontWeight.w700,
                ),
              ),
              const SizedBox(height: 4),
              Text(
                title,
                style: TextStyle(
                  color: Colors.white.withOpacity(0.9),
                  fontSize: 12,
                  fontWeight: FontWeight.w600,
                ),
                maxLines: 2,
                overflow: TextOverflow.ellipsis,
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildRecentAppointments(List<RecentAppointment> appointments) {
    if (appointments.isEmpty) {
      return Card(
        child: Padding(
          padding: const EdgeInsets.all(32),
          child: Center(
            child: Column(
              children: [
                Icon(Icons.calendar_today, size: 48, color: Colors.grey[400]),
                const SizedBox(height: 12),
                Text(
                  'Nenhum agendamento recente',
                  style: TextStyle(color: Colors.grey[600]),
                ),
              ],
            ),
          ),
        ),
      );
    }

    return ListView.builder(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      itemCount: appointments.length,
      itemBuilder: (context, index) {
        final appt = appointments[index];
        final isConfirmed = appt.status == 'confirmado';
        final isPending = appt.status == 'pendente';

        return Card(
          margin: const EdgeInsets.only(bottom: 8),
          child: ListTile(
            leading: CircleAvatar(
              backgroundColor: isConfirmed
                  ? AppColors.success
                  : isPending
                      ? AppColors.warning
                      : AppColors.textSecondary,
              child: Icon(
                isConfirmed ? Icons.check : Icons.schedule,
                color: Colors.white,
                size: 20,
              ),
            ),
            title: Text(
              appt.pacienteNome ?? 'Paciente',
              style: const TextStyle(fontWeight: FontWeight.w600),
            ),
            subtitle: Text(
              '${appt.especialidadeNome ?? ''} - ${appt.dataAgendamento} ${appt.horaAgendamento}',
            ),
            trailing: Container(
              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
              decoration: BoxDecoration(
                color: isConfirmed
                    ? AppColors.success
                    : isPending
                        ? AppColors.warning
                        : AppColors.textSecondary,
                borderRadius: BorderRadius.circular(12),
              ),
              child: Text(
                appt.status,
                style: const TextStyle(
                  fontSize: 11,
                  fontWeight: FontWeight.w600,
                  color: Colors.white,
                ),
              ),
            ),
          ),
        );
      },
    );
  }
}
