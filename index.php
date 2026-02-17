<?php
require_once 'auth.php';
// Para páginas administrativas:
// verificar_acesso(['admin']);
// Para páginas de médicos e recepcionistas:
// verificar_acesso(['admin', 'medico', 'recepcionista']);

/**
 * Sistema de Encaminhamento Clínico
 * Arquivo principal que gerencia o carregamento dos módulos
 */

// Carrega o arquivo de configuração
require_once 'config.php';

// Carrega a classe de conexão com o banco de dados
require_once 'Database.php';

// Carrega a classe base Model
require_once 'Model.php';

// Determina o módulo a ser carregado
$module = isset($_GET['module']) ? $_GET['module'] : (isset($_GET['modulo']) ? $_GET['modulo'] : 'dashboard');

// Determina a ação a ser executada
$action = isset($_GET['action']) ? $_GET['action'] : 'list';

// Verifica se o módulo existe
$modulePath = MODULES_PATH . '/' . $module;
if (!file_exists($modulePath)) {
    // Busca se existe com o parâmetro 'modulo'
    if (isset($_GET['modulo']) && file_exists(MODULES_PATH . '/' . $_GET['modulo'])) {
        $module = $_GET['modulo'];
        $modulePath = MODULES_PATH . '/' . $module;
    } else {
        $module = 'dashboard';
        $modulePath = MODULES_PATH . '/dashboard';
    }
}

// Verifica se o arquivo de inclusão do módulo existe
$includeFile = $modulePath . '/include.php';
if (!file_exists($includeFile)) {
    // Se não existir, redireciona para o dashboard
    header('Location: index.php?module=dashboard');
    exit;
}

// Inclui o arquivo de inclusão do módulo
require_once $includeFile;

// Determina a função de processamento do módulo
$processFunction = $module . 'ProcessAction';
if (!function_exists($processFunction)) {
    die('Função de processamento do módulo não encontrada: ' . $processFunction);
}

// Processa a ação e obtém o conteúdo
$content = $processFunction($action);

// Define o título da página
$pageTitle = SYSTEM_NAME;
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo $pageTitle; ?></title>

    <!-- Fontes e ícones -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Bootstrap e estilos personalizados -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assents/css/style.css" rel="stylesheet">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- jQuery Mask Plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
    <!-- Para usar o Bootstrap Datepicker -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.pt-BR.min.js"></script>

    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap4.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap4.min.js"></script>

    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    // Configuração global para evitar erro de reinicialização do DataTables
    if ($.fn.DataTable) {
        $.extend(true, $.fn.dataTable.defaults, {
            "retrieve": true, // Recupera a instância existente se já inicializada
            "language": {
                "sEmptyTable": "Nenhum registro encontrado",
                "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
                "sInfoFiltered": "(Filtrados de _MAX_ registros)",
                "sSearch": "Pesquisar",
                "oPaginate": { "sNext": "Próximo", "sPrevious": "Anterior" }
            }
        });
    }
    </script>


</head>

<body id="page-top">
    <div id="wrapper">
        <!-- Backdrop para mobile -->
        <div class="sidebar-backdrop"></div>

        <!-- Barra lateral -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <!-- Logo do sistema -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-clinic-medical"></i>
                </div>
                <div class="sidebar-brand-text mx-3">HSRB_SISTEMAS</div>
            </a>

            <!-- Divisor -->
            <hr class="sidebar-divider my-0">

            <!-- Item do Dashboard -->
            <li class="nav-item <?php echo $module == 'dashboard' ? 'active' : ''; ?>">
                <a class="nav-link" href="index.php?module=dashboard">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Divisor -->
            <hr class="sidebar-divider">

            <?php if (hasPermission('master_dashboard')): ?>
            <!-- Minha Clinica (Master) -->
            <div class="sidebar-heading">
                Minha Clinica
            </div>

            <li class="nav-item <?php echo $module == 'minha_clinica' ? 'active' : ''; ?>">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseMaster"
                    aria-expanded="true" aria-controls="collapseMaster">
                    <i class="fas fa-fw fa-clinic-medical"></i>
                    <span>Minha Clinica</span>
                </a>
                <div id="collapseMaster" class="collapse <?php echo $module == 'minha_clinica' ? 'show' : ''; ?>" aria-labelledby="headingMaster">
                    <div class="py-2 collapse-inner rounded">
                        <a class="collapse-item <?php echo $module == 'minha_clinica' && $action == 'index' ? 'active' : ''; ?>" href="index.php?module=minha_clinica">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                        <?php if (hasPermission('master_agendamentos')): ?>
                        <a class="collapse-item <?php echo $module == 'minha_clinica' && $action == 'agendamentos' ? 'active' : ''; ?>" href="index.php?module=minha_clinica&action=agendamentos">
                            <i class="fas fa-calendar-alt"></i> Agendamentos
                        </a>
                        <?php endif; ?>
                        <?php if (hasPermission('master_especialidades')): ?>
                        <a class="collapse-item <?php echo $module == 'minha_clinica' && $action == 'especialidades' ? 'active' : ''; ?>" href="index.php?module=minha_clinica&action=especialidades">
                            <i class="fas fa-stethoscope"></i> Especialidades
                        </a>
                        <?php endif; ?>
                        <?php if (hasPermission('master_procedimentos')): ?>
                        <a class="collapse-item <?php echo $module == 'minha_clinica' && $action == 'procedimentos' ? 'active' : ''; ?>" href="index.php?module=minha_clinica&action=procedimentos">
                            <i class="fas fa-notes-medical"></i> Procedimentos
                        </a>
                        <?php endif; ?>
                        <?php if (hasPermission('master_profissionais')): ?>
                        <a class="collapse-item <?php echo $module == 'minha_clinica' && $action == 'profissionais' ? 'active' : ''; ?>" href="index.php?module=minha_clinica&action=profissionais">
                            <i class="fas fa-user-md"></i> Profissionais
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </li>

            <hr class="sidebar-divider">
            <?php endif; ?>

            <!-- Cabeçalho - Módulos -->
            <div class="sidebar-heading">
                Modulos
            </div>

            <?php if (hasPermission('appointment_view') || hasPermission('appointment_create')): ?>
            <!-- Módulo de Pacientes -->
            <li class="nav-item <?php echo $module == 'pacientes' ? 'active' : ''; ?>">
                <a class="nav-link" href="index.php?module=pacientes">
                    <i class="fas fa-fw fa-user"></i>
                    <span>Pacientes</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (hasPermission('role_manage')): // Usando role_manage como proxy para admin em clínicas ?>
            <!-- Módulo de Clínicas -->
            <li class="nav-item <?php echo $module == 'clinicas' ? 'active' : ''; ?>">
                <a class="nav-link" href="index.php?module=clinicas">
                    <i class="fas fa-fw fa-hospital"></i>
                    <span>Clínicas Parceiras</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (hasPermission('role_manage')): // Especialidades restrito a admin/gestor ?>
            <!-- Módulo de Especialidades -->
            <li class="nav-item <?php echo $module == 'especialidades' ? 'active' : ''; ?>">
                <a class="nav-link" href="index.php?module=especialidades">
                    <i class="fas fa-fw fa-stethoscope"></i>
                    <span>Especialidades</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (hasPermission('appointment_view') || hasPermission('appointment_create')): ?>
            <!-- Módulo de Agendamentos -->
            <li class="nav-item <?php echo $module == 'agendamentos' ? 'active' : ''; ?>">
                <a class="nav-link" href="index.php?module=agendamentos">
                    <i class="fas fa-fw fa-calendar-alt"></i>
                    <span>Agendamentos</span>
                </a>
            </li>
            <?php endif; ?>
            <!-- Módulo de Procedimentos -->
            <!-- <li class="nav-item <?php echo $module == 'procedimentos' ? 'active' : ''; ?>">
                <a class="nav-link" href="index.php?module=procedimentos">
                    <i class="fas fa-fw fa-procedures"></i>
                    <span>Procedimentos</span>
                </a>
            </li> -->

            <?php if (hasPermission('price_manage')): ?>
            <!-- Módulo de Tabela de Preços -->
            <li class="nav-item <?php echo $module == 'tabela_precos' ? 'active' : ''; ?>">
                <a class="nav-link" href="index.php?module=tabela_precos">
                    <i class="fas fa-fw fa-search-dollar"></i>
                    <span>Consulta de Preços</span>
                </a>
            </li>
            <?php endif; ?>
            <!-- Divisor -->
            <hr class="sidebar-divider">

            <!-- Cabeçalho - Configurações -->
            <!-- <div class="sidebar-heading">
                Configurações
            </div> -->

            <?php if (hasPermission('user_manage')): ?>
            <!-- Módulo de Usuários -->
            <li class="nav-item <?php echo $module == 'usuarios' ? 'active' : ''; ?>">
                <a class="nav-link" href="index.php?module=usuarios">
                    <i class="fas fa-fw fa-users-cog"></i>
                    <span>Usuários</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (hasPermission('role_manage')): ?>
            <!-- Módulo de Perfis (Permissões) -->
            <li class="nav-item <?php echo $module == 'perfis' ? 'active' : ''; ?>">
                <a class="nav-link" href="index.php?module=perfis">
                    <i class="fas fa-fw fa-id-card"></i>
                    <span>Permissões</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (hasPermission('appointment_view') || hasPermission('appointment_create')): ?>
            <!-- Módulo de Guias de Encaminhamento -->
            <li class="nav-item <?php echo $module == 'guias' ? 'active' : ''; ?>">
                <a class="nav-link" href="index.php?module=guias">
                    <i class="fas fa-fw fa-file-medical"></i>
                    <span>Guias de Encaminhamento</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (hasPermission('role_manage')): // Configurações restrito a admin ?>
            <!-- Módulo de Configurações -->
            <li class="nav-item <?php echo $module == 'configuracoes' ? 'active' : ''; ?>">
                <a class="nav-link" href="index.php?module=configuracoes">
                    <i class="fas fa-fw fa-cog"></i>
                    <span>Configurações</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (hasPermission('report_view')): ?>
            <!-- Módulo de Relatórios -->
            <li class="nav-item <?php echo $module == 'relatorios' ? 'active' : ''; ?>">
                <a class="nav-link" href="index.php?module=relatorios">
                    <i class="fas fa-fw fa-chart-line"></i>
                    <span>Relatórios</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (hasPermission('dashboard_realtime')): ?>
            <!-- Painel em Tempo Real -->
            <li class="nav-item <?php echo $module == 'dashboard_realtime' ? 'active' : ''; ?>">
                <a class="nav-link" href="index.php?module=dashboard_realtime">
                    <i class="fas fa-fw fa-chart-bar"></i>
                    <span>Painel Tempo Real</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (hasPermission('kanban_view')): ?>
            <!-- Módulo Kanban -->
            <li class="nav-item <?php echo $module == 'kanban' ? 'active' : ''; ?>">
                <a class="nav-link" href="index.php?module=kanban">
                    <i class="fas fa-fw fa-columns"></i>
                    <span>Kanban</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- Divisor -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sobre o Sistema -->
            <li class="nav-item <?php echo $module == 'sobre' ? 'active' : ''; ?>">
                <a class="nav-link" href="index.php?module=sobre">
                    <i class="fas fa-fw fa-info-circle"></i>
                    <span>Sobre</span>
                </a>
            </li>

            <!-- Botão para recolher a barra lateral -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>

        <!-- Conteúdo -->
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <!-- Barra superior -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <!-- Botão para recolher a barra lateral (versão móvel) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Barra de pesquisa -->
                    <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small" placeholder="Buscar..."
                                aria-label="Search" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Clock/Date Placeholder -->
                    <div class="d-none d-md-block mx-3 text-gray-600 small font-weight-bold topbar-clock" id="clock-date" style="text-align: right;"></div>

                    <!-- Itens da barra superior -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Item de pesquisa (versão móvel) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown de pesquisa -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Buscar..." aria-label="Search"
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>

                        <!-- Item de notificações -->
                        <!-- <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bell fa-fw"></i> -->
                        <!-- Contador de notificações -->
                        <!-- <span class="badge badge-danger badge-counter">3+</span> -->
                        <!-- </a> -->
                        <!-- Dropdown de notificações -->
                        <!-- <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="alertsDropdown">
                                <h6 class="dropdown-header">
                                    Central de Notificações
                                </h6>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-primary">
                                            <i class="fas fa-calendar-check text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">12 de abril, 2023</div>
                                        <span class="font-weight-bold">Novo agendamento para hoje!</span>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-success">
                                            <i class="fas fa-user-plus text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">11 de abril, 2023</div>
                                        Novo paciente cadastrado: Maria Silva
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-warning">
                                            <i class="fas fa-exclamation-triangle text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">10 de abril, 2023</div>
                                        Alerta: Agendamento cancelado
                                    </div>
                                </a>
                                <a class="dropdown-item text-center small text-gray-500" href="#">Ver todas as notificações</a>
                            </div>
                        </li> -->

                        <?php
                        // Carregar notificações
                        require_once MODULES_PATH . '/sistema/models/NotificacaoModel.php';
                        $notificacaoModel = new NotificacaoModel();
                        $notificacoes = $notificacaoModel->getNotificacoesRecentes(5);
                        $totalNotificacoes = $notificacaoModel->contarNotificacoesNaoLidas();
                        ?>

                        <!-- Item de notificações -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bell fa-fw"></i>
                                <!-- Contador de notificações -->
                                <?php if ($totalNotificacoes > 0): ?>
                                    <span class="badge badge-danger badge-counter">
                                        <?php echo $totalNotificacoes > 9 ? '9+' : $totalNotificacoes; ?>
                                    </span>
                                <?php endif; ?>
                            </a>
                            <!-- Dropdown de notificações -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="alertsDropdown">
                                <h6 class="dropdown-header">
                                    Central de Notificações
                                </h6>

                                <?php if (empty($notificacoes)): ?>
                                    <span class="dropdown-item text-center small text-gray-500">Nenhuma notificação disponível</span>
                                <?php else: ?>
                                    <?php foreach ($notificacoes as $notificacao): ?>
                                        <a class="dropdown-item d-flex align-items-center" href="<?php echo $notificacao['link']; ?>">
                                            <div class="mr-3">
                                                <div class="icon-circle bg-<?php echo $notificacao['cor']; ?>">
                                                    <i class="fas fa-<?php echo $notificacao['icone']; ?> text-white"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="small text-gray-500">
                                                    <?php echo $notificacaoModel->formatarDataNotificacao($notificacao['data_criacao']); ?>
                                                </div>
                                                <span class="font-weight-bold"><?php echo $notificacao['titulo']; ?></span>
                                                <?php if (!empty($notificacao['mensagem'])): ?>
                                                    <div class="small"><?php echo $notificacao['mensagem']; ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                                <a class="dropdown-item text-center small text-gray-500" href="index.php?module=sistema&action=notificacoes">
                                    Ver todas as notificações
                                </a>
                                <!-- Link para marcar todas como lidas
                                <a class="dropdown-item text-center small text-gray-500" href="index.php?module=sistema&action=notificacoes&acao=marcar_todas">
                                    Marcar todas como lida
                                </a> -->

                                <!-- Link para marcar todas como lidas com confirmação -->
                                <a class="dropdown-item text-center small text-gray-500" href="javascript:void(0);" onclick="confirmarMarcarTodas()">
                                    Marcar todas como lida
                                </a>

                                <script>
                                    function confirmarMarcarTodas() {
                                        if (confirm('Marcar todas as notificações como lidas?')) {
                                            window.location.href = 'index.php?module=sistema&action=notificacoes&acao=marcar_todas';
                                        }
                                    }
                                </script>


                                </script>
                            </div>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Item de usuário -->

                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo htmlspecialchars($_SESSION['usuario_nome'] ?? 'Usuário'); ?></span>
                                <?php
                                $userFoto = isset($_SESSION['usuario_foto']) && $_SESSION['usuario_foto'] ? 'uploads/usuarios/' . $_SESSION['usuario_foto'] : 'assents/img/user.png';
                                ?>
                                <img class="img-profile rounded-circle" src="<?php echo $userFoto; ?>" style="width: 32px; height: 32px; object-fit: cover;">
                            </a>

                            <!-- Dropdown de usuário -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="index.php?module=usuarios&action=profile">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Perfil
                                </a>
                                <a class="dropdown-item" href="index.php?module=configuracoes">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Configurações
                                </a>
                                <a class="dropdown-item" href="index.php?module=log">
                                    <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Log de Atividades
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Sair
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>

                <!-- Conteúdo da página -->
                <main>
                    <?php echo $content; ?>
                </main>
            </div>

            <!-- Rodapé -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; HSRB_SISTEMAS <?php echo date('Y'); ?></span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Botão para voltar ao topo -->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Modal de logout -->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Pronto para sair?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Selecione "Sair" abaixo se você está pronto para encerrar sua sessão atual.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
                    <a class="btn btn-primary" href="logout.php">Sair</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Botão WhatsApp flutuante -->
    <a href="https://wa.me/5577999882930?text=Olá,%20preciso%20de%20suporte%20no%20sistema." class="whatsapp-float" target="_blank">
        <img src="https://cdn.jsdelivr.net/npm/simple-icons@3.0.1/icons/whatsapp.svg" alt="WhatsApp" style="filter: invert(1);">
    </a>

    <!-- Bootstrap core JavaScript-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>

    <!-- JavaScript personalizado -->
    <script src="assents/js/scripts.js"></script>


    <!-- Chart.js JavaScript -->
    <script>
        function confirmarMarcarTodas() {
            if (confirm('Marcar todas as notificações como lidas?')) {
                // Fazer requisição AJAX para marcar todas como lidas
                $.ajax({
                    url: 'index.php?module=sistema&action=notificacoes&acao=marcar_todas&ajax=1',
                    type: 'GET',
                    success: function(response) {
                        // Limpar o conteúdo de notificações
                        $('.dropdown-list[aria-labelledby="alertsDropdown"] .dropdown-item:not(.text-center)').remove();

                        // Adicionar mensagem de "sem notificações"
                        $('.dropdown-list[aria-labelledby="alertsDropdown"] h6').after(
                            '<span class="dropdown-item text-center small text-gray-500">Não há novas notificações</span>'
                        );

                        // Remover o contador de notificações
                        $('.badge-counter').hide();
                    }
                });
            }
        }
    </script>


    <script>
        $(document).ready(function() {
            // Marcar todas como lidas via AJAX
            $(document).on('click', '#marcar-todas-link', function(e) {
                e.preventDefault();

                if (confirm('Marcar todas as notificações como lidas?')) {
                    $.ajax({
                        url: 'index.php?module=sistema&action=notificacoes&acao=marcar_todas&ajax=1',
                        type: 'GET',
                        success: function(response) {
                            $('.dropdown-list[aria-labelledby="alertsDropdown"] .dropdown-item:not(.text-center)').remove();
                            $('.dropdown-list[aria-labelledby="alertsDropdown"] h6').after(
                                '<span class="dropdown-item text-center small text-gray-500">Não há novas notificações</span>'
                            );
                            $('.badge-counter').hide();
                        },
                        error: function() {
                            alert('Erro ao marcar notificações como lidas. Tente novamente.');
                        }
                    });
                }
            });

            // Polling de notificações a cada 30 segundos
            setInterval(function() {
                $.ajax({
                    url: 'index.php?module=sistema&action=notificacoes&acao=contar&ajax=1',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        var badge = $('.badge-counter');
                        if (response.total > 0) {
                            var texto = response.total > 9 ? '9+' : response.total;
                            if (badge.length) {
                                badge.text(texto).show();
                            } else {
                                $('#alertsDropdown').append('<span class="badge badge-danger badge-counter">' + texto + '</span>');
                            }
                        } else {
                            badge.hide();
                        }
                    }
                });
            }, 30000);
        });
    </script>
</body>

</html>