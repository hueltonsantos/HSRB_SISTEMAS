import 'package:equatable/equatable.dart';

class ClinicModel extends Equatable {
  final int id;
  final String nome;
  final String? razaoSocial;
  final String? cnpj;
  final String? responsavel;
  final String endereco;
  final String? numero;
  final String? complemento;
  final String? bairro;
  final String cidade;
  final String estado;
  final String? cep;
  final String telefone;
  final String? celular;
  final String? email;
  final String? site;
  final int status;
  final String? dataCadastro;
  final String? ultimaAtualizacao;
  final List<ClinicSpecialty>? especialidades;

  const ClinicModel({
    required this.id,
    required this.nome,
    this.razaoSocial,
    this.cnpj,
    this.responsavel,
    required this.endereco,
    this.numero,
    this.complemento,
    this.bairro,
    required this.cidade,
    required this.estado,
    this.cep,
    required this.telefone,
    this.celular,
    this.email,
    this.site,
    required this.status,
    this.dataCadastro,
    this.ultimaAtualizacao,
    this.especialidades,
  });

  factory ClinicModel.fromJson(Map<String, dynamic> json) {
    return ClinicModel(
      id: json['id'] is int ? json['id'] : int.tryParse(json['id'].toString()) ?? 0,
      nome: json['nome']?.toString() ?? '',
      razaoSocial: json['razao_social']?.toString(),
      cnpj: json['cnpj']?.toString(),
      responsavel: json['responsavel']?.toString(),
      endereco: json['endereco']?.toString() ?? '',
      numero: json['numero']?.toString(),
      complemento: json['complemento']?.toString(),
      bairro: json['bairro']?.toString(),
      cidade: json['cidade']?.toString() ?? '',
      estado: json['estado']?.toString() ?? '',
      cep: json['cep']?.toString(),
      telefone: json['telefone']?.toString() ?? '',
      celular: json['celular']?.toString(),
      email: json['email']?.toString(),
      site: json['site']?.toString(),
      status: json['status'] is int ? json['status'] : int.tryParse(json['status']?.toString() ?? '') ?? 1,
      dataCadastro: json['data_cadastro']?.toString(),
      ultimaAtualizacao: json['ultima_atualizacao']?.toString(),
      especialidades: json['especialidades'] != null
          ? (json['especialidades'] as List)
              .map((e) => ClinicSpecialty.fromJson(e as Map<String, dynamic>))
              .toList()
          : null,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'nome': nome,
      if (razaoSocial != null) 'razao_social': razaoSocial,
      if (cnpj != null) 'cnpj': cnpj,
      if (responsavel != null) 'responsavel': responsavel,
      'endereco': endereco,
      if (numero != null) 'numero': numero,
      if (complemento != null) 'complemento': complemento,
      if (bairro != null) 'bairro': bairro,
      'cidade': cidade,
      'estado': estado,
      if (cep != null) 'cep': cep,
      'telefone': telefone,
      if (celular != null) 'celular': celular,
      if (email != null) 'email': email,
      if (site != null) 'site': site,
    };
  }

  bool get isActive => status == 1;

  String get enderecoCompleto {
    final parts = <String>[];
    if (endereco.isNotEmpty) {
      parts.add(endereco);
      if (numero != null && numero!.isNotEmpty) parts.add(', $numero');
    }
    if (bairro != null && bairro!.isNotEmpty) parts.add(' - $bairro');
    if (cidade.isNotEmpty) parts.add(', $cidade');
    if (estado.isNotEmpty) parts.add(' - $estado');
    return parts.join();
  }

  @override
  List<Object?> get props => [id, nome, cnpj, cidade, estado, status];
}

class ClinicSpecialty extends Equatable {
  final int id;
  final String nome;

  const ClinicSpecialty({required this.id, required this.nome});

  factory ClinicSpecialty.fromJson(Map<String, dynamic> json) {
    return ClinicSpecialty(
      id: json['id'] is int ? json['id'] : int.tryParse(json['id'].toString()) ?? 0,
      nome: json['nome']?.toString() ?? '',
    );
  }

  @override
  List<Object?> get props => [id, nome];
}
