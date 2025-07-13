<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Configurações do Sistema</h1>
    <p class="mb-4">Visualize as configurações atuais do sistema</p>

    <?php if (isset($_SESSION['sucesso'])): ?>
        <div class="alert alert-success">
            <?php 
            echo $_SESSION['sucesso']; 
            unset($_SESSION['sucesso']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['erro'])): ?>
        <div class="alert alert-danger">
            <?php 
            echo $_SESSION['erro']; 
            unset($_SESSION['erro']);
            ?>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Configurações</h6>
            <a href="index.php?module=configuracoes&action=editar" class="btn btn-primary btn-sm">
                <i class="fas fa-edit"></i> Editar Configurações
            </a>
        </div>
        <div class="card-body">
            <!-- Abas para diferentes tipos de configurações -->
            <ul class="nav nav-tabs" id="configTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="geral-tab" data-toggle="tab" href="#geral" role="tab" aria-controls="geral" aria-selected="true">
                        Geral
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="horarios-tab" data-toggle="tab" href="#horarios" role="tab" aria-controls="horarios" aria-selected="false">
                        Horários
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="numericos-tab" data-toggle="tab" href="#numericos" role="tab" aria-controls="numericos" aria-selected="false">
                        Numéricos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="opcoes-tab" data-toggle="tab" href="#opcoes" role="tab" aria-controls="opcoes" aria-selected="false">
                        Opções
                    </a>
                </li>
            </ul>
            
            <div class="tab-content p-3" id="configTabsContent">
                <!-- Aba Geral -->
                <div class="tab-pane fade show active" id="geral" role="tabpanel" aria-labelledby="geral-tab">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="30%">Descrição</th>
                                    <th>Valor</th>
                                    <th width="20%">Última Atualização</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($configs_geral)): foreach ($configs_geral as $config): ?>
                                <tr>
                                    <td><?php echo $config['descricao']; ?></td>
                                    <td><?php echo $config['valor']; ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($config['data_atualizacao'])); ?></td>
                                </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Aba Horários -->
                <div class="tab-pane fade" id="horarios" role="tabpanel" aria-labelledby="horarios-tab">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="30%">Descrição</th>
                                    <th>Valor</th>
                                    <th width="20%">Última Atualização</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($configs_horarios)): foreach ($configs_horarios as $config): ?>
                                <tr>
                                    <td><?php echo $config['descricao']; ?></td>
                                    <td><?php echo $config['valor']; ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($config['data_atualizacao'])); ?></td>
                                </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Aba Numéricos -->
                <div class="tab-pane fade" id="numericos" role="tabpanel" aria-labelledby="numericos-tab">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="30%">Descrição</th>
                                    <th>Valor</th>
                                    <th width="20%">Última Atualização</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($configs_numericos)): foreach ($configs_numericos as $config): ?>
                                <tr>
                                    <td><?php echo $config['descricao']; ?></td>
                                    <td><?php echo $config['valor']; ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($config['data_atualizacao'])); ?></td>
                                </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Aba Opções -->
                <div class="tab-pane fade" id="opcoes" role="tabpanel" aria-labelledby="opcoes-tab">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="30%">Descrição</th>
                                    <th>Valor</th>
                                    <th width="20%">Última Atualização</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($configs_opcoes)): foreach ($configs_opcoes as $config): ?>
                                <tr>
                                    <td><?php echo $config['descricao']; ?></td>
                                    <td>
                                        <?php if ($config['valor'] == '1'): ?>
                                            <span class="badge badge-success">Sim</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Não</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($config['data_atualizacao'])); ?></td>
                                </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>