import 'dart:convert';
import 'package:http/http.dart' as http;
import '../models/user_model.dart';
import '../../core/constants/api_endpoints.dart';
import '../../core/services/auth_service.dart';

/// Repository para operações de Usuários
class UserRepository {
  final http.Client _client;
  final AuthService _authService;

  UserRepository({
    http.Client? client,
    required AuthService authService,
  })  : _client = client ?? http.Client(),
        _authService = authService;

  /// Listar usuários com paginação e filtros
  Future<Map<String, dynamic>> listUsers({
    int page = 1,
    int limit = 20,
    String? search,
    int? status,
    int? perfilId,
  }) async {
    try {
      final token = await _authService.getToken();
      if (token == null) throw Exception('Token não encontrado');

      final queryParams = {
        'page': page.toString(),
        'limit': limit.toString(),
        if (search != null && search.isNotEmpty) 'search': search,
        if (status != null) 'status': status.toString(),
        if (perfilId != null) 'perfil_id': perfilId.toString(),
      };

      final uri = Uri.parse(
        '${ApiEndpoints.baseUrl}${ApiEndpoints.usersList}',
      ).replace(queryParameters: queryParams);

      final response = await _client.get(
        uri,
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
      );

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        if (data['success'] == true) {
          final items = (data['data']['items'] as List)
              .map((json) => UserModel.fromJson(json))
              .toList();
          
          return {
            'items': items,
            'pagination': data['data']['pagination'],
          };
        } else {
          throw Exception(data['message'] ?? 'Erro ao listar usuários');
        }
      } else if (response.statusCode == 401) {
        throw Exception('Não autorizado');
      } else {
        throw Exception('Erro ao listar usuários: ${response.statusCode}');
      }
    } catch (e) {
      throw Exception('Erro ao listar usuários: $e');
    }
  }

  /// Obter detalhes de um usuário
  Future<UserModel> getUser(int id) async {
    try {
      final token = await _authService.getToken();
      if (token == null) throw Exception('Token não encontrado');

      final uri = Uri.parse(
        '${ApiEndpoints.baseUrl}${ApiEndpoints.usersGet}?id=$id',
      );

      final response = await _client.get(
        uri,
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
      );

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        if (data['success'] == true) {
          return UserModel.fromJson(data['data']['user']);
        } else {
          throw Exception(data['message'] ?? 'Erro ao buscar usuário');
        }
      } else if (response.statusCode == 404) {
        throw Exception('Usuário não encontrado');
      } else {
        throw Exception('Erro ao buscar usuário: ${response.statusCode}');
      }
    } catch (e) {
      throw Exception('Erro ao buscar usuário: $e');
    }
  }

  /// Criar novo usuário
  Future<int> createUser({
    required String nome,
    required String email,
    required String senha,
    required String nivelAcesso,
    int? perfilId,
    int? clinicaId,
    String? foto,
  }) async {
    try {
      final token = await _authService.getToken();
      if (token == null) throw Exception('Token não encontrado');

      final uri = Uri.parse(
        '${ApiEndpoints.baseUrl}${ApiEndpoints.usersCreate}',
      );

      final body = {
        'nome': nome,
        'email': email,
        'senha': senha,
        'nivel_acesso': nivelAcesso,
        if (perfilId != null) 'perfil_id': perfilId,
        if (clinicaId != null) 'clinica_id': clinicaId,
        if (foto != null) 'foto': foto,
      };

      final response = await _client.post(
        uri,
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
        body: json.encode(body),
      );

      if (response.statusCode == 201) {
        final data = json.decode(response.body);
        if (data['success'] == true) {
          return data['data']['id'] as int;
        } else {
          throw Exception(data['message'] ?? 'Erro ao criar usuário');
        }
      } else if (response.statusCode == 400) {
        final data = json.decode(response.body);
        throw Exception(data['message'] ?? 'Dados inválidos');
      } else {
        throw Exception('Erro ao criar usuário: ${response.statusCode}');
      }
    } catch (e) {
      throw Exception('Erro ao criar usuário: $e');
    }
  }

  /// Atualizar usuário
  Future<void> updateUser({
    required int id,
    String? nome,
    String? email,
    String? senha,
    String? nivelAcesso,
    int? perfilId,
    int? clinicaId,
    String? foto,
    int? status,
  }) async {
    try {
      final token = await _authService.getToken();
      if (token == null) throw Exception('Token não encontrado');

      final uri = Uri.parse(
        '${ApiEndpoints.baseUrl}${ApiEndpoints.usersUpdate}?id=$id',
      );

      final body = <String, dynamic>{};
      if (nome != null) body['nome'] = nome;
      if (email != null) body['email'] = email;
      if (senha != null && senha.isNotEmpty) body['senha'] = senha;
      if (nivelAcesso != null) body['nivel_acesso'] = nivelAcesso;
      if (perfilId != null) body['perfil_id'] = perfilId;
      if (clinicaId != null) body['clinica_id'] = clinicaId;
      if (foto != null) body['foto'] = foto;
      if (status != null) body['status'] = status;

      final response = await _client.put(
        uri,
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
        body: json.encode(body),
      );

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        if (data['success'] != true) {
          throw Exception(data['message'] ?? 'Erro ao atualizar usuário');
        }
      } else if (response.statusCode == 400) {
        final data = json.decode(response.body);
        throw Exception(data['message'] ?? 'Dados inválidos');
      } else {
        throw Exception('Erro ao atualizar usuário: ${response.statusCode}');
      }
    } catch (e) {
      throw Exception('Erro ao atualizar usuário: $e');
    }
  }

  /// Deletar usuário
  Future<void> deleteUser(int id) async {
    try {
      final token = await _authService.getToken();
      if (token == null) throw Exception('Token não encontrado');

      final uri = Uri.parse(
        '${ApiEndpoints.baseUrl}${ApiEndpoints.usersDelete}?id=$id',
      );

      final response = await _client.delete(
        uri,
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
      );

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        if (data['success'] != true) {
          throw Exception(data['message'] ?? 'Erro ao deletar usuário');
        }
      } else if (response.statusCode == 400) {
        final data = json.decode(response.body);
        throw Exception(data['message'] ?? 'Não é possível deletar este usuário');
      } else {
        throw Exception('Erro ao deletar usuário: ${response.statusCode}');
      }
    } catch (e) {
      throw Exception('Erro ao deletar usuário: $e');
    }
  }
}
