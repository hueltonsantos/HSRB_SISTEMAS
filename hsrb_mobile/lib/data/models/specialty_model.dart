import 'package:equatable/equatable.dart';

class SpecialtyModel extends Equatable {
  final int id;
  final String nome;
  final String? descricao;
  final int status;
  final int? totalProcedimentos;
  final List<ProcedureModel>? procedimentos;

  const SpecialtyModel({
    required this.id,
    required this.nome,
    this.descricao,
    required this.status,
    this.totalProcedimentos,
    this.procedimentos,
  });

  factory SpecialtyModel.fromJson(Map<String, dynamic> json) {
    return SpecialtyModel(
      id: json['id'] is int ? json['id'] : int.tryParse(json['id'].toString()) ?? 0,
      nome: json['nome']?.toString() ?? '',
      descricao: json['descricao']?.toString(),
      status: json['status'] is int ? json['status'] : int.tryParse(json['status']?.toString() ?? '') ?? 1,
      totalProcedimentos: json['total_procedimentos'] != null ? int.tryParse(json['total_procedimentos'].toString()) : null,
      procedimentos: json['procedimentos'] != null
          ? (json['procedimentos'] as List)
              .map((e) => ProcedureModel.fromJson(e as Map<String, dynamic>))
              .toList()
          : null,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'nome': nome,
      if (descricao != null) 'descricao': descricao,
      'status': status,
    };
  }

  bool get isActive => status == 1;

  @override
  List<Object?> get props => [id, nome, descricao, status, totalProcedimentos];
}

class ProcedureModel extends Equatable {
  final int id;
  final int especialidadeId;
  final String procedimento;
  final double valorPaciente;
  final double valorRepasse;
  final int status;

  const ProcedureModel({
    required this.id,
    required this.especialidadeId,
    required this.procedimento,
    required this.valorPaciente,
    required this.valorRepasse,
    required this.status,
  });

  factory ProcedureModel.fromJson(Map<String, dynamic> json) {
    return ProcedureModel(
      id: json['id'] is int ? json['id'] : int.tryParse(json['id'].toString()) ?? 0,
      especialidadeId: json['especialidade_id'] is int ? json['especialidade_id'] : int.tryParse(json['especialidade_id']?.toString() ?? '') ?? 0,
      procedimento: json['procedimento']?.toString() ?? '',
      valorPaciente: double.tryParse(json['valor_paciente']?.toString() ?? '') ?? 0.0,
      valorRepasse: double.tryParse(json['valor_repasse']?.toString() ?? '') ?? 0.0,
      status: json['status'] is int ? json['status'] : int.tryParse(json['status']?.toString() ?? '') ?? 1,
    );
  }

  bool get isActive => status == 1;

  @override
  List<Object?> get props => [id, especialidadeId, procedimento, valorPaciente, valorRepasse, status];
}
