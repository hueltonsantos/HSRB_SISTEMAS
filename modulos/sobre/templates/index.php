<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Sobre o Sistema</h1>

    <div class="row">
        <!-- Card principal -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-clinic-medical text-primary" style="font-size: 64px;"></i>
                    </div>
                    <h2 class="text-primary font-weight-bold">HSRB SISTEMAS</h2>
                    <p class="text-muted mb-1">Sistema de Gestao de Clinicas Medicas</p>
                    <span class="badge badge-primary px-3 py-2 mb-4" style="font-size: 14px;">
                        v<?php echo htmlspecialchars($sistemaVersao); ?>
                    </span>

                    <hr class="my-4" style="max-width: 400px; margin: 0 auto;">

                    <p class="text-gray-600 mt-4" style="max-width: 600px; margin: 0 auto;">
                        Sistema open source de gestao de clinicas medicas, desenvolvido para facilitar
                        o agendamento de consultas, gerenciamento de pacientes, clinicas e especialidades
                        medicas, alem de permitir a geracao de guias para procedimentos medicos.
                    </p>
                </div>
            </div>

            <!-- Funcionalidades -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Funcionalidades</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="fas fa-check-circle text-success mr-2"></i> Agendamento de consultas</li>
                                <li class="mb-2"><i class="fas fa-check-circle text-success mr-2"></i> Cadastro de pacientes</li>
                                <li class="mb-2"><i class="fas fa-check-circle text-success mr-2"></i> Gestao de clinicas parceiras</li>
                                <li class="mb-2"><i class="fas fa-check-circle text-success mr-2"></i> Especialidades medicas</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="fas fa-check-circle text-success mr-2"></i> Geracao de guias</li>
                                <li class="mb-2"><i class="fas fa-check-circle text-success mr-2"></i> Tabela de precos</li>
                                <li class="mb-2"><i class="fas fa-check-circle text-success mr-2"></i> Controle de permissoes</li>
                                <li class="mb-2"><i class="fas fa-check-circle text-success mr-2"></i> Relatorios e logs</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card de contato -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-headset mr-1"></i> Suporte & Contato</h6>
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="fas fa-user-circle text-gray-400" style="font-size: 80px;"></i>
                        <h5 class="mt-3 font-weight-bold text-gray-800">Huelton Santos</h5>
                        <p class="text-muted small">Desenvolvedor</p>
                    </div>

                    <!-- WhatsApp -->
                    <a href="https://wa.me/5577999882930" target="_blank" class="btn btn-success btn-block mb-3" style="border-radius: 8px;">
                        <i class="fab fa-whatsapp mr-2" style="font-size: 18px;"></i> (77) 99988-2930
                    </a>

                    <!-- Email -->
                    <a href="mailto:hueltonti@gmail.com" class="btn btn-danger btn-block mb-3" style="border-radius: 8px;">
                        <i class="fas fa-envelope mr-2"></i> hueltonti@gmail.com
                    </a>

                    <!-- GitHub -->
                    <a href="https://github.com/hueltonsantos/HSRB_SISTEMAS" target="_blank" class="btn btn-dark btn-block mb-3" style="border-radius: 8px;">
                        <i class="fab fa-github mr-2"></i> GitHub do Projeto
                    </a>

                    <hr>
                    <p class="text-muted small mb-0">
                        Duvidas, sugestoes ou problemas?<br>
                        Entre em contato por qualquer canal acima.
                    </p>
                </div>
            </div>

            <!-- Tecnologias -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-code mr-1"></i> Tecnologias</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap">
                        <span class="badge badge-info m-1 px-2 py-1">PHP</span>
                        <span class="badge badge-info m-1 px-2 py-1">MySQL</span>
                        <span class="badge badge-info m-1 px-2 py-1">Bootstrap 4</span>
                        <span class="badge badge-info m-1 px-2 py-1">jQuery</span>
                        <span class="badge badge-info m-1 px-2 py-1">DataTables</span>
                        <span class="badge badge-info m-1 px-2 py-1">Chart.js</span>
                        <span class="badge badge-info m-1 px-2 py-1">Font Awesome</span>
                    </div>
                </div>
            </div>

            <!-- Licenca -->
            <div class="card shadow mb-4">
                <div class="card-body text-center">
                    <i class="fas fa-balance-scale text-gray-400 mb-2" style="font-size: 24px;"></i>
                    <p class="mb-1 font-weight-bold text-gray-700">Open Source</p>
                    <p class="text-muted small mb-0">&copy; <?php echo $sistemaAno; ?> HSRB SISTEMAS</p>
                </div>
            </div>
        </div>
    </div>
</div>
