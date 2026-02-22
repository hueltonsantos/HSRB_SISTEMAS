import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:equatable/equatable.dart';
import '../../data/models/user_model.dart';
import '../../data/repositories/user_repository.dart';

// Events
abstract class UserEvent extends Equatable {
  @override
  List<Object?> get props => [];
}

class LoadUsers extends UserEvent {
  final int page;
  final String? search;
  final int? status;
  final int? perfilId;

  LoadUsers({
    this.page = 1,
    this.search,
    this.status,
    this.perfilId,
  });

  @override
  List<Object?> get props => [page, search, status, perfilId];
}

class LoadUserDetail extends UserEvent {
  final int userId;

  LoadUserDetail(this.userId);

  @override
  List<Object?> get props => [userId];
}

class CreateUser extends UserEvent {
  final String nome;
  final String email;
  final String senha;
  final String nivelAcesso;
  final int? perfilId;
  final int? clinicaId;

  CreateUser({
    required this.nome,
    required this.email,
    required this.senha,
    required this.nivelAcesso,
    this.perfilId,
    this.clinicaId,
  });

  @override
  List<Object?> get props => [nome, email, senha, nivelAcesso, perfilId, clinicaId];
}

class UpdateUser extends UserEvent {
  final int id;
  final String? nome;
  final String? email;
  final String? senha;
  final String? nivelAcesso;
  final int? perfilId;
  final int? status;

  UpdateUser({
    required this.id,
    this.nome,
    this.email,
    this.senha,
    this.nivelAcesso,
    this.perfilId,
    this.status,
  });

  @override
  List<Object?> get props => [id, nome, email, senha, nivelAcesso, perfilId, status];
}

class DeleteUser extends UserEvent {
  final int userId;

  DeleteUser(this.userId);

  @override
  List<Object?> get props => [userId];
}

// States
abstract class UserState extends Equatable {
  @override
  List<Object?> get props => [];
}

class UserInitial extends UserState {}

class UserLoading extends UserState {}

class UsersLoaded extends UserState {
  final List<UserModel> users;
  final int totalPages;
  final int currentPage;
  final int total;

  UsersLoaded({
    required this.users,
    required this.totalPages,
    required this.currentPage,
    required this.total,
  });

  @override
  List<Object?> get props => [users, totalPages, currentPage, total];
}

class UserDetailLoaded extends UserState {
  final UserModel user;

  UserDetailLoaded(this.user);

  @override
  List<Object?> get props => [user];
}

class UserOperationSuccess extends UserState {
  final String message;

  UserOperationSuccess(this.message);

  @override
  List<Object?> get props => [message];
}

class UserError extends UserState {
  final String message;

  UserError(this.message);

  @override
  List<Object?> get props => [message];
}

// BLoC
class UserBloc extends Bloc<UserEvent, UserState> {
  final UserRepository repository;

  UserBloc({required this.repository}) : super(UserInitial()) {
    on<LoadUsers>(_onLoadUsers);
    on<LoadUserDetail>(_onLoadUserDetail);
    on<CreateUser>(_onCreateUser);
    on<UpdateUser>(_onUpdateUser);
    on<DeleteUser>(_onDeleteUser);
  }

  Future<void> _onLoadUsers(LoadUsers event, Emitter<UserState> emit) async {
    emit(UserLoading());
    try {
      final result = await repository.listUsers(
        page: event.page,
        search: event.search,
        status: event.status,
        perfilId: event.perfilId,
      );

      final users = result['items'] as List<UserModel>;
      final pagination = result['pagination'] as Map<String, dynamic>;

      emit(UsersLoaded(
        users: users,
        totalPages: pagination['pages'] as int,
        currentPage: pagination['page'] as int,
        total: pagination['total'] as int,
      ));
    } catch (e) {
      emit(UserError(e.toString()));
    }
  }

  Future<void> _onLoadUserDetail(
    LoadUserDetail event,
    Emitter<UserState> emit,
  ) async {
    emit(UserLoading());
    try {
      final user = await repository.getUser(event.userId);
      emit(UserDetailLoaded(user));
    } catch (e) {
      emit(UserError(e.toString()));
    }
  }

  Future<void> _onCreateUser(CreateUser event, Emitter<UserState> emit) async {
    emit(UserLoading());
    try {
      await repository.createUser(
        nome: event.nome,
        email: event.email,
        senha: event.senha,
        nivelAcesso: event.nivelAcesso,
        perfilId: event.perfilId,
        clinicaId: event.clinicaId,
      );
      emit(UserOperationSuccess('Usuário criado com sucesso!'));
    } catch (e) {
      emit(UserError(e.toString()));
    }
  }

  Future<void> _onUpdateUser(UpdateUser event, Emitter<UserState> emit) async {
    emit(UserLoading());
    try {
      await repository.updateUser(
        id: event.id,
        nome: event.nome,
        email: event.email,
        senha: event.senha,
        nivelAcesso: event.nivelAcesso,
        perfilId: event.perfilId,
        status: event.status,
      );
      emit(UserOperationSuccess('Usuário atualizado com sucesso!'));
    } catch (e) {
      emit(UserError(e.toString()));
    }
  }

  Future<void> _onDeleteUser(DeleteUser event, Emitter<UserState> emit) async {
    emit(UserLoading());
    try {
      await repository.deleteUser(event.userId);
      emit(UserOperationSuccess('Usuário deletado com sucesso!'));
    } catch (e) {
      emit(UserError(e.toString()));
    }
  }
}
