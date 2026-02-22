import 'dart:convert';
import 'package:http/http.dart' as http;
import '../../core/constants/api_endpoints.dart';
import '../../core/services/auth_service.dart';

/// Repository para operações de Preços
class PriceRepository {
  final http.Client _client;
  final AuthService _authService;

  PriceRepository({
    http.Client? client,
    required AuthService authService,
  })  : _client = client ?? http.Client(),
        _authService = authService;

  /// Listar preços com paginação
  Future<Map<String, dynamic>> listPrices({
    int page = 1,
    int limit = 20,
    String? search,
    int? especialidadeId,
  }) async {
    try {
      final token = await _authService.getToken();
      if (token == null) throw Exception('Token não encontrado');

      final queryParams = {
        'page': page.toString(),
        'limit': limit.toString(),
        if (search != null && search.isNotEmpty) 'search': search,
        if (especialidadeId != null) 'especialidade_id': especialidadeId.toString(),
      };

      final uri = Uri.parse(
        '${ApiEndpoints.baseUrl}${ApiEndpoints.pricesList}',
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
          throw Exception(data['message'] ?? 'Erro ao listar preços');
        }
      } else {
        throw Exception('Erro ao listar preços: ${response.statusCode}');
      }
    } catch (e) {
      throw Exception('Erro ao listar preços: $e');
    }
  }

  /// Obter detalhes de um preço
  Future<Map<String, dynamic>> getPrice(int id) async {
    try {
      final token = await _authService.getToken();
      if (token == null) throw Exception('Token não encontrado');

      final uri = Uri.parse(
        '${ApiEndpoints.baseUrl}${ApiEndpoints.pricesGet}?id=$id',
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
          return data['data']['price'];
        } else {
          throw Exception(data['message'] ?? 'Erro ao buscar preço');
        }
      } else {
        throw Exception('Erro ao buscar preço: ${response.statusCode}');
      }
    } catch (e) {
      throw Exception('Erro ao buscar preço: $e');
    }
  }

  /// Atualizar preço
  Future<void> updatePrice({
    required int id,
    double? valorPaciente,
    double? valorRepasse,
    int? status,
  }) async {
    try {
      final token = await _authService.getToken();
      if (token == null) throw Exception('Token não encontrado');

      final uri = Uri.parse(
        '${ApiEndpoints.baseUrl}${ApiEndpoints.pricesUpdate}?id=$id',
      );

      final body = <String, dynamic>{};
      if (valorPaciente != null) body['valor_paciente'] = valorPaciente;
      if (valorRepasse != null) body['valor_repasse'] = valorRepasse;
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
          throw Exception(data['message'] ?? 'Erro ao atualizar preço');
        }
      } else {
        throw Exception('Erro ao atualizar preço: ${response.statusCode}');
      }
    } catch (e) {
      throw Exception('Erro ao atualizar preço: $e');
    }
  }
}
