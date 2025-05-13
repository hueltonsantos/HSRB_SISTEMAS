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
$module = isset($_GET['module']) ? $_GET['module'] : 'dashboard';

// Determina a ação a ser executada
$action = isset($_GET['action']) ? $_GET['action'] : 'list';

// Verifica se o módulo existe
$modulePath = MODULES_PATH . '/' . $module;
if (!file_exists($modulePath)) {
    $module = 'dashboard';
    $modulePath = MODULES_PATH . '/dashboard';
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
    <link href="/clinica/assents/css/style.css" rel="stylesheet">

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


</head>

<body id="page-top">
    <div id="wrapper">
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

            <!-- Cabeçalho - Módulos -->
            <div class="sidebar-heading">
                Módulos
            </div>

            <!-- Módulo de Pacientes -->
            <li class="nav-item <?php echo $module == 'pacientes' ? 'active' : ''; ?>">
                <a class="nav-link" href="index.php?module=pacientes">
                    <i class="fas fa-fw fa-user"></i>
                    <span>Pacientes</span>
                </a>
            </li>

            <!-- Módulo de Clínicas -->
            <li class="nav-item <?php echo $module == 'clinicas' ? 'active' : ''; ?>">
                <a class="nav-link" href="index.php?module=clinicas">
                    <i class="fas fa-fw fa-hospital"></i>
                    <span>Clínicas Parceiras</span>
                </a>
            </li>

            <!-- Módulo de Especialidades -->
            <li class="nav-item <?php echo $module == 'especialidades' ? 'active' : ''; ?>">
                <a class="nav-link" href="index.php?module=especialidades">
                    <i class="fas fa-fw fa-stethoscope"></i>
                    <span>Especialidades</span>
                </a>
            </li>

            <!-- Módulo de Agendamentos -->
            <li class="nav-item <?php echo $module == 'agendamentos' ? 'active' : ''; ?>">
                <a class="nav-link" href="index.php?module=agendamentos">
                    <i class="fas fa-fw fa-calendar-alt"></i>
                    <span>Agendamentos</span>
                </a>
            </li>
            <!-- Módulo de Procedimentos -->
            <!-- <li class="nav-item <?php echo $module == 'procedimentos' ? 'active' : ''; ?>">
                <a class="nav-link" href="index.php?module=procedimentos">
                    <i class="fas fa-fw fa-procedures"></i>
                    <span>Procedimentos</span>
                </a>
            </li> -->

            <!-- Módulo de Tabela de Preços -->
            <li class="nav-item <?php echo $module == 'tabela_precos' ? 'active' : ''; ?>">
                <a class="nav-link" href="index.php?module=tabela_precos">
                    <i class="fas fa-fw fa-search-dollar"></i>
                    <span>Consulta de Preços</span>
                </a>
            </li>
            <!-- Divisor -->
            <hr class="sidebar-divider">

            <!-- Cabeçalho - Configurações -->
            <!-- <div class="sidebar-heading">
                Configurações
            </div> -->

            <!-- Módulo de Usuários -->
            <!-- <li class="nav-item <?php echo $module == 'usuarios' ? 'active' : ''; ?>">
                <a class="nav-link" href="index.php?module=usuarios">
                    <i class="fas fa-fw fa-users-cog"></i>
                    <span>Usuários</span>
                </a>
            </li> -->

            <!-- Módulo de Configurações -->
            <!-- <li class="nav-item <?php echo $module == 'configuracoes' ? 'active' : ''; ?>">
                <a class="nav-link" href="index.php?module=configuracoes">
                    <i class="fas fa-fw fa-cog"></i>
                    <span>Configurações</span>
                </a>
            </li> -->

            <!-- Divisor -->
            <hr class="sidebar-divider d-none d-md-block">

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
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">Usuário Exemplo</span>
                                <img class="img-profile rounded-circle" src="assets/img/user.png">
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
    <a href="https://wa.me/5500000000000?text=Olá,%20preciso%20de%20suporte%20no%20sistema." class="whatsapp-float" target="_blank">
        <img src="https://cdn.jsdelivr.net/npm/simple-icons@3.0.1/icons/whatsapp.svg" alt="WhatsApp" style="filter: invert(1);">
    </a>

    <!-- Bootstrap core JavaScript-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>

    <!-- JavaScript personalizado -->
    <script src="/clinica/assents/js/scripts.js"></script>


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
            // Adiciona o evento de clique ao link "Marcar todas como lidas"
            $(document).on('click', '#marcar-todas-link', function(e) {
                e.preventDefault();

                if (confirm('Marcar todas as notificações como lidas?')) {
                    $.ajax({
                        url: 'index.php?module=sistema&action=notificacoes&acao=marcar_todas&ajax=1',
                        type: 'GET',
                        success: function(response) {
                            // Oculta todas as notificações do dropdown
                            $('.dropdown-list[aria-labelledby="alertsDropdown"] .dropdown-item:not(.text-center)').remove();

                            // Adiciona mensagem de "sem notificações"
                            $('.dropdown-list[aria-labelledby="alertsDropdown"] h6').after(
                                '<span class="dropdown-item text-center small text-gray-500">Não há novas notificações</span>'
                            );

                            // Remove o contador
                            $('.badge-counter').hide();
                        },
                        error: function() {
                            alert('Erro ao marcar notificações como lidas. Tente novamente.');
                        }
                    });
                }
            });
        });
    </script>
</body>

</html>