import 'package:dio/dio.dart';
import '../constants/api_endpoints.dart';
import '../storage/storage_manager.dart';

/// Cliente HTTP para comunicação com a API
/// Configurado com interceptors para autenticação e logging
class ApiClient {
  late final Dio _dio;
  late final Dio _refreshDio;
  final StorageManager _storage;
  bool _isRefreshing = false;

  ApiClient(this._storage) {
    _dio = Dio(
      BaseOptions(
        baseUrl: ApiEndpoints.baseUrl,
        connectTimeout: const Duration(seconds: 30),
        receiveTimeout: const Duration(seconds: 30),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
      ),
    );

    // Dio separado para refresh (sem interceptor de 401, evita loop)
    _refreshDio = Dio(
      BaseOptions(
        baseUrl: ApiEndpoints.baseUrl,
        connectTimeout: const Duration(seconds: 30),
        receiveTimeout: const Duration(seconds: 30),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
      ),
    );

    // Interceptor para adicionar token de autenticação
    _dio.interceptors.add(
      InterceptorsWrapper(
        onRequest: (options, handler) {
          final token = _storage.getToken();
          if (token != null) {
            options.headers['Authorization'] = 'Bearer $token';
          }
          return handler.next(options);
        },
        onError: (error, handler) async {
          // Se erro 401 (não autorizado), tentar refresh token
          // Não tentar refresh se já estiver refreshing ou se for o próprio endpoint de refresh
          if (error.response?.statusCode == 401 &&
              !_isRefreshing &&
              !error.requestOptions.path.contains('auth/refresh')) {
            final refreshed = await _refreshToken();
            if (refreshed) {
              // Tentar novamente a requisição original
              final options = error.requestOptions;
              final token = _storage.getToken();
              options.headers['Authorization'] = 'Bearer $token';

              try {
                final response = await _dio.fetch(options);
                return handler.resolve(response);
              } catch (e) {
                return handler.next(error);
              }
            }
          }
          return handler.next(error);
        },
        onResponse: (response, handler) {
          return handler.next(response);
        },
      ),
    );

    // Interceptor para logging (apenas em debug)
    _dio.interceptors.add(
      LogInterceptor(
        requestBody: true,
        responseBody: true,
        error: true,
        logPrint: (obj) => print('[API] $obj'),
      ),
    );
  }

  /// Tentar renovar o token usando refresh token
  Future<bool> _refreshToken() async {
    if (_isRefreshing) return false;
    _isRefreshing = true;

    try {
      final refreshToken = _storage.getRefreshToken();
      if (refreshToken == null) {
        _isRefreshing = false;
        return false;
      }

      final token = _storage.getToken();
      final response = await _refreshDio.post(
        ApiEndpoints.refreshToken,
        data: {'refresh_token': refreshToken},
        options: Options(
          headers: {
            if (token != null) 'Authorization': 'Bearer $token',
          },
        ),
      );

      if (response.statusCode == 200 && response.data['success'] == true) {
        final newToken = response.data['data']['token'] as String;
        final newRefreshToken = response.data['data']['refresh_token'] as String;

        await _storage.saveToken(newToken);
        await _storage.saveRefreshToken(newRefreshToken);

        _isRefreshing = false;
        return true;
      }

      // Refresh falhou - limpar sessao para forcar re-login
      await _storage.clearAll();
      _isRefreshing = false;
      return false;
    } catch (e) {
      print('[API] Erro ao renovar token: $e');
      // Refresh falhou - limpar sessao para forcar re-login
      await _storage.clearAll();
      _isRefreshing = false;
      return false;
    }
  }

  // ===== GET =====

  Future<Response> get(
    String path, {
    Map<String, dynamic>? queryParameters,
    Options? options,
  }) async {
    return await _dio.get(
      path,
      queryParameters: queryParameters,
      options: options,
    );
  }

  // ===== POST =====

  Future<Response> post(
    String path, {
    dynamic data,
    Map<String, dynamic>? queryParameters,
    Options? options,
  }) async {
    return await _dio.post(
      path,
      data: data,
      queryParameters: queryParameters,
      options: options,
    );
  }

  // ===== PUT =====

  Future<Response> put(
    String path, {
    dynamic data,
    Map<String, dynamic>? queryParameters,
    Options? options,
  }) async {
    return await _dio.put(
      path,
      data: data,
      queryParameters: queryParameters,
      options: options,
    );
  }

  // ===== DELETE =====

  Future<Response> delete(
    String path, {
    dynamic data,
    Map<String, dynamic>? queryParameters,
    Options? options,
  }) async {
    return await _dio.delete(
      path,
      data: data,
      queryParameters: queryParameters,
      options: options,
    );
  }

  // ===== UPLOAD =====

  Future<Response> upload(
    String path,
    String filePath, {
    String fileKey = 'file',
    Map<String, dynamic>? data,
    ProgressCallback? onSendProgress,
  }) async {
    final formData = FormData.fromMap({
      fileKey: await MultipartFile.fromFile(filePath),
      ...?data,
    });

    return await _dio.post(
      path,
      data: formData,
      onSendProgress: onSendProgress,
    );
  }
}
