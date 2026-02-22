import 'dart:convert';
import 'package:http/http.dart' as http;
import '../../core/constants/api_endpoints.dart';
import '../../core/services/auth_service.dart';

/// Repository para operações de Perfis
class ProfileRepository {
  final http.Client _client;
  final AuthService _authService;

  ProfileRepository({
    http.Client? client,
    required AuthService authService,
  })  : _client = client ?? http.Client(),
        _authService = authService;

  /// Listar perfis com paginação
  Future<Map<String, dynamic>> listProfiles({
    int page = 1,
    int limit = 20,
    String? search,
    int? status,
  }) async {
    try {
      final token = await _authService.getToken();
      if (token == null) throw Exception('Token não encontrado');

      final queryParams = {
        'page': page.toString(),
        'limit': limit.toString(),
        if (search != null && search.isNotEmpty) 'search': search,
        if (status != null) 'status': status.toString(),
      };

      final uri = Uri.parse(
        '${ApiEndpoints.baseUrl}${ApiEndpoints.profilesList}',
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
          return {
            'items': data['data']['items'] as List,
            'pagination': data['data']['pagination'],
          };
        } else {
          throw Exception(data['message'] ?? 'Erro ao listar perfis');
        }
      } else {
        throw Exception('Erro ao listar perfis: ${response.statusCode}');
      }
    } catch (e) {
      throw Exception('Erro ao listar perfis: $e');
    }
  }

  /// Obter detalhes de um perfil
  Future<Map<String, dynamic>> getProfile(int id) async {
    try {
      final token = await _authService.getToken();
      if (token == null) throw Exception('Token não encontrado');

      final uri = Uri.parse(
        '${ApiEndpoints.baseUrl}${ApiEndpoints.profilesGet}?id=$id',
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
          return data['data']['profile'];
        } else {
          throw Exception(data['message'] ?? 'Erro ao buscar perfil');
        }
      } else {
        throw Exception('Erro ao buscar perfil: ${response.statusCode}');
      }
    } catch (e) {
      throw Exception('Erro ao buscar perfil: $e');
    }
  }

  /// Criar novo perfil
  Future<int> createProfile({
    required String nome,
    String? descricao,
    List<int>? permissoes,
  }) async {
    try {
      final token = await _authService.getToken();
      if (token == null) throw Exception('Token não encontrado');

      final uri = Uri.parse(
        '${ApiEndpoints.baseUrl}${ApiEndpoints.profilesCreate}',
      );

      final body = {
        'nome': nome,
        if (descricao != null) 'descricao': descricao,
        if (permissoes != null) 'permissoes': permissoes,
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
          throw Exception(data['message'] ?? 'Erro ao criar perfil');
        }
      } else {
        throw Exception('Erro ao criar perfil: ${response.statusCode}');
      }
    } catch (e) {
      throw Exception('Erro ao criar perfil: $e');
    }
  }

  /// Atualizar perfil
  Future<void> updateProfile({
    required int id,
    String? nome,
    String? descricao,
    List<int>? permissoes,
    int? status,
  }) async {
    try {
      final token = await _authService.getToken();
      if (token == null) throw Exception('Token não encontrado');

      final uri = Uri.parse(
        '${ApiEndpoints.baseUrl}${ApiEndpoints.profilesUpdate}?id=$id',
      );

      final body = <String, dynamic>{};
      if (nome != null) body['nome'] = nome;
      if (descricao != null) body['descricao'] = descricao;
      if (permissoes != null) body['permissoes'] = permissoes;
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
          throw Exception(data['message'] ?? 'Erro ao atualizar perfil');
        }
      } else {
        throw Exception('Erro ao atualizar perfil: ${response.statusCode}');
      }
    } catch (e) {
      throw Exception('Erro ao atualizar perfil: $e');
    }
  }

  /// Deletar perfil
  Future<void> deleteProfile(int id) async {
    try {
      final token = await _authService.getToken();
      if (token == null) throw Exception('Token não encontrado');

      final uri = Uri.parse(
        '${ApiEndpoints.baseUrl}${ApiEndpoints.profilesDelete}?id=$id',
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
          throw Exception(data['message'] ?? 'Erro ao deletar perfil');
        }
      } else {
        throw Exception('Erro ao deletar perfil: ${response.statusCode}');
      }
    } catch (e) {
      throw Exception('Erro ao deletar perfil: $e');
    }
  }

  /// Listar todas as permissões disponíveis
  Future<List<dynamic>> listPermissions() async {
    try {
      final token = await _authService.getToken();
      if (token == null) throw Exception('Token não encontrado');

      final uri = Uri.parse(
        '${ApiEndpoints.baseUrl}${ApiEndpoints.profilesPermissions}',
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
          if (data['data'] != null && data['data']['items'] != null) {
             return data['data']['items'] as List;
          }
          // Fallback se a estrutura for diferente
          if (data['data'] is List) return data['data'];
          // Fallback se items estiver na raiz (pouco provável baseado em ApiResponse::success)
           return [];
        } else {
          throw Exception(data['message'] ?? 'Erro ao listar permissões');
        }
      } else {
        throw Exception('Erro ao listar permissões: ${response.statusCode}');
      }
    } catch (e) {
      throw Exception('Erro ao listar permissões: $e');
    }
  }
}
