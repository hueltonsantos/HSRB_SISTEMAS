import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:equatable/equatable.dart';
import '../../data/repositories/price_repository.dart';

// Events
abstract class PriceEvent extends Equatable {
  @override
  List<Object?> get props => [];
}

class LoadPrices extends PriceEvent {
  final int page;
  final String? search;
  final int? especialidadeId;

  LoadPrices({
    this.page = 1,
    this.search,
    this.especialidadeId,
  });

  @override
  List<Object?> get props => [page, search, especialidadeId];
}

class LoadPriceDetail extends PriceEvent {
  final int id;

  LoadPriceDetail(this.id);

  @override
  List<Object?> get props => [id];
}

class UpdatePrice extends PriceEvent {
  final int id;
  final double? valorPaciente;
  final double? valorRepasse;
  final int? status;

  UpdatePrice({
    required this.id,
    this.valorPaciente,
    this.valorRepasse,
    this.status,
  });

  @override
  List<Object?> get props => [id, valorPaciente, valorRepasse, status];
}

// States
abstract class PriceState extends Equatable {
  @override
  List<Object?> get props => [];
}

class PriceInitial extends PriceState {}

class PriceLoading extends PriceState {}

class PricesLoaded extends PriceState {
  final List<dynamic> prices;
  final int totalPages;
  final int currentPage;
  final int total;

  PricesLoaded({
    required this.prices,
    required this.totalPages,
    required this.currentPage,
    required this.total,
  });

  @override
  List<Object?> get props => [prices, totalPages, currentPage, total];
}

class PriceDetailLoaded extends PriceState {
  final Map<String, dynamic> price;

  PriceDetailLoaded(this.price);

  @override
  List<Object?> get props => [price];
}

class PriceOperationSuccess extends PriceState {
  final String message;

  PriceOperationSuccess(this.message);

  @override
  List<Object?> get props => [message];
}

class PriceError extends PriceState {
  final String message;

  PriceError(this.message);

  @override
  List<Object?> get props => [message];
}

// BLoC
class PriceBloc extends Bloc<PriceEvent, PriceState> {
  final PriceRepository repository;

  PriceBloc({required this.repository}) : super(PriceInitial()) {
    on<LoadPrices>(_onLoadPrices);
    on<LoadPriceDetail>(_onLoadPriceDetail);
    on<UpdatePrice>(_onUpdatePrice);
  }

  Future<void> _onLoadPrices(LoadPrices event, Emitter<PriceState> emit) async {
    emit(PriceLoading());
    try {
      final result = await repository.listPrices(
        page: event.page,
        search: event.search,
        especialidadeId: event.especialidadeId,
      );

      final prices = result['items'] as List;
      final pagination = result['pagination'] as Map<String, dynamic>;

      emit(PricesLoaded(
        prices: prices,
        totalPages: pagination['pages'] as int,
        currentPage: pagination['page'] as int,
        total: pagination['total'] as int,
      ));
    } catch (e) {
      emit(PriceError(e.toString()));
    }
  }

  Future<void> _onLoadPriceDetail(
    LoadPriceDetail event,
    Emitter<PriceState> emit,
  ) async {
    emit(PriceLoading());
    try {
      final price = await repository.getPrice(event.id);
      emit(PriceDetailLoaded(price));
    } catch (e) {
      emit(PriceError(e.toString()));
    }
  }

  Future<void> _onUpdatePrice(UpdatePrice event, Emitter<PriceState> emit) async {
    emit(PriceLoading());
    try {
      await repository.updatePrice(
        id: event.id,
        valorPaciente: event.valorPaciente,
        valorRepasse: event.valorRepasse,
        status: event.status,
      );
      emit(PriceOperationSuccess('Preço atualizado com sucesso!'));
    } catch (e) {
      emit(PriceError(e.toString()));
    }
  }
}
