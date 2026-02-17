import '../network/api_client.dart';
import '../constants/api_endpoints.dart';
import '../../data/models/appointment_model.dart';
import 'patient_service.dart';

class AppointmentService {
  final ApiClient _apiClient;

  AppointmentService(this._apiClient);

  Future<PaginatedResponse<AppointmentModel>> listAppointments({
    int page = 1,
    int limit = 20,
    String? search,
    String? status,
    String? dataInicio,
    String? dataFim,
    int? clinicaId,
    int? especialidadeId,
  }) async {
    try {
      final queryParams = <String, dynamic>{
        'page': page,
        'limit': limit,
      };
      if (search != null && search.isNotEmpty) queryParams['search'] = search;
      if (status != null && status.isNotEmpty) queryParams['status'] = status;
      if (dataInicio != null) queryParams['data_inicio'] = dataInicio;
      if (dataFim != null) queryParams['data_fim'] = dataFim;
      if (clinicaId != null) queryParams['clinica_id'] = clinicaId;
      if (especialidadeId != null) queryParams['especialidade_id'] = especialidadeId;

      final response = await _apiClient.get(
        ApiEndpoints.appointmentsList,
        queryParameters: queryParams,
      );

      if (response.statusCode == 200 && response.data['success'] == true) {
        final data = response.data['data'];
        final items = (data['items'] as List)
            .map((e) => AppointmentModel.fromJson(e as Map<String, dynamic>))
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
        throw Exception(response.data['message'] ?? 'Erro ao listar agendamentos');
      }
    } catch (e) {
      throw Exception('Erro ao listar agendamentos: $e');
    }
  }

  Future<AppointmentModel> getAppointment(int id) async {
    try {
      final response = await _apiClient.get(
        ApiEndpoints.appointmentsGet,
        queryParameters: {'id': id},
      );

      if (response.statusCode == 200 && response.data['success'] == true) {
        return AppointmentModel.fromJson(response.data['data']['appointment']);
      } else {
        throw Exception(response.data['message'] ?? 'Erro ao buscar agendamento');
      }
    } catch (e) {
      throw Exception('Erro ao buscar agendamento: $e');
    }
  }

  Future<int> createAppointment(Map<String, dynamic> data) async {
    try {
      final response = await _apiClient.post(
        ApiEndpoints.appointmentsCreate,
        data: data,
      );

      if (response.statusCode == 201 && response.data['success'] == true) {
        return response.data['data']['appointment_id'] as int;
      } else {
        throw Exception(response.data['message'] ?? 'Erro ao criar agendamento');
      }
    } catch (e) {
      throw Exception('Erro ao criar agendamento: $e');
    }
  }

  Future<void> updateAppointment(int id, Map<String, dynamic> data) async {
    try {
      final body = {'id': id, ...data};
      final response = await _apiClient.put(
        ApiEndpoints.appointmentsUpdate,
        data: body,
      );

      if (response.statusCode != 200 || response.data['success'] != true) {
        throw Exception(response.data['message'] ?? 'Erro ao atualizar agendamento');
      }
    } catch (e) {
      throw Exception('Erro ao atualizar agendamento: $e');
    }
  }

  Future<void> deleteAppointment(int id) async {
    try {
      final response = await _apiClient.delete(
        ApiEndpoints.appointmentsDelete,
        queryParameters: {'id': id},
      );

      if (response.statusCode != 200 || response.data['success'] != true) {
        throw Exception(response.data['message'] ?? 'Erro ao cancelar agendamento');
      }
    } catch (e) {
      throw Exception('Erro ao cancelar agendamento: $e');
    }
  }

  Future<List<Map<String, dynamic>>> getCalendar(String mes) async {
    try {
      final response = await _apiClient.get(
        ApiEndpoints.appointmentsCalendar,
        queryParameters: {'mes': mes},
      );

      if (response.statusCode == 200 && response.data['success'] == true) {
        return (response.data['data']['calendar'] as List)
            .map((e) => e as Map<String, dynamic>)
            .toList();
      } else {
        throw Exception(response.data['message'] ?? 'Erro ao buscar calendario');
      }
    } catch (e) {
      throw Exception('Erro ao buscar calendario: $e');
    }
  }
}
