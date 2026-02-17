import 'dart:convert';
import 'package:http/http.dart' as http;
import '../../core/constants/api_endpoints.dart';
import '../../core/services/auth_service.dart';

/// Repository para operações de Relatórios
class ReportRepository {
  final http.Client _client;
  final AuthService _authService;

  ReportRepository({
    http.Client? client,
    required AuthService authService,
  })  : _client = client ?? http.Client(),
        _authService = authService;

  /// Gerar relatório financeiro
  Future<Map<String, dynamic>> getFinancialReport({
    required String dataInicio,
    required String dataFim,
    int? clinicaId,
  }) async {
    try {
      final token = await _authService.getToken();
      if (token == null) throw Exception('Token não encontrado');

      final queryParams = {
        'data_inicio': dataInicio,
        'data_fim': dataFim,
        if (clinicaId != null) 'clinica_id': clinicaId.toString(),
      };

      final uri = Uri.parse(
        '${ApiEndpoints.baseUrl}${ApiEndpoints.reportsFinancial}',
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
          return data['data']['report'];
        } else {
          throw Exception(data['message'] ?? 'Erro ao gerar relatório');
        }
      } else {
        throw Exception('Erro ao gerar relatório: ${response.statusCode}');
      }
    } catch (e) {
      throw Exception('Erro ao gerar relatório: $e');
    }
  }

  /// Gerar relatório de agendamentos
  Future<Map<String, dynamic>> getAppointmentsReport({
    required String dataInicio,
    required String dataFim,
    int? especialidadeId,
    int? clinicaId,
  }) async {
    try {
      final token = await _authService.getToken();
      if (token == null) throw Exception('Token não encontrado');

      final queryParams = {
        'data_inicio': dataInicio,
        'data_fim': dataFim,
        if (especialidadeId != null) 'especialidade_id': especialidadeId.toString(),
        if (clinicaId != null) 'clinica_id': clinicaId.toString(),
      };

      final uri = Uri.parse(
        '${ApiEndpoints.baseUrl}${ApiEndpoints.reportsAppointments}',
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
          return data['data']['report'];
        } else {
          throw Exception(data['message'] ?? 'Erro ao gerar relatório');
        }
      } else {
        throw Exception('Erro ao gerar relatório: ${response.statusCode}');
      }
    } catch (e) {
      throw Exception('Erro ao gerar relatório: $e');
    }
  }
}
