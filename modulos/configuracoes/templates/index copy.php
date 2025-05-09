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
            <a href="index.php?modulo=configuracoes&action=editar" class="btn btn-primary btn-sm">
                <i class="fas fa-edit"></i> Editar Configurações
            </a>
        </div>
        <div class="card-body">
            <!-- Abas para diferentes tipos de configurações -->
            <ul class="nav nav-tabs" id="configTabs" role="tablist">
                <?php $primeiro = true; ?>
                <?php foreach ($configPorTipo as $tipo => $configs): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $primeiro ? 'active' : ''; ?>" 
                           id="<?php echo $tipo; ?>-tab" 
                           data-toggle="tab" 
                           href="#<?php echo $tipo; ?>" 
                           role="tab" 
                           aria-controls="<?php echo $tipo; ?>" 
                           aria-selected="<?php echo $primeiro ? 'true' : 'false'; ?>">
                            <?php 
                            switch ($tipo) {
                                case 'texto':
                                    echo 'Geral';
                                    break;
                                case 'arquivo':
                                    echo 'Arquivos';
                                    break;
                                case 'numero':
                                    echo 'Numéricos';
                                    break;
                                case 'booleano':
                                    echo 'Opções';
                                    break;
                                case 'hora':
                                    echo 'Horários';
                                    break;
                                case 'lista':
                                    echo 'Listas';
                                    break;
                                default:
                                    echo ucfirst($tipo);
                            }
                            ?>
                        </a>
                    </li>
                    <?php $primeiro = false; ?>
                <?php endforeach; ?>
            </ul>
            
            <div class="tab-content p-3" id="configTabsContent">
                <?php $primeiro = true; ?>
                <?php foreach ($configPorTipo as $tipo => $configs): ?>
                    <div class="tab-pane fade <?php echo $primeiro ? 'show active' : ''; ?>" 
                         id="<?php echo $tipo; ?>" 
                         role="tabpanel" 
                         aria-labelledby="<?php echo $tipo; ?>-tab">
                        
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Descrição</th>
                                        <th>Valor</th>
                                        <th>Última Atualização</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($configs as $config): ?>
                                        <tr>
                                            <td><?php echo $config['descricao']; ?></td>
                                            <td>
                                                <?php if ($tipo == 'booleano'): ?>
                                                    <?php if ($config['valor'] == '1'): ?>
                                                        <span class="badge badge-success">Sim</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-danger">Não</span>
                                                    <?php endif; ?>
                                                <?php elseif ($tipo == 'arquivo' && !empty($config['valor'])): ?>
                                                    <img src="uploads/<?php echo $config['valor']; ?>" alt="<?php echo $config['descricao']; ?>" style="max-height: 50px;">
                                                <?php elseif ($tipo == 'lista'): ?>
                                                    <?php 
                                                    $valores = explode(',', $config['valor']);
                                                    $diasSemana = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado', 'Domingo'];
                                                    $textoDias = [];
                                                    
                                                    foreach ($valores as $dia) {
                                                        if (isset($diasSemana[$dia - 1])) {
                                                            $textoDias[] = $diasSemana[$dia - 1];
                                                        }
                                                    }
                                                    
                                                    echo implode(', ', $textoDias);
                                                    ?>
                                                <?php else: ?>
                                                    <?php echo $config['valor']; ?>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($config['data_atualizacao'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php $primeiro = false; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>