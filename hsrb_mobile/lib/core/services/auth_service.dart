import 'dart:convert';
import '../network/api_client.dart';
import '../constants/api_endpoints.dart';
import '../storage/storage_manager.dart';
import '../../data/models/auth_response_model.dart';
import '../../data/models/user_model.dart';

/// Serviço de Autenticação
/// Gerencia login, logout, refresh token e dados do usuário
class AuthService {
  final ApiClient _apiClient;
  final StorageManager _storage;

  AuthService(this._apiClient, this._storage);

  /// Fazer login
  Future<AuthResponseModel> login(String email, String password) async {
    try {
      final response = await _apiClient.post(
        ApiEndpoints.login,
        data: {
          'email': email,
          'senha': password,
        },
      );

      if (response.statusCode == 200 && response.data['success'] == true) {
        final authResponse = AuthResponseModel.fromJson(response.data['data']);
        
        // Salvar tokens e dados do usuário
        await _storage.saveToken(authResponse.token);
        await _storage.saveRefreshToken(authResponse.refreshToken);
        await _storage.saveUserData(jsonEncode(authResponse.user.toJson()));
        
        return authResponse;
      } else {
        throw Exception(response.data['message'] ?? 'Erro ao fazer login');
      }
    } catch (e) {
      throw Exception('Erro ao fazer login: $e');
    }
  }

  /// Fazer logout
  Future<void> logout() async {
    try {
      // Chamar endpoint de logout (opcional, apenas para log)
      await _apiClient.post(ApiEndpoints.logout);
    } catch (e) {
      print('[AuthService] Erro ao fazer logout na API: $e');
    } finally {
      // Limpar dados locais independentemente do resultado
      await _storage.clearAll();
    }
  }

  /// Obter dados do usuário atual
  Future<UserModel> getCurrentUser() async {
    try {
      final response = await _apiClient.get(ApiEndpoints.me);

      if (response.statusCode == 200 && response.data['success'] == true) {
        final user = UserModel.fromJson(response.data['data']['user']);
        
        // Atualizar dados salvos
        await _storage.saveUserData(jsonEncode(user.toJson()));
        
        return user;
      } else {
        throw Exception(response.data['message'] ?? 'Erro ao buscar usuário');
      }
    } catch (e) {
      throw Exception('Erro ao buscar usuário: $e');
    }
  }

  /// Obter usuário salvo localmente
  UserModel? getSavedUser() {
    final userData = _storage.getUserData();
    if (userData != null) {
      try {
        return UserModel.fromJson(jsonDecode(userData));
      } catch (e) {
        print('[AuthService] Erro ao decodificar usuário salvo: $e');
        return null;
      }
    }
    return null;
  }

  /// Verificar se está logado
  bool get isLoggedIn => _storage.isLoggedIn;

  /// Obter token atual
  Future<String?> getToken() async {
    return _storage.getToken();
  }

  /// Renovar token (delega ao ApiClient que usa Dio separado)
  Future<bool> refreshToken() async {
    try {
      final refreshToken = _storage.getRefreshToken();
      if (refreshToken == null) return false;

      // O ApiClient._refreshToken() ja e chamado automaticamente
      // pelo interceptor quando recebe 401. Este metodo serve para
      // refresh manual se necessario.
      final response = await _apiClient.post(
        ApiEndpoints.refreshToken,
        data: {'refresh_token': refreshToken},
      );

      if (response.statusCode == 200 && response.data['success'] == true) {
        final newToken = response.data['data']['token'] as String;
        final newRefreshToken = response.data['data']['refresh_token'] as String;

        await _storage.saveToken(newToken);
        await _storage.saveRefreshToken(newRefreshToken);

        return true;
      }

      return false;
    } catch (e) {
      print('[AuthService] Erro ao renovar token: $e');
      return false;
    }
  }
}
