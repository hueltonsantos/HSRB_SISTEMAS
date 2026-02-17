import 'package:equatable/equatable.dart';
import '../../../data/models/user_model.dart';

/// Estados da autenticação
abstract class AuthState extends Equatable {
  const AuthState();

  @override
  List<Object?> get props => [];
}

/// Estado inicial
class AuthInitial extends AuthState {}

/// Carregando
class AuthLoading extends AuthState {}

/// Autenticado
class AuthAuthenticated extends AuthState {
  final UserModel user;

  const AuthAuthenticated(this.user);

  @override
  List<Object?> get props => [user];
}

/// Não autenticado
class AuthUnauthenticated extends AuthState {}

/// Erro
class AuthError extends AuthState {
  final String message;

  const AuthError(this.message);

  @override
  List<Object?> get props => [message];
}
