import 'dart:convert';
import 'package:http/http.dart' as http;
import '../../core/constants/api_endpoints.dart';
import '../../core/services/auth_service.dart';

/// Repository para operações de Especialidades
class SpecialtyRepository {
  final http.Client _client;
  final AuthService _authService;

  SpecialtyRepository({
    http.Client? client,
    required AuthService authService,
  })  : _client = client ?? http.Client(),
        _authService = authService;

  /// Listar especialidades (simples, sem paginação complexa se não precisar)
  Future<List<dynamic>> listSpecialties() async {
    try {
      final token = await _authService.getToken();
      if (token == null) throw Exception('Token não encontrado');

      // Reusing list endpoint, assuming it might support fetching all if no limit or large limit
      final uri = Uri.parse(
        '${ApiEndpoints.baseUrl}${ApiEndpoints.specialtiesList}?limit=100',
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
          return data['data']['items'] as List;
        } else {
          throw Exception(data['message'] ?? 'Erro ao listar especialidades');
        }
      } else {
        throw Exception('Erro ao listar especialidades: ${response.statusCode}');
      }
    } catch (e) {
      throw Exception('Erro ao listar especialidades: $e');
    }
  }
}
