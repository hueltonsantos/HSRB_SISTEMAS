import 'dart:convert';
import 'package:http/http.dart' as http;
import '../../core/constants/api_endpoints.dart';
import '../../core/services/auth_service.dart';

/// Repository para operações de Configurações
class SettingsRepository {
  final http.Client _client;
  final AuthService _authService;

  SettingsRepository({
    http.Client? client,
    required AuthService authService,
  })  : _client = client ?? http.Client(),
        _authService = authService;

  /// Obter configurações do sistema
  Future<Map<String, dynamic>> getSettings() async {
    try {
      final token = await _authService.getToken();
      if (token == null) throw Exception('Token não encontrado');

      final uri = Uri.parse(
        '${ApiEndpoints.baseUrl}${ApiEndpoints.settingsGet}',
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
          return data['data']['settings'] as Map<String, dynamic>;
        } else {
          throw Exception(data['message'] ?? 'Erro ao buscar configurações');
        }
      } else {
        throw Exception('Erro ao buscar configurações: ${response.statusCode}');
      }
    } catch (e) {
      throw Exception('Erro ao buscar configurações: $e');
    }
  }

  /// Atualizar configurações
  Future<void> updateSettings(Map<String, dynamic> settings) async {
    try {
      final token = await _authService.getToken();
      if (token == null) throw Exception('Token não encontrado');

      final uri = Uri.parse(
        '${ApiEndpoints.baseUrl}${ApiEndpoints.settingsUpdate}',
      );

      final response = await _client.put(
        uri,
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
        body: json.encode(settings),
      );

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        if (data['success'] != true) {
          throw Exception(data['message'] ?? 'Erro ao atualizar configurações');
        }
      } else {
        throw Exception('Erro ao atualizar configurações: ${response.statusCode}');
      }
    } catch (e) {
      throw Exception('Erro ao atualizar configurações: $e');
    }
  }
}
