import '../network/api_client.dart';
import '../constants/api_endpoints.dart';
import '../../data/models/patient_model.dart';

/// Resposta paginada
class PaginatedResponse<T> {
  final List<T> items;
  final int total;
  final int page;
  final int limit;
  final int pages;

  PaginatedResponse({
    required this.items,
    required this.total,
    required this.page,
    required this.limit,
    required this.pages,
  });
}

/// Serviço de Pacientes
/// CRUD completo de pacientes
class PatientService {
  final ApiClient _apiClient;

  PatientService(this._apiClient);

  /// Listar pacientes com paginação
  Future<PaginatedResponse<PatientModel>> listPatients({
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

      if (search != null && search.isNotEmpty) {
        queryParams['search'] = search;
      }

      if (status != null) {
        queryParams['status'] = status;
      }

      final response = await _apiClient.get(
        ApiEndpoints.patientsList,
        queryParameters: queryParams,
      );

      if (response.statusCode == 200 && response.data['success'] == true) {
        final data = response.data['data'];
        final items = (data['items'] as List)
            .map((e) => PatientModel.fromJson(e as Map<String, dynamic>))
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
        throw Exception(response.data['message'] ?? 'Erro ao listar pacientes');
      }
    } catch (e) {
      throw Exception('Erro ao listar pacientes: $e');
    }
  }

  /// Buscar paciente por ID
  Future<PatientModel> getPatient(int id) async {
    try {
      final response = await _apiClient.get(
        ApiEndpoints.patientsGet,
        queryParameters: {'id': id},
      );

      if (response.statusCode == 200 && response.data['success'] == true) {
        return PatientModel.fromJson(response.data['data']['patient']);
      } else {
        throw Exception(response.data['message'] ?? 'Erro ao buscar paciente');
      }
    } catch (e) {
      throw Exception('Erro ao buscar paciente: $e');
    }
  }

  /// Criar paciente
  Future<int> createPatient(Map<String, dynamic> patientData) async {
    try {
      final response = await _apiClient.post(
        ApiEndpoints.patientsCreate,
        data: patientData,
      );

      if (response.statusCode == 201 && response.data['success'] == true) {
        return response.data['data']['patient_id'] as int;
      } else {
        throw Exception(response.data['message'] ?? 'Erro ao criar paciente');
      }
    } catch (e) {
      throw Exception('Erro ao criar paciente: $e');
    }
  }

  /// Atualizar paciente
  Future<void> updatePatient(int id, Map<String, dynamic> patientData) async {
    try {
      final data = {'id': id, ...patientData};
      
      final response = await _apiClient.put(
        ApiEndpoints.patientsUpdate,
        data: data,
      );

      if (response.statusCode != 200 || response.data['success'] != true) {
        throw Exception(response.data['message'] ?? 'Erro ao atualizar paciente');
      }
    } catch (e) {
      throw Exception('Erro ao atualizar paciente: $e');
    }
  }

  /// Deletar paciente
  Future<void> deletePatient(int id) async {
    try {
      final response = await _apiClient.delete(
        ApiEndpoints.patientsDelete,
        queryParameters: {'id': id},
      );

      if (response.statusCode != 200 || response.data['success'] != true) {
        throw Exception(response.data['message'] ?? 'Erro ao deletar paciente');
      }
    } catch (e) {
      throw Exception('Erro ao deletar paciente: $e');
    }
  }
}
