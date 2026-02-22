<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Acesso Restrito - Minha Clínica</title>

    <!-- Fontes e ícones -->
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">

    <!-- Bootstrap e estilos personalizados -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assents/css/style.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-lg border-0 rounded-lg border-left-primary">
                <div class="card-body text-center p-5">
                    <!-- Ícones Animados/Ilustrativos -->
                    <div class="mb-4">
                        <span class="fa-stack fa-3x">
                            <i class="fas fa-circle fa-stack-2x text-primary"></i>
                            <i class="fas fa-user-lock fa-stack-1x fa-inverse"></i>
                        </span>
                    </div>

                    <h1 class="h3 font-weight-bold text-gray-800 mb-2">Acesso Restrito</h1>
                    <h4 class="h5 text-gray-600 font-weight-normal mb-4">Perfil Profissional Não Detectado</h4>

                    <p class="text-muted mb-4">
                        Seu usuário não possui um vínculo ativo com um cadastro de profissional neste sistema.<br>
                        Para acessar o painel de atendimento, é necessário este vínculo.
                    </p>

                    <!-- Box de Diagnóstico -->
                    <div class="alert alert-light border border-primary rounded text-left p-3 mb-4"
                        style="background-color: #f8f9fc;">
                        <h6 class="font-weight-bold text-primary mb-2" style="font-size: 0.9rem;"><i
                                class="fas fa-info-circle"></i> O QUE FAZER?</h6>
                        <ul class="list-unstyled mb-0 small text-muted pl-1">
                            <li class="mb-2"><i class="fas fa-check text-primary mr-2"></i> Solicite ao administrador
                                para vincular seu usuário ao seu cadastro de profissional.</li>
                            <li><i class="fas fa-check text-primary mr-2"></i> Verifique se seu cadastro de profissional
                                está "Ativo".</li>
                        </ul>
                    </div>

                    <!-- Ações -->
                    <div class="d-grid gap-2">
                        <a href="index.php?module=dashboard" class="btn btn-primary btn-lg btn-block shadow-sm">
                            <i class="fas fa-tachometer-alt mr-2"></i> Ir para o Dashboard
                        </a>
                        <a href="javascript:history.back()" class="btn btn-light btn-sm btn-block text-muted mt-3">
                            <i class="fas fa-arrow-left mr-1"></i> Voltar
                        </a>
                    </div>

                    <div class="mt-4 pt-3 border-top">
                        <p class="mb-0 text-muted small">
                            HSRB Sistemas &copy; <?= date('Y') ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>