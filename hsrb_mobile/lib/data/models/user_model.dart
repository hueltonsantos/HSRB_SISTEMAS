import 'package:equatable/equatable.dart';

/// Model de Usuário
/// Representa os dados do usuário retornados pela API
class UserModel extends Equatable {
  final int id;
  final String nome;
  final String email;
  final String? foto;
  final String nivelAcesso;
  final int? perfilId;
  final String? perfilNome;
  final int? clinicaId;
  final String? clinicaNome;
  final List<String> permissoes;
  final int status;
  final String? dataCadastro;
  final String? dataAtualizacao;

  const UserModel({
    required this.id,
    required this.nome,
    required this.email,
    this.foto,
    required this.nivelAcesso,
    this.perfilId,
    this.perfilNome,
    this.clinicaId,
    this.clinicaNome,
    required this.permissoes,
    required this.status,
    this.dataCadastro,
    this.dataAtualizacao,
  });

  /// Criar UserModel a partir de JSON
  factory UserModel.fromJson(Map<String, dynamic> json) {
    return UserModel(
      id: json['id'] is int ? json['id'] : int.tryParse(json['id'].toString()) ?? 0,
      nome: json['nome']?.toString() ?? '',
      email: json['email']?.toString() ?? '',
      foto: json['foto']?.toString(),
      nivelAcesso: json['nivel_acesso']?.toString() ?? 'recepcionista',
      perfilId: json['perfil_id'] as int?,
      perfilNome: json['perfil_nome']?.toString(),
      clinicaId: json['clinica_id'] as int?,
      clinicaNome: json['clinica_nome']?.toString(),
      permissoes: (json['permissoes'] as List<dynamic>?)
              ?.map((e) => e.toString())
              .toList() ??
          [],
      status: json['status'] is int ? json['status'] : int.tryParse(json['status']?.toString() ?? '') ?? 1,
      dataCadastro: json['data_cadastro']?.toString(),
      dataAtualizacao: json['data_atualizacao']?.toString(),
    );
  }

  /// Converter UserModel para JSON
  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'nome': nome,
      'email': email,
      'foto': foto,
      'nivel_acesso': nivelAcesso,
      'perfil_id': perfilId,
      'perfil_nome': perfilNome,
      'clinica_id': clinicaId,
      'clinica_nome': clinicaNome,
      'permissoes': permissoes,
      'status': status,
      'data_cadastro': dataCadastro,
      'data_atualizacao': dataAtualizacao,
    };
  }

  /// Copiar com novos valores
  UserModel copyWith({
    int? id,
    String? nome,
    String? email,
    String? foto,
    String? nivelAcesso,
    int? perfilId,
    String? perfilNome,
    int? clinicaId,
    String? clinicaNome,
    List<String>? permissoes,
    int? status,
    String? dataCadastro,
    String? dataAtualizacao,
  }) {
    return UserModel(
      id: id ?? this.id,
      nome: nome ?? this.nome,
      email: email ?? this.email,
      foto: foto ?? this.foto,
      nivelAcesso: nivelAcesso ?? this.nivelAcesso,
      perfilId: perfilId ?? this.perfilId,
      perfilNome: perfilNome ?? this.perfilNome,
      clinicaId: clinicaId ?? this.clinicaId,
      clinicaNome: clinicaNome ?? this.clinicaNome,
      permissoes: permissoes ?? this.permissoes,
      status: status ?? this.status,
      dataCadastro: dataCadastro ?? this.dataCadastro,
      dataAtualizacao: dataAtualizacao ?? this.dataAtualizacao,
    );
  }

  /// Verificar se usuário tem permissão
  bool hasPermission(String permission) {
    return permissoes.contains(permission);
  }

  /// Verificar se usuário está ativo
  bool get isActive => status == 1;

  @override
  List<Object?> get props => [
        id,
        nome,
        email,
        foto,
        nivelAcesso,
        perfilId,
        perfilNome,
        clinicaId,
        clinicaNome,
        permissoes,
        status,
        dataCadastro,
        dataAtualizacao,
      ];
}
