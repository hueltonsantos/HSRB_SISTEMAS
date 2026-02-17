/// Strings e textos do aplicativo
class AppStrings {
  AppStrings._(); // Construtor privado
  
  // ===== Nome do Sistema =====
  static const String appName = 'HSRB_SISTEMAS';
  static const String appFullName = 'Sistema de Atendimento Clínico';
  
  // ===== Autenticação =====
  static const String login = 'Entrar';
  static const String logout = 'Sair';
  static const String email = 'Email';
  static const String password = 'Senha';
  static const String forgotPassword = 'Esqueceu a senha?';
  static const String welcomeBack = 'Bem-vindo ao';
  static const String loginSuccess = 'Login realizado com sucesso';
  static const String loginError = 'Email ou senha inválidos';
  static const String logoutConfirm = 'Deseja realmente sair?';
  
  // ===== Navegação =====
  static const String dashboard = 'Dashboard';
  static const String patients = 'Pacientes';
  static const String clinics = 'Clínicas Parceiras';
  static const String specialties = 'Especialidades';
  static const String appointments = 'Agendamentos';
  static const String guides = 'Guias de Encaminhamento';
  static const String users = 'Usuários';
  static const String profiles = 'Permissões';
  static const String myClinic = 'Minha Clínica';
  static const String settings = 'Configurações';
  static const String reports = 'Relatórios';
  static const String about = 'Sobre';
  
  // ===== Dashboard =====
  static const String totalPatients = 'Total de Pacientes';
  static const String totalAppointments = 'Total de Agendamentos';
  static const String appointmentsToday = 'Agendamentos Hoje';
  static const String pendingAppointments = 'Agendamentos Pendentes';
  static const String totalClinics = 'Total de Clínicas';
  static const String totalSpecialties = 'Total de Especialidades';
  static const String recentAppointments = 'Agendamentos Recentes';
  static const String statistics = 'Estatísticas';
  
  // ===== Pacientes =====
  static const String newPatient = 'Novo Paciente';
  static const String editPatient = 'Editar Paciente';
  static const String patientDetails = 'Detalhes do Paciente';
  static const String patientName = 'Nome do Paciente';
  static const String cpf = 'CPF';
  static const String rg = 'RG';
  static const String birthDate = 'Data de Nascimento';
  static const String gender = 'Sexo';
  static const String phone = 'Telefone';
  static const String address = 'Endereço';
  static const String city = 'Cidade';
  static const String state = 'Estado';
  static const String zipCode = 'CEP';
  static const String guardianName = 'Nome do Responsável';
  static const String guardianPhone = 'Telefone do Responsável';
  static const String observations = 'Observações';
  
  // ===== Ações =====
  static const String save = 'Salvar';
  static const String cancel = 'Cancelar';
  static const String delete = 'Excluir';
  static const String edit = 'Editar';
  static const String view = 'Visualizar';
  static const String search = 'Buscar';
  static const String filter = 'Filtrar';
  static const String refresh = 'Atualizar';
  static const String confirm = 'Confirmar';
  static const String yes = 'Sim';
  static const String no = 'Não';
  static const String ok = 'OK';
  static const String close = 'Fechar';
  
  // ===== Mensagens =====
  static const String loading = 'Carregando...';
  static const String noData = 'Nenhum registro encontrado';
  static const String error = 'Erro';
  static const String success = 'Sucesso';
  static const String warning = 'Aviso';
  static const String info = 'Informação';
  static const String deleteConfirm = 'Deseja realmente excluir?';
  static const String saveSuccess = 'Salvo com sucesso';
  static const String deleteSuccess = 'Excluído com sucesso';
  static const String updateSuccess = 'Atualizado com sucesso';
  static const String errorOccurred = 'Ocorreu um erro';
  static const String tryAgain = 'Tentar novamente';
  static const String noInternet = 'Sem conexão com a internet';
  static const String sessionExpired = 'Sessão expirada. Faça login novamente.';
  
  // ===== Validação =====
  static const String requiredField = 'Campo obrigatório';
  static const String invalidEmail = 'Email inválido';
  static const String invalidCpf = 'CPF inválido';
  static const String invalidPhone = 'Telefone inválido';
  static const String invalidDate = 'Data inválida';
  
  // ===== Status =====
  static const String active = 'Ativo';
  static const String inactive = 'Inativo';
  static const String pending = 'Pendente';
  static const String confirmed = 'Confirmado';
  static const String cancelled = 'Cancelado';
  static const String completed = 'Concluído';
}
