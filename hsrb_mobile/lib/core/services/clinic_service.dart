import '../network/api_client.dart';
import '../constants/api_endpoints.dart';
import '../../data/models/clinic_model.dart';
import 'patient_service.dart';

class ClinicService {
  final ApiClient _apiClient;

  ClinicService(this._apiClient);

  Future<PaginatedResponse<ClinicModel>> listClinics({
    int page = 1,
    int limit = 20,
    String? search,
    int? status,
  }) async {
    try {
      final queryParams = <String, dynamic>{
        'page': page,
        'limit': limit,
      };
      if (search != null && search.isNotEmpty) queryParams['search'] = search;
      if (status != null) queryParams['status'] = status;

      final response = await _apiClient.get(
        ApiEndpoints.clinicsList,
        queryParameters: queryParams,
      );

      if (response.statusCode == 200 && response.data['success'] == true) {
        final data = response.data['data'];
        final items = (data['items'] as List)
            .map((e) => ClinicModel.fromJson(e as Map<String, dynamic>))
            .toList();
        final pagination = data['pagination'] as Map<String, dynamic>;

        return PaginatedResponse(
          items: items,
          total: pagination['total'] as int,
          page: pagination['page'] as int,
          limit: pagination['limit'] as int,
          pages: pagination['pages'] as int,
        );
      } else {
        throw Exception(response.data['message'] ?? 'Erro ao listar clinicas');
      }
    } catch (e) {
      throw Exception('Erro ao listar clinicas: $e');
    }
  }

  Future<ClinicModel> getClinic(int id) async {
    try {
      final response = await _apiClient.get(
        ApiEndpoints.clinicsGet,
        queryParameters: {'id': id},
      );

      if (response.statusCode == 200 && response.data['success'] == true) {
        return ClinicModel.fromJson(response.data['data']['clinic']);
      } else {
        throw Exception(response.data['message'] ?? 'Erro ao buscar clinica');
      }
    } catch (e) {
      throw Exception('Erro ao buscar clinica: $e');
    }
  }

  Future<int> createClinic(Map<String, dynamic> data) async {
    try {
      final response = await _apiClient.post(
        ApiEndpoints.clinicsCreate,
        data: data,
      );

      if (response.statusCode == 201 && response.data['success'] == true) {
        return response.data['data']['clinic_id'] as int;
      } else {
        throw Exception(response.data['message'] ?? 'Erro ao criar clinica');
      }
    } catch (e) {
      throw Exception('Erro ao criar clinica: $e');
    }
  }

  Future<void> updateClinic(int id, Map<String, dynamic> data) async {
    try {
      final body = {'id': id, ...data};
      final response = await _apiClient.put(
        ApiEndpoints.clinicsUpdate,
        data: body,
      );

      if (response.statusCode != 200 || response.data['success'] != true) {
        throw Exception(response.data['message'] ?? 'Erro ao atualizar clinica');
      }
    } catch (e) {
      throw Exception('Erro ao atualizar clinica: $e');
    }
  }

  Future<void> deleteClinic(int id) async {
    try {
      final response = await _apiClient.delete(
        ApiEndpoints.clinicsDelete,
        queryParameters: {'id': id},
      );

      if (response.statusCode != 200 || response.data['success'] != true) {
        throw Exception(response.data['message'] ?? 'Erro ao deletar clinica');
      }
    } catch (e) {
      throw Exception('Erro ao deletar clinica: $e');
    }
  }
}
