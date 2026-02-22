import 'package:equatable/equatable.dart';

class GuideModel extends Equatable {
  final int id;
  final String? codigo;
  final int pacienteId;
  final int procedimentoId;
  final String dataAgendamento;
  final String? horarioAgendamento;
  final String? observacoes;
  final String status;
  final String? dataEmissao;
  final String? pacienteNome;
  final String? pacienteCpf;
  final String? procedimentoNome;
  final String? especialidadeNome;
  final double? valor;

  const GuideModel({
    required this.id,
    this.codigo,
    required this.pacienteId,
    required this.procedimentoId,
    required this.dataAgendamento,
    this.horarioAgendamento,
    this.observacoes,
    required this.status,
    this.dataEmissao,
    this.pacienteNome,
    this.pacienteCpf,
    this.procedimentoNome,
    this.especialidadeNome,
    this.valor,
  });

  factory GuideModel.fromJson(Map<String, dynamic> json) {
    return GuideModel(
      id: json['id'] is int ? json['id'] : int.tryParse(json['id'].toString()) ?? 0,
      codigo: json['codigo']?.toString(),
      pacienteId: json['paciente_id'] is int ? json['paciente_id'] : int.tryParse(json['paciente_id']?.toString() ?? '') ?? 0,
      procedimentoId: json['procedimento_id'] is int ? json['procedimento_id'] : int.tryParse(json['procedimento_id']?.toString() ?? '') ?? 0,
      dataAgendamento: json['data_agendamento']?.toString() ?? '',
      horarioAgendamento: json['horario_agendamento']?.toString(),
      observacoes: json['observacoes']?.toString(),
      status: json['status']?.toString() ?? 'agendado',
      dataEmissao: json['data_emissao']?.toString(),
      pacienteNome: json['paciente_nome']?.toString(),
      pacienteCpf: json['paciente_cpf']?.toString(),
      procedimentoNome: json['procedimento_nome']?.toString(),
      especialidadeNome: json['especialidade_nome']?.toString(),
      valor: json['valor'] != null
          ? double.tryParse(json['valor'].toString())
          : (json['procedimento_valor'] != null ? double.tryParse(json['procedimento_valor'].toString()) : null),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'paciente_id': pacienteId,
      'procedimento_id': procedimentoId,
      'data_agendamento': dataAgendamento,
      if (horarioAgendamento != null) 'horario_agendamento': horarioAgendamento,
      if (observacoes != null) 'observacoes': observacoes,
    };
  }

  bool get isAgendado => status == 'agendado';
  bool get isRealizado => status == 'realizado';
  bool get isCancelado => status == 'cancelado';

  String get statusLabel {
    switch (status) {
      case 'agendado':
        return 'Agendado';
      case 'realizado':
        return 'Realizado';
      case 'cancelado':
        return 'Cancelado';
      default:
        return status;
    }
  }

  @override
  List<Object?> get props => [id, codigo, pacienteId, procedimentoId, status];
}
