import 'package:equatable/equatable.dart';
import 'user_model.dart';

/// Model de Resposta de Autenticação
/// Representa a resposta do endpoint /api/auth/login
class AuthResponseModel extends Equatable {
  final String token;
  final String refreshToken;
  final UserModel user;

  const AuthResponseModel({
    required this.token,
    required this.refreshToken,
    required this.user,
  });

  /// Criar AuthResponseModel a partir de JSON
  factory AuthResponseModel.fromJson(Map<String, dynamic> json) {
    return AuthResponseModel(
      token: json['token'] as String,
      refreshToken: json['refresh_token'] as String,
      user: UserModel.fromJson(json['user'] as Map<String, dynamic>),
    );
  }

  /// Converter AuthResponseModel para JSON
  Map<String, dynamic> toJson() {
    return {
      'token': token,
      'refresh_token': refreshToken,
      'user': user.toJson(),
    };
  }

  @override
  List<Object?> get props => [token, refreshToken, user];
}
