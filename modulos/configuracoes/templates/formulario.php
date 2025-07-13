<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Editar Configurações</h1>
    
    <?php if (isset($_SESSION['erro'])): ?>
        <div class="alert alert-danger">
            <?php 
            echo $_SESSION['erro']; 
            unset($_SESSION['erro']);
            ?>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Editar configurações do sistema</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="index.php?modulo=configuracoes&action=salvar" enctype="multipart/form-data">
                <!-- Abas para diferentes tipos de configurações -->
                <ul class="nav nav-tabs" id="configTabs" role="tablist">
                    <?php $primeiro = true; ?>
                    <?php foreach ($configPorTipo as $tipo => $configs): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $primeiro ? 'active' : ''; ?>" 
                               id="edit-<?php echo $tipo; ?>-tab" 
                               data-toggle="tab" 
                               href="#edit-<?php echo $tipo; ?>" 
                               role="tab" 
                               aria-controls="edit-<?php echo $tipo; ?>" 
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
                             id="edit-<?php echo $tipo; ?>" 
                             role="tabpanel" 
                             aria-labelledby="edit-<?php echo $tipo; ?>-tab">
                            
                            <?php foreach ($configs as $config): ?>
                                <div class="form-group">
                                    <label for="<?php echo $config['chave']; ?>">
                                        <?php echo $config['descricao']; ?>
                                    </label>
                                    
                                    <?php if ($tipo == 'texto'): ?>
                                        <input type="text" class="form-control" id="<?php echo $config['chave']; ?>" name="<?php echo $config['chave']; ?>" value="<?php echo $config['valor']; ?>">
                                    
                                    <?php elseif ($tipo == 'numero'): ?>
                                        <input type="number" class="form-control" id="<?php echo $config['chave']; ?>" name="<?php echo $config['chave']; ?>" value="<?php echo $config['valor']; ?>">
                                    
                                    <?php elseif ($tipo == 'booleano'): ?>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="<?php echo $config['chave']; ?>" name="<?php echo $config['chave']; ?>" value="1" <?php echo $config['valor'] == '1' ? 'checked' : ''; ?>>
                                            <label class="custom-control-label" for="<?php echo $config['chave']; ?>">
                                                <?php echo $config['valor'] == '1' ? 'Sim' : 'Não'; ?>
                                            </label>
                                        </div>
                                    
                                    <?php elseif ($tipo == 'arquivo'): ?>
                                        <?php if (!empty($config['valor'])): ?>
                                            <div class="mb-2">
                                                <img src="uploads/<?php echo $config['valor']; ?>" alt="<?php echo $config['descricao']; ?>" style="max-height: 100px;">
                                            </div>
                                        <?php endif; ?>
                                        <input type="file" class="form-control-file" id="<?php echo $config['chave']; ?>" name="<?php echo $config['chave']; ?>">
                                        <small class="form-text text-muted">Deixe em branco para manter o arquivo atual.</small>
                                    
                                    <?php elseif ($tipo == 'hora'): ?>
                                        <input type="time" class="form-control" id="<?php echo $config['chave']; ?>" name="<?php echo $config['chave']; ?>" value="<?php echo $config['valor']; ?>">
                                    
                                    <?php elseif ($tipo == 'lista'): ?>
                                        <?php if ($config['chave'] == 'dias_atendimento'): ?>
                                            <?php $diasSelecionados = explode(',', $config['valor']); ?>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="dia-1" name="dias_atendimento[]" value="1" <?php echo in_array('1', $diasSelecionados) ? 'checked' : ''; ?>>
                                                <label class="custom-control-label" for="dia-1">Segunda-feira</label>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="dia-2" name="dias_atendimento[]" value="2" <?php echo in_array('2', $diasSelecionados) ? 'checked' : ''; ?>>
                                                <label class="custom-control-label" for="dia-2">Terça-feira</label>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="dia-3" name="dias_atendimento[]" value="3" <?php echo in_array('3', $diasSelecionados) ? 'checked' : ''; ?>>
                                                <label class="custom-control-label" for="dia-3">Quarta-feira</label>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="dia-4" name="dias_atendimento[]" value="4" <?php echo in_array('4', $diasSelecionados) ? 'checked' : ''; ?>>
                                                <label class="custom-control-label" for="dia-4">Quinta-feira</label>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="dia-5" name="dias_atendimento[]" value="5" <?php echo in_array('5', $diasSelecionados) ? 'checked' : ''; ?>>
                                                <label class="custom-control-label" for="dia-5">Sexta-feira</label>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="dia-6" name="dias_atendimento[]" value="6" <?php echo in_array('6', $diasSelecionados) ? 'checked' : ''; ?>>
                                                <label class="custom-control-label" for="dia-6">Sábado</label>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="dia-7" name="dias_atendimento[]" value="7" <?php echo in_array('7', $diasSelecionados) ? 'checked' : ''; ?>>
                                                <label class="custom-control-label" for="dia-7">Domingo</label>
                                            </div>
                                        <?php else: ?>
                                            <input type="text" class="form-control" id="<?php echo $config['chave']; ?>" name="<?php echo $config['chave']; ?>" value="<?php echo $config['valor']; ?>">
                                            <small class="form-text text-muted">Use vírgula para separar os valores.</small>
                                        <?php endif; ?>
                                    
                                    <?php else: ?>
                                        <input type="text" class="form-control" id="<?php echo $config['chave']; ?>" name="<?php echo $config['chave']; ?>" value="<?php echo $config['valor']; ?>">
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php $primeiro = false; ?>
                    <?php endforeach; ?>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">Salvar Configurações</button>
                        <a href="index.php?modulo=configuracoes&action=index" class="btn btn-secondary">Cancelar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Para os checkboxes de dias da semana
    const diasCheckboxes = document.querySelectorAll('input[name="dias_atendimento[]"]');
    
    if (diasCheckboxes.length > 0) {
        diasCheckboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                // Concatena os valores selecionados em um campo hidden
                const diasSelecionados = [];
                diasCheckboxes.forEach(function(cb) {
                    if (cb.checked) {
                        diasSelecionados.push(cb.value);
                    }
                });
                
                // Cria ou atualiza o campo hidden
                let hiddenField = document.getElementById('dias_atendimento');
                if (!hiddenField) {
                    hiddenField = document.createElement('input');
                    hiddenField.type = 'hidden';
                    hiddenField.id = 'dias_atendimento';
                    hiddenField.name = 'dias_atendimento';
                    document.querySelector('form').appendChild(hiddenField);
                }
                
                hiddenField.value = diasSelecionados.join(',');
            });
        });
    }
});
</script>