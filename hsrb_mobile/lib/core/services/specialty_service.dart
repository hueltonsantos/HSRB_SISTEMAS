import '../network/api_client.dart';
import '../constants/api_endpoints.dart';
import '../../data/models/specialty_model.dart';
import 'patient_service.dart';

class SpecialtyService {
  final ApiClient _apiClient;

  SpecialtyService(this._apiClient);

  Future<PaginatedResponse<SpecialtyModel>> listSpecialties({
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
        ApiEndpoints.specialtiesList,
        queryParameters: queryParams,
      );

      if (response.statusCode == 200 && response.data['success'] == true) {
        final data = response.data['data'];
        final items = (data['items'] as List)
            .map((e) => SpecialtyModel.fromJson(e as Map<String, dynamic>))
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
        throw Exception(response.data['message'] ?? 'Erro ao listar especialidades');
      }
    } catch (e) {
      throw Exception('Erro ao listar especialidades: $e');
    }
  }

  Future<SpecialtyModel> getSpecialty(int id) async {
    try {
      final response = await _apiClient.get(
        ApiEndpoints.specialtiesGet,
        queryParameters: {'id': id},
      );

      if (response.statusCode == 200 && response.data['success'] == true) {
        return SpecialtyModel.fromJson(response.data['data']['specialty']);
      } else {
        throw Exception(response.data['message'] ?? 'Erro ao buscar especialidade');
      }
    } catch (e) {
      throw Exception('Erro ao buscar especialidade: $e');
    }
  }

  Future<int> createSpecialty(Map<String, dynamic> data) async {
    try {
      final response = await _apiClient.post(
        ApiEndpoints.specialtiesCreate,
        data: data,
      );

      if (response.statusCode == 201 && response.data['success'] == true) {
        return response.data['data']['specialty_id'] as int;
      } else {
        throw Exception(response.data['message'] ?? 'Erro ao criar especialidade');
      }
    } catch (e) {
      throw Exception('Erro ao criar especialidade: $e');
    }
  }

  Future<void> updateSpecialty(int id, Map<String, dynamic> data) async {
    try {
      final body = {'id': id, ...data};
      final response = await _apiClient.put(
        ApiEndpoints.specialtiesUpdate,
        data: body,
      );

      if (response.statusCode != 200 || response.data['success'] != true) {
        throw Exception(response.data['message'] ?? 'Erro ao atualizar especialidade');
      }
    } catch (e) {
      throw Exception('Erro ao atualizar especialidade: $e');
    }
  }

  Future<void> deleteSpecialty(int id) async {
    try {
      final response = await _apiClient.delete(
        ApiEndpoints.specialtiesDelete,
        queryParameters: {'id': id},
      );

      if (response.statusCode != 200 || response.data['success'] != true) {
        throw Exception(response.data['message'] ?? 'Erro ao deletar especialidade');
      }
    } catch (e) {
      throw Exception('Erro ao deletar especialidade: $e');
    }
  }
}
