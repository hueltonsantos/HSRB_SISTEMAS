import 'package:equatable/equatable.dart';

/// Eventos de autenticação
abstract class AuthEvent extends Equatable {
  const AuthEvent();

  @override
  List<Object?> get props => [];
}

/// Verificar se está autenticado
class AuthCheckRequested extends AuthEvent {}

/// Fazer login
class AuthLoginRequested extends AuthEvent {
  final String email;
  final String password;

  const AuthLoginRequested({
    required this.email,
    required this.password,
  });

  @override
  List<Object?> get props => [email, password];
}

/// Fazer logout
class AuthLogoutRequested extends AuthEvent {}

/// Atualizar dados do usuário
class AuthUserUpdateRequested extends AuthEvent {}
