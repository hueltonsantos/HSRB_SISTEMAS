/// Endpoints da API
class ApiEndpoints {
  ApiEndpoints._(); // Construtor privado
  
  // ===== Base URL =====
  // Produção VPS
  static const String baseUrl = 'https://clinicas.hueltonsites.com.br/api';
  
  // Desenvolvimento local (descomente para testar localmente)
  // static const String baseUrl = 'http://localhost:8080/api';
  
  // ===== Autenticação =====
  static const String login = '/auth/login';
  static const String logout = '/auth/logout';
  static const String refreshToken = '/auth/refresh';
  static const String me = '/auth/me';
  
  // ===== Dashboard =====
  static const String dashboardStats = '/dashboard/stats';
  
  // ===== Pacientes =====
  static const String patientsList = '/patients/list';
  static const String patientsGet = '/patients/get';
  static const String patientsCreate = '/patients/create';
  static const String patientsUpdate = '/patients/update';
  static const String patientsDelete = '/patients/delete';
  
  // ===== Clínicas =====
  static const String clinicsList = '/clinics/list';
  static const String clinicsGet = '/clinics/get';
  static const String clinicsCreate = '/clinics/create';
  static const String clinicsUpdate = '/clinics/update';
  static const String clinicsDelete = '/clinics/delete';
  
  // ===== Especialidades =====
  static const String specialtiesList = '/specialties/list';
  static const String specialtiesGet = '/specialties/get';
  static const String specialtiesCreate = '/specialties/create';
  static const String specialtiesUpdate = '/specialties/update';
  static const String specialtiesDelete = '/specialties/delete';
  
  // ===== Agendamentos =====
  static const String appointmentsList = '/appointments/list';
  static const String appointmentsGet = '/appointments/get';
  static const String appointmentsCreate = '/appointments/create';
  static const String appointmentsUpdate = '/appointments/update';
  static const String appointmentsDelete = '/appointments/delete';
  static const String appointmentsCalendar = '/appointments/calendar';
  
  // ===== Guias =====
  static const String guidesList = '/guides/list';
  static const String guidesGet = '/guides/get';
  static const String guidesCreate = '/guides/create';
  static const String guidesPdf = '/guides/pdf';
  
  // ===== Usuários =====
  static const String usersList = '/users/list';
  static const String usersGet = '/users/get';
  static const String usersCreate = '/users/create';
  static const String usersUpdate = '/users/update';
  static const String usersDelete = '/users/delete';
  static const String usersUpdatePhoto = '/users/update-photo';
  
  // ===== Perfis/Permissões =====
  static const String profilesList = '/profiles/list';
  static const String profilesPermissions = '/profiles/permissions';
  static const String profilesGet = '/profiles/get';
  static const String profilesCreate = '/profiles/create';
  static const String profilesUpdate = '/profiles/update';
  static const String profilesDelete = '/profiles/delete';
  
  // ===== Tabela de Preços =====
  static const String pricesList = '/prices/list';
  static const String pricesGet = '/prices/get';
  static const String pricesUpdate = '/prices/update';
  
  // ===== Configurações =====
  static const String settingsGet = '/settings/get';
  static const String settingsUpdate = '/settings/update';
  
  // ===== Relatórios =====
  static const String reportsFinancial = '/reports/financial';
  static const String reportsAppointments = '/reports/appointments';
  
  // ===== Logs do Sistema =====
  static const String logsList = '/logs/list';
  static const String logsGet = '/logs/get';
  
  // ===== Uploads =====
  static const String uploadImage = '/upload/image';
  static const String uploadDocument = '/upload/document';

}
