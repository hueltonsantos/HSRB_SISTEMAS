import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:equatable/equatable.dart';
import '../../data/repositories/log_repository.dart';

// Events
abstract class LogEvent extends Equatable {
  @override
  List<Object?> get props => [];
}

class LoadLogs extends LogEvent {
  final int page;
  final String? modulo;
  final String? acao;
  final int? usuarioId;

  LoadLogs({
    this.page = 1,
    this.modulo,
    this.acao,
    this.usuarioId,
  });

  @override
  List<Object?> get props => [page, modulo, acao, usuarioId];
}

class LoadLogDetail extends LogEvent {
  final int logId;

  LoadLogDetail(this.logId);

  @override
  List<Object?> get props => [logId];
}

// States
abstract class LogState extends Equatable {
  @override
  List<Object?> get props => [];
}

class LogInitial extends LogState {}

class LogLoading extends LogState {}

class LogsLoaded extends LogState {
  final List<Map<String, dynamic>> logs;
  final int totalPages;
  final int currentPage;
  final int total;

  LogsLoaded({
    required this.logs,
    required this.totalPages,
    required this.currentPage,
    required this.total,
  });

  @override
  List<Object?> get props => [logs, totalPages, currentPage, total];
}

class LogDetailLoaded extends LogState {
  final Map<String, dynamic> log;

  LogDetailLoaded(this.log);

  @override
  List<Object?> get props => [log];
}

class LogError extends LogState {
  final String message;

  LogError(this.message);

  @override
  List<Object?> get props => [message];
}

// BLoC
class LogBloc extends Bloc<LogEvent, LogState> {
  final LogRepository repository;

  LogBloc({required this.repository}) : super(LogInitial()) {
    on<LoadLogs>(_onLoadLogs);
    on<LoadLogDetail>(_onLoadLogDetail);
  }

  Future<void> _onLoadLogs(LoadLogs event, Emitter<LogState> emit) async {
    emit(LogLoading());
    try {
      final result = await repository.listLogs(
        page: event.page,
        modulo: event.modulo,
        acao: event.acao,
        usuarioId: event.usuarioId,
      );

      final logs = result['items'] as List<Map<String, dynamic>>;
      final pagination = result['pagination'] as Map<String, dynamic>;

      emit(LogsLoaded(
        logs: logs,
        totalPages: pagination['pages'] as int,
        currentPage: pagination['page'] as int,
        total: pagination['total'] as int,
      ));
    } catch (e) {
      emit(LogError(e.toString()));
    }
  }

  Future<void> _onLoadLogDetail(
    LoadLogDetail event,
    Emitter<LogState> emit,
  ) async {
    emit(LogLoading());
    try {
      final log = await repository.getLog(event.logId);
      emit(LogDetailLoaded(log));
    } catch (e) {
      emit(LogError(e.toString()));
    }
  }
}
