import '../network/api_client.dart';
import '../constants/api_endpoints.dart';
import '../../data/models/guide_model.dart';
import 'patient_service.dart';

class GuideService {
  final ApiClient _apiClient;

  GuideService(this._apiClient);

  Future<PaginatedResponse<GuideModel>> listGuides({
    int page = 1,
    int limit = 20,
    String? search,
    String? status,
  }) async {
    try {
      final queryParams = <String, dynamic>{
        'page': page,
        'limit': limit,
      };
      if (search != null && search.isNotEmpty) queryParams['search'] = search;
      if (status != null && status.isNotEmpty) queryParams['status'] = status;

      final response = await _apiClient.get(
        ApiEndpoints.guidesList,
        queryParameters: queryParams,
      );

      if (response.statusCode == 200 && response.data['success'] == true) {
        final data = response.data['data'];
        final items = (data['items'] as List)
            .map((e) => GuideModel.fromJson(e as Map<String, dynamic>))
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
        throw Exception(response.data['message'] ?? 'Erro ao listar guias');
      }
    } catch (e) {
      throw Exception('Erro ao listar guias: $e');
    }
  }

  Future<GuideModel> getGuide(int id) async {
    try {
      final response = await _apiClient.get(
        ApiEndpoints.guidesGet,
        queryParameters: {'id': id},
      );

      if (response.statusCode == 200 && response.data['success'] == true) {
        return GuideModel.fromJson(response.data['data']['guide']);
      } else {
        throw Exception(response.data['message'] ?? 'Erro ao buscar guia');
      }
    } catch (e) {
      throw Exception('Erro ao buscar guia: $e');
    }
  }

  Future<int> createGuide(Map<String, dynamic> data) async {
    try {
      final response = await _apiClient.post(
        ApiEndpoints.guidesCreate,
        data: data,
      );

      if (response.statusCode == 201 && response.data['success'] == true) {
        return response.data['data']['guide_id'] as int;
      } else {
        throw Exception(response.data['message'] ?? 'Erro ao criar guia');
      }
    } catch (e) {
      throw Exception('Erro ao criar guia: $e');
    }
  }

  Future<Map<String, dynamic>> getGuidePdfData(int id) async {
    try {
      final response = await _apiClient.get(
        ApiEndpoints.guidesPdf,
        queryParameters: {'id': id},
      );

      if (response.statusCode == 200 && response.data['success'] == true) {
        return response.data['data'] as Map<String, dynamic>;
      } else {
        throw Exception(response.data['message'] ?? 'Erro ao buscar dados da guia');
      }
    } catch (e) {
      throw Exception('Erro ao buscar dados da guia: $e');
    }
  }
}
