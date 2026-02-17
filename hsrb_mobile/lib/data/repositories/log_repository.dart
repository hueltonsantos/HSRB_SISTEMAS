import 'dart:convert';
import 'package:http/http.dart' as http;
import '../../core/constants/api_endpoints.dart';
import '../../core/services/auth_service.dart';

/// Repository para operações de Logs
class LogRepository {
  final http.Client _client;
  final AuthService _authService;

  LogRepository({
    http.Client? client,
    required AuthService authService,
  })  : _client = client ?? http.Client(),
        _authService = authService;

  /// Listar logs com paginação e filtros
  Future<Map<String, dynamic>> listLogs({
    int page = 1,
    int limit = 20,
    String? modulo,
    String? acao,
    int? usuarioId,
  }) async {
    try {
      final token = await _authService.getToken();
      if (token == null) throw Exception('Token não encontrado');

      final queryParams = {
        'page': page.toString(),
        'limit': limit.toString(),
        if (modulo != null && modulo.isNotEmpty) 'modulo': modulo,
        if (acao != null && acao.isNotEmpty) 'acao': acao,
        if (usuarioId != null) 'usuario_id': usuarioId.toString(),
      };

      final uri = Uri.parse(
        '${ApiEndpoints.baseUrl}${ApiEndpoints.logsList}',
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
          throw Exception(data['message'] ?? 'Erro ao listar logs');
        }
      } else {
        throw Exception('Erro ao listar logs: ${response.statusCode}');
      }
    } catch (e) {
      throw Exception('Erro ao listar logs: $e');
    }
  }

  /// Obter detalhes de um log
  Future<Map<String, dynamic>> getLog(int id) async {
    try {
      final token = await _authService.getToken();
      if (token == null) throw Exception('Token não encontrado');

      final uri = Uri.parse(
        '${ApiEndpoints.baseUrl}${ApiEndpoints.logsGet}?id=$id',
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
          return data['data']['log'];
        } else {
          throw Exception(data['message'] ?? 'Erro ao buscar log');
        }
      } else {
        throw Exception('Erro ao buscar log: ${response.statusCode}');
      }
    } catch (e) {
      throw Exception('Erro ao buscar log: $e');
    }
  }
}
