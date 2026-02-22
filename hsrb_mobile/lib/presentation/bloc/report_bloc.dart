import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:equatable/equatable.dart';
import '../../data/repositories/report_repository.dart';

// Events
abstract class ReportEvent extends Equatable {
  @override
  List<Object?> get props => [];
}

class GenerateFinancialReport extends ReportEvent {
  final String dataInicio;
  final String dataFim;
  final int? clinicaId;

  GenerateFinancialReport({
    required this.dataInicio,
    required this.dataFim,
    this.clinicaId,
  });

  @override
  List<Object?> get props => [dataInicio, dataFim, clinicaId];
}

class GenerateAppointmentsReport extends ReportEvent {
  final String dataInicio;
  final String dataFim;
  final int? especialidadeId;
  final int? clinicaId;

  GenerateAppointmentsReport({
    required this.dataInicio,
    required this.dataFim,
    this.especialidadeId,
    this.clinicaId,
  });

  @override
  List<Object?> get props => [dataInicio, dataFim, especialidadeId, clinicaId];
}

// States
abstract class ReportState extends Equatable {
  @override
  List<Object?> get props => [];
}

class ReportInitial extends ReportState {}

class ReportLoading extends ReportState {}

class FinancialReportLoaded extends ReportState {
  final Map<String, dynamic> reportData;

  FinancialReportLoaded(this.reportData);

  @override
  List<Object?> get props => [reportData];
}

class AppointmentsReportLoaded extends ReportState {
  final Map<String, dynamic> reportData;

  AppointmentsReportLoaded(this.reportData);

  @override
  List<Object?> get props => [reportData];
}

class ReportError extends ReportState {
  final String message;

  ReportError(this.message);

  @override
  List<Object?> get props => [message];
}

// BLoC
class ReportBloc extends Bloc<ReportEvent, ReportState> {
  final ReportRepository repository;

  ReportBloc({required this.repository}) : super(ReportInitial()) {
    on<GenerateFinancialReport>(_onGenerateFinancialReport);
    on<GenerateAppointmentsReport>(_onGenerateAppointmentsReport);
  }

  Future<void> _onGenerateFinancialReport(
    GenerateFinancialReport event,
    Emitter<ReportState> emit,
  ) async {
    emit(ReportLoading());
    try {
      final report = await repository.getFinancialReport(
        dataInicio: event.dataInicio,
        dataFim: event.dataFim,
        clinicaId: event.clinicaId,
      );
      emit(FinancialReportLoaded(report));
    } catch (e) {
      emit(ReportError(e.toString()));
    }
  }

  Future<void> _onGenerateAppointmentsReport(
    GenerateAppointmentsReport event,
    Emitter<ReportState> emit,
  ) async {
    emit(ReportLoading());
    try {
      final report = await repository.getAppointmentsReport(
        dataInicio: event.dataInicio,
        dataFim: event.dataFim,
        especialidadeId: event.especialidadeId,
        clinicaId: event.clinicaId,
      );
      emit(AppointmentsReportLoaded(report));
    } catch (e) {
      emit(ReportError(e.toString()));
    }
  }
}
