import 'package:equatable/equatable.dart';

/// Model de Paciente
class PatientModel extends Equatable {
  final int id;
  final String nome;
  final String cpf;
  final String? rg;
  final String dataNascimento;
  final String? sexo;
  final String telefone;
  final String? email;
  final String? endereco;
  final String? numero;
  final String? complemento;
  final String? bairro;
  final String? cidade;
  final String? estado;
  final String? cep;
  final String? nomeResponsavel;
  final String? telefoneResponsavel;
  final String? observacoes;
  final int? clinicaId;
  final int status;
  final String? dataCadastro;
  final String? dataAtualizacao;
  final int? idade;
  final String? cpfFormatado;

  const PatientModel({
    required this.id,
    required this.nome,
    required this.cpf,
    this.rg,
    required this.dataNascimento,
    this.sexo,
    required this.telefone,
    this.email,
    this.endereco,
    this.numero,
    this.complemento,
    this.bairro,
    this.cidade,
    this.estado,
    this.cep,
    this.nomeResponsavel,
    this.telefoneResponsavel,
    this.observacoes,
    this.clinicaId,
    required this.status,
    this.dataCadastro,
    this.dataAtualizacao,
    this.idade,
    this.cpfFormatado,
  });

  factory PatientModel.fromJson(Map<String, dynamic> json) {
    return PatientModel(
      id: json['id'] is int ? json['id'] : int.tryParse(json['id'].toString()) ?? 0,
      nome: json['nome']?.toString() ?? '',
      cpf: json['cpf']?.toString() ?? '',
      rg: json['rg']?.toString(),
      dataNascimento: json['data_nascimento']?.toString() ?? '',
      sexo: json['sexo']?.toString(),
      telefone: json['telefone']?.toString() ?? json['celular']?.toString() ?? '',
      email: json['email']?.toString(),
      endereco: json['endereco']?.toString(),
      numero: json['numero']?.toString(),
      complemento: json['complemento']?.toString(),
      bairro: json['bairro']?.toString(),
      cidade: json['cidade']?.toString(),
      estado: json['estado']?.toString(),
      cep: json['cep']?.toString(),
      nomeResponsavel: json['nome_responsavel']?.toString(),
      telefoneResponsavel: json['telefone_responsavel']?.toString(),
      observacoes: json['observacoes']?.toString(),
      clinicaId: json['clinica_id'] as int?,
      status: json['status'] is int ? json['status'] : int.tryParse(json['status'].toString()) ?? 1,
      dataCadastro: json['data_cadastro']?.toString(),
      dataAtualizacao: json['data_atualizacao']?.toString() ?? json['ultima_atualizacao']?.toString(),
      idade: json['idade'] as int?,
      cpfFormatado: json['cpf_formatado']?.toString(),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'nome': nome,
      'cpf': cpf,
      'rg': rg,
      'data_nascimento': dataNascimento,
      'sexo': sexo,
      'telefone': telefone,
      'email': email,
      'endereco': endereco,
      'numero': numero,
      'complemento': complemento,
      'bairro': bairro,
      'cidade': cidade,
      'estado': estado,
      'cep': cep,
      'nome_responsavel': nomeResponsavel,
      'telefone_responsavel': telefoneResponsavel,
      'observacoes': observacoes,
      'clinica_id': clinicaId,
      'status': status,
    };
  }

  bool get isActive => status == 1;

  @override
  List<Object?> get props => [
        id,
        nome,
        cpf,
        rg,
        dataNascimento,
        sexo,
        telefone,
        email,
        endereco,
        numero,
        complemento,
        bairro,
        cidade,
        estado,
        cep,
        nomeResponsavel,
        telefoneResponsavel,
        observacoes,
        clinicaId,
        status,
        dataCadastro,
        dataAtualizacao,
      ];
}
