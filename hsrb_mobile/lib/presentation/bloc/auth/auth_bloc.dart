import 'package:flutter_bloc/flutter_bloc.dart';
import '../../../core/services/auth_service.dart';
import 'auth_event.dart';
import 'auth_state.dart';

/// BLoC de Autenticação
/// Gerencia o estado de autenticação do usuário
class AuthBloc extends Bloc<AuthEvent, AuthState> {
  final AuthService _authService;

  AuthBloc(this._authService) : super(AuthInitial()) {
    on<AuthCheckRequested>(_onAuthCheckRequested);
    on<AuthLoginRequested>(_onAuthLoginRequested);
    on<AuthLogoutRequested>(_onAuthLogoutRequested);
    on<AuthUserUpdateRequested>(_onAuthUserUpdateRequested);
  }

  /// Verificar se está autenticado
  Future<void> _onAuthCheckRequested(
    AuthCheckRequested event,
    Emitter<AuthState> emit,
  ) async {
    emit(AuthLoading());

    try {
      if (_authService.isLoggedIn) {
        // Tentar buscar usuário atualizado
        try {
          final user = await _authService.getCurrentUser();
          emit(AuthAuthenticated(user));
        } catch (e) {
          // Verificar se os tokens ainda existem (podem ter sido limpos pelo refresh falho)
          if (_authService.isLoggedIn) {
            // Tokens ainda validos mas sem internet - usar usuario salvo
            final savedUser = _authService.getSavedUser();
            if (savedUser != null) {
              emit(AuthAuthenticated(savedUser));
            } else {
              emit(AuthUnauthenticated());
            }
          } else {
            // Tokens foram limpos (sessao expirada) - forcar re-login
            emit(AuthUnauthenticated());
          }
        }
      } else {
        emit(AuthUnauthenticated());
      }
    } catch (e) {
      emit(AuthUnauthenticated());
    }
  }

  /// Fazer login
  Future<void> _onAuthLoginRequested(
    AuthLoginRequested event,
    Emitter<AuthState> emit,
  ) async {
    emit(AuthLoading());

    try {
      final authResponse = await _authService.login(
        event.email,
        event.password,
      );

      emit(AuthAuthenticated(authResponse.user));
    } catch (e) {
      emit(AuthError(e.toString().replaceAll('Exception: ', '')));
      emit(AuthUnauthenticated());
    }
  }

  /// Fazer logout
  Future<void> _onAuthLogoutRequested(
    AuthLogoutRequested event,
    Emitter<AuthState> emit,
  ) async {
    emit(AuthLoading());

    try {
      await _authService.logout();
      emit(AuthUnauthenticated());
    } catch (e) {
      emit(AuthError('Erro ao fazer logout'));
      emit(AuthUnauthenticated());
    }
  }

  /// Atualizar dados do usuário
  Future<void> _onAuthUserUpdateRequested(
    AuthUserUpdateRequested event,
    Emitter<AuthState> emit,
  ) async {
    try {
      final user = await _authService.getCurrentUser();
      emit(AuthAuthenticated(user));
    } catch (e) {
      // Manter estado atual em caso de erro
    }
  }
}
