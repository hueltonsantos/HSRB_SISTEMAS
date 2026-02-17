import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:equatable/equatable.dart';
import '../../data/repositories/profile_repository.dart';

// Events
abstract class ProfileEvent extends Equatable {
  @override
  List<Object?> get props => [];
}

class LoadProfiles extends ProfileEvent {
  final int page;
  final String? search;
  final int? status;

  LoadProfiles({
    this.page = 1,
    this.search,
    this.status,
  });

  @override
  List<Object?> get props => [page, search, status];
}

class LoadProfileDetail extends ProfileEvent {
  final int id;

  LoadProfileDetail(this.id);

  @override
  List<Object?> get props => [id];
}

class CreateProfile extends ProfileEvent {
  final String nome;
  final String? descricao;
  final List<int>? permissoes;

  CreateProfile({
    required this.nome,
    this.descricao,
    this.permissoes,
  });

  @override
  List<Object?> get props => [nome, descricao, permissoes];
}

class UpdateProfile extends ProfileEvent {
  final int id;
  final String? nome;
  final String? descricao;
  final List<int>? permissoes;
  final int? status;

  UpdateProfile({
    required this.id,
    this.nome,
    this.descricao,
    this.permissoes,
    this.status,
  });

  @override
  List<Object?> get props => [id, nome, descricao, permissoes, status];
}

class DeleteProfile extends ProfileEvent {
  final int id;

  DeleteProfile(this.id);

  @override
  List<Object?> get props => [id];
}

// States
abstract class ProfileState extends Equatable {
  @override
  List<Object?> get props => [];
}

class ProfileInitial extends ProfileState {}

class ProfileLoading extends ProfileState {}

class ProfilesLoaded extends ProfileState {
  final List<dynamic> profiles;
  final int totalPages;
  final int currentPage;
  final int total;

  ProfilesLoaded({
    required this.profiles,
    required this.totalPages,
    required this.currentPage,
    required this.total,
  });

  @override
  List<Object?> get props => [profiles, totalPages, currentPage, total];
}

class ProfileDetailLoaded extends ProfileState {
  final Map<String, dynamic> profile;

  ProfileDetailLoaded(this.profile);

  @override
  List<Object?> get props => [profile];
}

class ProfileOperationSuccess extends ProfileState {
  final String message;

  ProfileOperationSuccess(this.message);

  @override
  List<Object?> get props => [message];
}

class ProfileError extends ProfileState {
  final String message;

  ProfileError(this.message);

  @override
  List<Object?> get props => [message];
}

// BLoC
// Events additions
class LoadPermissions extends ProfileEvent {}

// States additions
class PermissionsLoaded extends ProfileState {
  final List<dynamic> permissions;
  
  PermissionsLoaded(this.permissions);

  @override
  List<Object?> get props => [permissions];
}

class ProfileDetailWithPermissionsLoaded extends ProfileState {
  final Map<String, dynamic> profile;
  final List<dynamic> allPermissions;

  ProfileDetailWithPermissionsLoaded(this.profile, this.allPermissions);

  @override
  List<Object?> get props => [profile, allPermissions];
}

// BLoC updates
class ProfileBloc extends Bloc<ProfileEvent, ProfileState> {
  final ProfileRepository repository;

  ProfileBloc({required this.repository}) : super(ProfileInitial()) {
    on<LoadProfiles>(_onLoadProfiles);
    on<LoadProfileDetail>(_onLoadProfileDetail);
    on<LoadPermissions>(_onLoadPermissions);
    on<CreateProfile>(_onCreateProfile);
    on<UpdateProfile>(_onUpdateProfile);
    on<DeleteProfile>(_onDeleteProfile);
  }

  Future<void> _onLoadProfiles(LoadProfiles event, Emitter<ProfileState> emit) async {
    emit(ProfileLoading());
    try {
      final result = await repository.listProfiles(
        page: event.page,
        search: event.search,
        status: event.status,
      );

      final profiles = result['items'] as List;
      final pagination = result['pagination'] as Map<String, dynamic>;

      emit(ProfilesLoaded(
        profiles: profiles,
        totalPages: pagination['pages'] as int,
        currentPage: pagination['page'] as int,
        total: pagination['total'] as int,
      ));
    } catch (e) {
      emit(ProfileError(e.toString()));
    }
  }

  Future<void> _onLoadProfileDetail(
    LoadProfileDetail event,
    Emitter<ProfileState> emit,
  ) async {
    emit(ProfileLoading());
    try {
      // Parallel fetching
      final results = await Future.wait([
        repository.getProfile(event.id),
        repository.listPermissions(),
      ]);
      
      final profile = results[0] as Map<String, dynamic>;
      final permissions = results[1] as List<dynamic>;
      
      emit(ProfileDetailWithPermissionsLoaded(profile, permissions));
    } catch (e) {
      emit(ProfileError(e.toString()));
    }
  }
  
  Future<void> _onLoadPermissions(
    LoadPermissions event,
    Emitter<ProfileState> emit,
  ) async {
    emit(ProfileLoading());
    try {
      final permissions = await repository.listPermissions();
      emit(PermissionsLoaded(permissions));
    } catch (e) {
      emit(ProfileError(e.toString()));
    }
  }

  Future<void> _onCreateProfile(CreateProfile event, Emitter<ProfileState> emit) async {
    emit(ProfileLoading());
    try {
      await repository.createProfile(
        nome: event.nome,
        descricao: event.descricao,
        permissoes: event.permissoes,
      );
      emit(ProfileOperationSuccess('Perfil criado com sucesso!'));
    } catch (e) {
      emit(ProfileError(e.toString()));
    }
  }

  Future<void> _onUpdateProfile(UpdateProfile event, Emitter<ProfileState> emit) async {
    emit(ProfileLoading());
    try {
      await repository.updateProfile(
        id: event.id,
        nome: event.nome,
        descricao: event.descricao,
        permissoes: event.permissoes,
        status: event.status,
      );
      emit(ProfileOperationSuccess('Perfil atualizado com sucesso!'));
    } catch (e) {
      emit(ProfileError(e.toString()));
    }
  }

  Future<void> _onDeleteProfile(DeleteProfile event, Emitter<ProfileState> emit) async {
    emit(ProfileLoading());
    try {
      await repository.deleteProfile(event.id);
      emit(ProfileOperationSuccess('Perfil deletado com sucesso!'));
    } catch (e) {
      emit(ProfileError(e.toString()));
    }
  }
}
