import 'package:equatable/equatable.dart';

class AppointmentModel extends Equatable {
  final int id;
  final int pacienteId;
  final int clinicaId;
  final int especialidadeId;
  final int? procedimentoId;
  final String dataConsulta;
  final String horaConsulta;
  final String status;
  final String? observacoes;
  final String? pacienteNome;
  final String? pacienteCpf;
  final String? clinicaNome;
  final String? especialidadeNome;
  final String? procedimentoNome;
  final String? dataCriacao;

  const AppointmentModel({
    required this.id,
    required this.pacienteId,
    required this.clinicaId,
    required this.especialidadeId,
    this.procedimentoId,
    required this.dataConsulta,
    required this.horaConsulta,
    required this.status,
    this.observacoes,
    this.pacienteNome,
    this.pacienteCpf,
    this.clinicaNome,
    this.especialidadeNome,
    this.procedimentoNome,
    this.dataCriacao,
  });

  factory AppointmentModel.fromJson(Map<String, dynamic> json) {
    return AppointmentModel(
      id: json['id'] is int ? json['id'] : int.tryParse(json['id'].toString()) ?? 0,
      pacienteId: json['paciente_id'] is int ? json['paciente_id'] : int.tryParse(json['paciente_id']?.toString() ?? '') ?? 0,
      clinicaId: json['clinica_id'] is int ? json['clinica_id'] : int.tryParse(json['clinica_id']?.toString() ?? '') ?? 0,
      especialidadeId: json['especialidade_id'] is int ? json['especialidade_id'] : int.tryParse(json['especialidade_id']?.toString() ?? '') ?? 0,
      procedimentoId: json['procedimento_id'] != null ? (json['procedimento_id'] is int ? json['procedimento_id'] : int.tryParse(json['procedimento_id'].toString())) : null,
      dataConsulta: json['data_consulta']?.toString() ?? '',
      horaConsulta: json['hora_consulta']?.toString() ?? '',
      status: json['status']?.toString() ?? json['status_agendamento']?.toString() ?? 'agendado',
      observacoes: json['observacoes']?.toString(),
      pacienteNome: json['paciente_nome']?.toString(),
      pacienteCpf: json['paciente_cpf']?.toString(),
      clinicaNome: json['clinica_nome']?.toString(),
      especialidadeNome: json['especialidade_nome']?.toString(),
      procedimentoNome: json['procedimento_nome']?.toString(),
      dataCriacao: json['data_criacao']?.toString() ?? json['data_agendamento']?.toString(),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'paciente_id': pacienteId,
      'clinica_id': clinicaId,
      'especialidade_id': especialidadeId,
      if (procedimentoId != null) 'procedimento_id': procedimentoId,
      'data_consulta': dataConsulta,
      'hora_consulta': horaConsulta,
      if (observacoes != null) 'observacoes': observacoes,
    };
  }

  bool get isAgendado => status == 'agendado';
  bool get isConfirmado => status == 'confirmado';
  bool get isRealizado => status == 'realizado';
  bool get isCancelado => status == 'cancelado';

  String get statusLabel {
    switch (status) {
      case 'agendado':
        return 'Agendado';
      case 'confirmado':
        return 'Confirmado';
      case 'realizado':
        return 'Realizado';
      case 'cancelado':
        return 'Cancelado';
      default:
        return status;
    }
  }

  @override
  List<Object?> get props => [
        id,
        pacienteId,
        clinicaId,
        especialidadeId,
        dataConsulta,
        horaConsulta,
        status,
      ];
}
