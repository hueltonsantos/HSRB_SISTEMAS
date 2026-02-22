import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'core/theme/app_theme.dart';
import 'core/storage/storage_manager.dart';
import 'core/network/api_client.dart';
import 'core/services/auth_service.dart';
import 'core/services/dashboard_service.dart';
import 'core/services/patient_service.dart';
import 'core/services/specialty_service.dart';
import 'core/services/clinic_service.dart';
import 'core/services/appointment_service.dart';
import 'core/services/guide_service.dart';
import 'presentation/bloc/auth/auth_bloc.dart';
import 'presentation/bloc/auth/auth_event.dart';
import 'presentation/bloc/auth/auth_state.dart';
import 'presentation/pages/splash/splash_page.dart';
import 'presentation/pages/login/login_page.dart';
import 'presentation/pages/dashboard/dashboard_page.dart';
// New module pages
import 'presentation/pages/users/users_list_page.dart';
import 'presentation/pages/users/user_detail_page.dart';
import 'presentation/pages/users/user_form_page.dart';
import 'presentation/pages/profiles/profiles_list_page.dart';
import 'presentation/pages/prices/prices_list_page.dart';
import 'presentation/pages/settings/settings_page.dart';
import 'presentation/pages/reports/reports_page.dart';
import 'presentation/pages/reports/financial_report_page.dart';
import 'presentation/pages/logs/system_logs_page.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();

  final prefs = await SharedPreferences.getInstance();

  final storage = StorageManager(prefs);
  final apiClient = ApiClient(storage);
  final authService = AuthService(apiClient, storage);
  final dashboardService = DashboardService(apiClient);
  final patientService = PatientService(apiClient);
  final specialtyService = SpecialtyService(apiClient);
  final clinicService = ClinicService(apiClient);
  final appointmentService = AppointmentService(apiClient);
  final guideService = GuideService(apiClient);

  runApp(HSRBMobileApp(
    storage: storage,
    authService: authService,
    dashboardService: dashboardService,
    patientService: patientService,
    specialtyService: specialtyService,
    clinicService: clinicService,
    appointmentService: appointmentService,
    guideService: guideService,
  ));
}

class HSRBMobileApp extends StatelessWidget {
  final StorageManager storage;
  final AuthService authService;
  final DashboardService dashboardService;
  final PatientService patientService;
  final SpecialtyService specialtyService;
  final ClinicService clinicService;
  final AppointmentService appointmentService;
  final GuideService guideService;

  const HSRBMobileApp({
    super.key,
    required this.storage,
    required this.authService,
    required this.dashboardService,
    required this.patientService,
    required this.specialtyService,
    required this.clinicService,
    required this.appointmentService,
    required this.guideService,
  });

  @override
  Widget build(BuildContext context) {
    return MultiRepositoryProvider(
      providers: [
        RepositoryProvider.value(value: dashboardService),
        RepositoryProvider.value(value: patientService),
        RepositoryProvider.value(value: specialtyService),
        RepositoryProvider.value(value: clinicService),
        RepositoryProvider.value(value: appointmentService),
        RepositoryProvider.value(value: guideService),
        RepositoryProvider.value(value: authService),
      ],
      child: MultiBlocProvider(
        providers: [
          BlocProvider(
            create: (context) => AuthBloc(authService)..add(AuthCheckRequested()),
          ),
        ],
        child: MaterialApp(
          title: 'HSRB_SISTEMAS',
          debugShowCheckedModeBanner: false,
          theme: AppTheme.lightTheme,
          home: BlocBuilder<AuthBloc, AuthState>(
            builder: (context, state) {
              if (state is AuthInitial) {
                return const SplashPage();
              } else if (state is AuthAuthenticated) {
                return DashboardPage(user: state.user);
              } else {
                return const LoginPage();
              }
            },
          ),
          routes: {
            '/users': (context) => const UsersListPage(),
            '/users/detail': (context) {
              final userId = ModalRoute.of(context)!.settings.arguments as int;
              return UserDetailPage(userId: userId);
            },
            '/users/form': (context) {
              final userId = ModalRoute.of(context)?.settings.arguments as int?;
              return UserFormPage(userId: userId);
            },
            '/profiles': (context) => const ProfilesListPage(),
            '/prices': (context) => const PricesListPage(),
            '/settings': (context) => const SettingsPage(),
            '/reports': (context) => const ReportsPage(),
            '/reports/financial': (context) => const FinancialReportPage(),
            '/logs': (context) => const SystemLogsPage(),
          },
        ),
      ),
    );
  }
}
