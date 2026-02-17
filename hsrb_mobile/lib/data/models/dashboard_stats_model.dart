import 'package:equatable/equatable.dart';

/// Model de Estatísticas do Dashboard
class DashboardStatsModel extends Equatable {
  final StatsCards cards;
  final StatsCharts charts;
  final List<RecentAppointment> recentAppointments;

  const DashboardStatsModel({
    required this.cards,
    required this.charts,
    required this.recentAppointments,
  });

  factory DashboardStatsModel.fromJson(Map<String, dynamic> json) {
    final stats = json['stats'] as Map<String, dynamic>;
    return DashboardStatsModel(
      cards: StatsCards.fromJson(stats['cards'] as Map<String, dynamic>),
      charts: StatsCharts.fromJson(stats['charts'] as Map<String, dynamic>),
      recentAppointments: (stats['recent_appointments'] as List<dynamic>)
          .map((e) => RecentAppointment.fromJson(e as Map<String, dynamic>))
          .toList(),
    );
  }

  @override
  List<Object?> get props => [cards, charts, recentAppointments];
}

class StatsCards extends Equatable {
  final int totalPacientes;
  final int totalAgendamentos;
  final int agendamentosHoje;
  final int agendamentosPendentes;
  final int totalClinicas;
  final int totalEspecialidades;

  const StatsCards({
    required this.totalPacientes,
    required this.totalAgendamentos,
    required this.agendamentosHoje,
    required this.agendamentosPendentes,
    required this.totalClinicas,
    required this.totalEspecialidades,
  });

  factory StatsCards.fromJson(Map<String, dynamic> json) {
    return StatsCards(
      totalPacientes: int.tryParse(json['total_pacientes']?.toString() ?? '') ?? 0,
      totalAgendamentos: int.tryParse(json['total_agendamentos']?.toString() ?? '') ?? 0,
      agendamentosHoje: int.tryParse(json['agendamentos_hoje']?.toString() ?? '') ?? 0,
      agendamentosPendentes: int.tryParse(json['agendamentos_pendentes']?.toString() ?? '') ?? 0,
      totalClinicas: int.tryParse(json['total_clinicas']?.toString() ?? '') ?? 0,
      totalEspecialidades: int.tryParse(json['total_especialidades']?.toString() ?? '') ?? 0,
    );
  }

  @override
  List<Object?> get props => [
        totalPacientes,
        totalAgendamentos,
        agendamentosHoje,
        agendamentosPendentes,
        totalClinicas,
        totalEspecialidades,
      ];
}

class StatsCharts extends Equatable {
  final List<MonthlyData> agendamentosPorMes;
  final List<MonthlyData> pacientesPorMes;

  const StatsCharts({
    required this.agendamentosPorMes,
    required this.pacientesPorMes,
  });

  factory StatsCharts.fromJson(Map<String, dynamic> json) {
    return StatsCharts(
      agendamentosPorMes: (json['agendamentos_por_mes'] as List<dynamic>)
          .map((e) => MonthlyData.fromJson(e as Map<String, dynamic>))
          .toList(),
      pacientesPorMes: (json['pacientes_por_mes'] as List<dynamic>)
          .map((e) => MonthlyData.fromJson(e as Map<String, dynamic>))
          .toList(),
    );
  }

  @override
  List<Object?> get props => [agendamentosPorMes, pacientesPorMes];
}

class MonthlyData extends Equatable {
  final String mes;
  final int total;

  const MonthlyData({
    required this.mes,
    required this.total,
  });

  factory MonthlyData.fromJson(Map<String, dynamic> json) {
    return MonthlyData(
      mes: json['mes']?.toString() ?? '',
      total: int.tryParse(json['total']?.toString() ?? '') ?? 0,
    );
  }

  @override
  List<Object?> get props => [mes, total];
}

class RecentAppointment extends Equatable {
  final int id;
  final String dataAgendamento;
  final String horaAgendamento;
  final String status;
  final String? pacienteNome;
  final String? especialidadeNome;
  final String? clinicaNome;

  const RecentAppointment({
    required this.id,
    required this.dataAgendamento,
    required this.horaAgendamento,
    required this.status,
    this.pacienteNome,
    this.especialidadeNome,
    this.clinicaNome,
  });

  factory RecentAppointment.fromJson(Map<String, dynamic> json) {
    return RecentAppointment(
      id: json['id'] is int ? json['id'] : int.tryParse(json['id'].toString()) ?? 0,
      dataAgendamento: json['data_agendamento']?.toString() ?? '',
      horaAgendamento: json['hora_agendamento']?.toString() ?? '',
      status: json['status']?.toString() ?? 'agendado',
      pacienteNome: json['paciente_nome']?.toString(),
      especialidadeNome: json['especialidade_nome']?.toString(),
      clinicaNome: json['clinica_nome']?.toString(),
    );
  }

  @override
  List<Object?> get props => [
        id,
        dataAgendamento,
        horaAgendamento,
        status,
        pacienteNome,
        especialidadeNome,
        clinicaNome,
      ];
}
