import '../network/api_client.dart';
import '../constants/api_endpoints.dart';
import '../../data/models/dashboard_stats_model.dart';

/// Serviço de Dashboard
/// Busca estatísticas e dados do dashboard
class DashboardService {
  final ApiClient _apiClient;

  DashboardService(this._apiClient);

  /// Buscar estatísticas do dashboard
  Future<DashboardStatsModel> getStats() async {
    try {
      final response = await _apiClient.get(ApiEndpoints.dashboardStats);

      if (response.statusCode == 200 && response.data['success'] == true) {
        return DashboardStatsModel.fromJson(response.data['data']);
      } else {
        throw Exception(response.data['message'] ?? 'Erro ao buscar estatísticas');
      }
    } catch (e) {
      throw Exception('Erro ao buscar estatísticas: $e');
    }
  }
}
