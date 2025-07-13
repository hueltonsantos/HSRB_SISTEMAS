<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><?php echo $pageTitle; ?></h1>

    <?php if (isset($_SESSION['mensagem'])): ?>
        <div class="alert alert-<?php echo $_SESSION['mensagem']['tipo']; ?> alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['mensagem']['texto']; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php unset($_SESSION['mensagem']); ?>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Dados do Agendamento</h6>
        </div>
        <div class="card-body">
            <form action="index.php?module=agendamentos&action=save" method="post" id="agendamentoForm">
                <!-- ID oculto para edição -->
                <?php if (isset($formData['id'])): ?>
                    <input type="hidden" name="id" value="<?php echo $formData['id']; ?>">
                <?php endif; ?>

                <div class="row">
                    <!-- Paciente -->
                    <div class="col-md-6 form-group">
                        <label for="paciente_id">Paciente</label>
                        <div class="input-group">
                            <input type="text" class="form-control <?php echo isset($formErrors['paciente_id']) ? 'is-invalid' : ''; ?>"
                                id="paciente_nome" name="paciente_nome" value="<?php echo isset($formData['paciente_nome']) ? htmlspecialchars($formData['paciente_nome']) : ''; ?>"
                                placeholder="Pesquisar paciente..." required>
                            <input type="hidden" id="paciente_id" name="paciente_id" value="<?php echo isset($formData['paciente_id']) ? $formData['paciente_id'] : ''; ?>">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" data-toggle="modal" data-target="#pacienteModal">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <?php if (isset($formErrors['paciente_id'])): ?>
                            <div class="invalid-feedback d-block">
                                <?php echo $formErrors['paciente_id']; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Clínica -->
                    <div class="col-md-6 form-group">
                        <label for="clinica_id">Clínica</label>
                        <select class="form-control <?php echo isset($formErrors['clinica_id']) ? 'is-invalid' : ''; ?>"
                            id="clinica_id" name="clinica_id" required>
                            <option value="">Selecione uma clínica</option>
                            <?php foreach ($clinicas as $clinica): ?>
                                <option value="<?php echo $clinica['id']; ?>" <?php echo (isset($formData['clinica_id']) && $formData['clinica_id'] == $clinica['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($clinica['nome']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($formErrors['clinica_id'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $formErrors['clinica_id']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row">
                    <!-- Especialidade -->
                    <div class="col-md-6 form-group">
                        <label for="especialidade_id">Especialidade</label>
                        <select class="form-control <?php echo isset($formErrors['especialidade_id']) ? 'is-invalid' : ''; ?>"
                            id="especialidade_id" name="especialidade_id" required>
                            <option value="">Selecione primeiro uma clínica</option>
                            <?php if (!empty($especialidades)): ?>
                                <?php foreach ($especialidades as $especialidade): ?>
                                    <option value="<?php echo $especialidade['id']; ?>" <?php echo (isset($formData['especialidade_id']) && $formData['especialidade_id'] == $especialidade['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($especialidade['nome']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <?php if (isset($formErrors['especialidade_id'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $formErrors['especialidade_id']; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Procedimento -->
                    <div class="col-md-6 form-group">
                        <label for="procedimento_id">Procedimento</label>
                        <select class="form-control <?php echo isset($formErrors['procedimento_id']) ? 'is-invalid' : ''; ?>" 
                            id="procedimento_id" name="procedimento_id" required>
                            <option value="">Selecione uma especialidade primeiro</option>
                            <?php if (!empty($procedimentos)): ?>
                                <?php foreach ($procedimentos as $proc): ?>
                                    <option value="<?php echo $proc['id']; ?>" <?php echo (isset($formData['procedimento_id']) && $formData['procedimento_id'] == $proc['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($proc['procedimento']); ?>
                                        <?php if (isset($proc['valor'])): ?>
                                            - R$ <?php echo number_format($proc['valor'], 2, ',', '.'); ?>
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <?php if (isset($formErrors['procedimento_id'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $formErrors['procedimento_id']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row">
                    <!-- Status do Agendamento -->
                    <div class="col-md-6 form-group">
                        <label for="status_agendamento">Status do Agendamento</label>
                        <select class="form-control <?php echo isset($formErrors['status_agendamento']) ? 'is-invalid' : ''; ?>"
                            id="status_agendamento" name="status_agendamento">
                            <?php foreach ($statusAgendamento as $key => $value): ?>
                                <option value="<?php echo $key; ?>" <?php echo (isset($formData['status_agendamento']) && $formData['status_agendamento'] == $key) ? 'selected' : ''; ?>>
                                    <?php echo $value; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($formErrors['status_agendamento'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $formErrors['status_agendamento']; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Data da Consulta -->
                    <div class="col-md-6 form-group">
                        <label for="data_consulta">Data da Consulta</label>
                        <input type="text" class="form-control datepicker <?php echo isset($formErrors['data_consulta']) ? 'is-invalid' : ''; ?>"
                            id="data_consulta" name="data_consulta" value="<?php echo isset($formData['data_consulta']) ? htmlspecialchars($formData['data_consulta']) : ''; ?>"
                            required>
                        <?php if (isset($formErrors['data_consulta'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $formErrors['data_consulta']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row">
                    <!-- Hora da Consulta -->
                    <div class="col-md-6 form-group">
                        <label for="hora_consulta">Hora da Consulta</label>
                        <select class="form-control <?php echo isset($formErrors['hora_consulta']) ? 'is-invalid' : ''; ?>"
                            id="hora_consulta" name="hora_consulta" required>
                            <option value="">Selecione primeiro uma data</option>
                            <?php if (isset($formData['hora_consulta']) && !empty($formData['hora_consulta'])): ?>
                                <option value="<?php echo $formData['hora_consulta']; ?>" selected>
                                    <?php echo substr($formData['hora_consulta'], 0, 5); ?>
                                </option>
                            <?php endif; ?>
                        </select>
                        <?php if (isset($formErrors['hora_consulta'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $formErrors['hora_consulta']; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Observações -->
                    <div class="col-md-6 form-group">
                        <label for="observacoes">Observações</label>
                        <textarea class="form-control <?php echo isset($formErrors['observacoes']) ? 'is-invalid' : ''; ?>"
                            id="observacoes" name="observacoes" rows="3"><?php echo isset($formData['observacoes']) ? htmlspecialchars($formData['observacoes']) : ''; ?></textarea>
                        <?php if (isset($formErrors['observacoes'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $formErrors['observacoes']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary">Salvar</button>
                    <a href="index.php?module=agendamentos&action=list" class="btn btn-secondary">Cancelar</a>
                </div>
                
                <!-- DEBUG - Remover após testes -->
                <div class="form-group mt-2 border-top pt-3" style="background-color: #f8f9fa; padding: 15px; border-radius: 5px;">
                    <h6 class="text-muted">🔧 Debug - Testes dos Endpoints</h6>
                    <button type="button" class="btn btn-sm btn-info" onclick="testarEspecialidades()">Testar Especialidades</button>
                    <button type="button" class="btn btn-sm btn-info" onclick="testarProcedimentos()">Testar Procedimentos</button>
                    <button type="button" class="btn btn-sm btn-warning" onclick="limparConsole()">Limpar Console</button>
                    <div id="debug-result" class="mt-2" style="max-height: 200px; overflow-y: auto;"></div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Busca de Pacientes -->
<div class="modal fade" id="pacienteModal" tabindex="-1" role="dialog" aria-labelledby="pacienteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pacienteModalLabel">Buscar Paciente</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="input-group">
                        <input type="text" class="form-control" id="busca_paciente" placeholder="Digite o nome ou CPF do paciente">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button" id="btn_buscar_paciente">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                        </div>
                    </div>
                </div>

                <div id="resultado_busca" class="mt-3">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="tabela_pacientes">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>CPF</th>
                                    <th>Telefone</th>
                                    <th>Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pacientes as $paciente): ?>
                                    <tr>
                                        <td><?php echo $paciente['id']; ?></td>
                                        <td><?php echo htmlspecialchars($paciente['nome']); ?></td>
                                        <td><?php echo htmlspecialchars($paciente['cpf']); ?></td>
                                        <td><?php echo htmlspecialchars($paciente['celular']); ?></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary btn-selecionar-paciente"
                                                data-id="<?php echo $paciente['id']; ?>"
                                                data-nome="<?php echo htmlspecialchars($paciente['nome']); ?>">
                                                Selecionar
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <a href="index.php?module=pacientes&action=new" class="btn btn-success" target="_blank">
                    <i class="fas fa-plus"></i> Novo Paciente
                </a>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    console.log('Inicializando formulário de agendamento...');

    // Inicializa o datepicker
    $('.datepicker').datepicker({
        format: 'dd/mm/yyyy',
        language: 'pt-BR',
        autoclose: true,
        todayHighlight: true,
        startDate: new Date()
    });

    // Função para carregar especialidades
    function carregarEspecialidades(clinicaId) {
        console.log('Carregando especialidades para clínica:', clinicaId);
        
        if (!clinicaId) {
            $('#especialidade_id').html('<option value="">Selecione primeiro uma clínica</option>').prop('disabled', true);
            $('#procedimento_id').html('<option value="">Selecione uma especialidade primeiro</option>').prop('disabled', true);
            return;
        }

        $('#especialidade_id').html('<option value="">Carregando...</option>').prop('disabled', true);
        $('#procedimento_id').html('<option value="">Selecione uma especialidade primeiro</option>').prop('disabled', true);

        $.ajax({
            url: 'index.php?module=agendamentos&action=get_especialidades',
            type: 'POST',
            data: { clinica_id: clinicaId },
            dataType: 'json',
            success: function(response) {
                console.log('Resposta especialidades:', response);
                
                var options = '<option value="">Selecione uma especialidade</option>';
                
                // Verifica se a resposta tem a estrutura esperada
                var especialidades = [];
                if (response.success && response.data) {
                    especialidades = response.data;
                } else if (Array.isArray(response)) {
                    especialidades = response;
                }
                
                if (especialidades && especialidades.length > 0) {
                    $.each(especialidades, function(index, especialidade) {
                        options += '<option value="' + especialidade.id + '">' + especialidade.nome + '</option>';
                    });
                }
                
                $('#especialidade_id').html(options).prop('disabled', false);
                
                // Se já tiver uma especialidade selecionada previamente
                <?php if (isset($formData['especialidade_id']) && !empty($formData['especialidade_id'])): ?>
                    $('#especialidade_id').val('<?php echo $formData['especialidade_id']; ?>');
                    carregarProcedimentos('<?php echo $formData['especialidade_id']; ?>');
                <?php endif; ?>
            },
            error: function(xhr, status, error) {
                console.error('Erro ao carregar especialidades:', error);
                console.error('Resposta:', xhr.responseText);
                $('#especialidade_id').html('<option value="">Erro ao carregar</option>').prop('disabled', true);
            }
        });
    }

    // Função para carregar procedimentos
    function carregarProcedimentos(especialidadeId) {
        console.log('=== INICIANDO CARREGAMENTO DE PROCEDIMENTOS ===');
        console.log('Especialidade ID:', especialidadeId);
        
        if (!especialidadeId) {
            console.log('Nenhuma especialidade fornecida');
            $('#procedimento_id').html('<option value="">Selecione uma especialidade primeiro</option>').prop('disabled', true);
            return;
        }

        $('#procedimento_id').html('<option value="">Carregando procedimentos...</option>').prop('disabled', true);

        // URL de debug
        var url = 'index.php?module=agendamentos&action=get_procedimentos&especialidade_id=' + especialidadeId + '&debug=1';
        console.log('URL da requisição:', url);

        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                console.log('=== RESPOSTA RECEBIDA ===');
                console.log('Tipo da resposta:', typeof data);
                console.log('Dados recebidos:', data);
                
                var options = '<option value="">Selecione um procedimento</option>';
                
                // Verifica se há erro na resposta
                if (data.error) {
                    console.error('Erro retornado pelo servidor:', data.error);
                    $('#procedimento_id').html('<option value="">Erro: ' + data.error + '</option>').prop('disabled', true);
                    return;
                }
                
                // Verifica se é um array válido
                if (Array.isArray(data) && data.length > 0) {
                    console.log('Processando', data.length, 'procedimentos');
                    
                    $.each(data, function(index, proc) {
                        console.log('Processando procedimento:', proc);
                        
                        var valor = parseFloat(proc.valor || 0).toLocaleString('pt-BR', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                        
                        var optionText = proc.procedimento + ' - R$ ' + valor;
                        options += '<option value="' + proc.id + '">' + optionText + '</option>';
                    });
                    
                    $('#procedimento_id').html(options).prop('disabled', false);
                    console.log('Procedimentos carregados com sucesso!');
                    
                    // Se já tiver um procedimento selecionado previamente
                    <?php if (isset($formData['procedimento_id']) && !empty($formData['procedimento_id'])): ?>
                        console.log('Selecionando procedimento pré-definido:', '<?php echo $formData['procedimento_id']; ?>');
                        $('#procedimento_id').val('<?php echo $formData['procedimento_id']; ?>');
                    <?php endif; ?>
                    
                } else {
                    console.log('Nenhum procedimento encontrado ou dados inválidos');
                    $('#procedimento_id').html('<option value="">Nenhum procedimento encontrado</option>').prop('disabled', true);
                }
            },
            error: function(xhr, status, error) {
                console.error('=== ERRO NA REQUISIÇÃO AJAX ===');
                console.error('Status:', status);
                console.error('Erro:', error);
                console.error('Status Code:', xhr.status);
                console.error('Response Text:', xhr.responseText);
                
                var errorMsg = 'Erro ao carregar procedimentos';
                if (xhr.status === 404) {
                    errorMsg = 'Endpoint não encontrado (404)';
                } else if (xhr.status === 500) {
                    errorMsg = 'Erro interno do servidor (500)';
                }
                
                $('#procedimento_id').html('<option value="">' + errorMsg + '</option>').prop('disabled', true);
            }
        });
    }

    // Função para carregar horários
    function carregarHorarios() {
        var dataConsulta = $('#data_consulta').val();
        var clinicaId = $('#clinica_id').val();
        var especialidadeId = $('#especialidade_id').val();

        if (!dataConsulta || !clinicaId || !especialidadeId) {
            $('#hora_consulta').html('<option value="">Preencha todos os campos anteriores</option>').prop('disabled', true);
            return;
        }

        $('#hora_consulta').html('<option value="">Carregando horários...</option>').prop('disabled', true);

        $.ajax({
            url: 'index.php?module=agendamentos&action=get_horarios',
            type: 'POST',
            data: {
                data_consulta: dataConsulta,
                clinica_id: clinicaId,
                especialidade_id: especialidadeId,
                <?php if (isset($formData['id'])): ?>
                    agendamento_id: <?php echo $formData['id']; ?>
                <?php endif; ?>
            },
            dataType: 'json',
            success: function(data) {
                var options = '<option value="">Selecione um horário</option>';
                
                if (data && data.length > 0) {
                    $.each(data, function(index, horario) {
                        var horarioFormatado = horario.substring(0, 5);
                        options += '<option value="' + horario + '">' + horarioFormatado + '</option>';
                    });
                    $('#hora_consulta').html(options).prop('disabled', false);
                    
                    // Se já tiver um horário selecionado previamente
                    <?php if (isset($formData['hora_consulta']) && !empty($formData['hora_consulta'])): ?>
                        $('#hora_consulta').val('<?php echo $formData['hora_consulta']; ?>');
                    <?php endif; ?>
                } else {
                    $('#hora_consulta').html('<option value="">Nenhum horário disponível</option>').prop('disabled', true);
                }
            },
            error: function(xhr, status, error) {
                console.error('Erro ao carregar horários:', error);
                $('#hora_consulta').html('<option value="">Erro ao carregar horários</option>').prop('disabled', true);
            }
        });
    }

    // Events
    $('#clinica_id').change(function() {
        var clinicaId = $(this).val();
        carregarEspecialidades(clinicaId);
        
        // Limpa campos dependentes
        $('#data_consulta').val('').prop('disabled', !clinicaId);
        $('#hora_consulta').html('<option value="">Selecione primeiro uma data</option>').prop('disabled', true);
    });

    $('#especialidade_id').change(function() {
        var especialidadeId = $(this).val();
        carregarProcedimentos(especialidadeId);
        
        // Ativa/desativa campo de data
        $('#data_consulta').prop('disabled', !especialidadeId);
        if (!especialidadeId) {
            $('#data_consulta').val('');
            $('#hora_consulta').html('<option value="">Selecione primeiro uma data</option>').prop('disabled', true);
        }
    });

    $('#data_consulta').change(function() {
        carregarHorarios();
    });

    // Busca pacientes no modal
    $('#btn_buscar_paciente').click(function() {
        var termo = $('#busca_paciente').val();
        
        if (termo.length >= 3) {
            $('#tabela_pacientes tbody tr').hide();
            $('#tabela_pacientes tbody tr').each(function() {
                var nome = $(this).find('td:eq(1)').text().toLowerCase();
                var cpf = $(this).find('td:eq(2)').text().toLowerCase();
                
                if (nome.indexOf(termo.toLowerCase()) >= 0 || cpf.indexOf(termo.toLowerCase()) >= 0) {
                    $(this).show();
                }
            });
        } else {
            $('#tabela_pacientes tbody tr').show();
        }
    });

    $('#busca_paciente').keypress(function(e) {
        if (e.which == 13) {
            e.preventDefault();
            $('#btn_buscar_paciente').click();
        }
    });

    // Seleciona um paciente
    $('.btn-selecionar-paciente').click(function() {
        var id = $(this).data('id');
        var nome = $(this).data('nome');
        
        $('#paciente_id').val(id);
        $('#paciente_nome').val(nome);
        $('#pacienteModal').modal('hide');
    });

    // Validação do formulário
    $('#agendamentoForm').submit(function(e) {
        var valid = true;
        
        // Remove classes de erro anteriores
        $('.form-control').removeClass('is-invalid');
        
        // Validações
        if (!$('#paciente_id').val()) {
            $('#paciente_nome').addClass('is-invalid');
            valid = false;
        }
        
        if (!$('#clinica_id').val()) {
            $('#clinica_id').addClass('is-invalid');
            valid = false;
        }
        
        if (!$('#especialidade_id').val()) {
            $('#especialidade_id').addClass('is-invalid');
            valid = false;
        }
        
        if (!$('#procedimento_id').val()) {
            $('#procedimento_id').addClass('is-invalid');
            valid = false;
        }
        
        if (!$('#data_consulta').val()) {
            $('#data_consulta').addClass('is-invalid');
            valid = false;
        }
        
        if (!$('#hora_consulta').val()) {
            $('#hora_consulta').addClass('is-invalid');
            valid = false;
        }

        if (!valid) {
            alert('Por favor, preencha todos os campos obrigatórios.');
        }
        
        return valid;
    });

    // Carrega dados iniciais se já houver clínica selecionada
    <?php if (isset($formData['clinica_id']) && !empty($formData['clinica_id'])): ?>
        carregarEspecialidades('<?php echo $formData['clinica_id']; ?>');
    <?php endif; ?>

    // Se houver data e especialidade, carrega horários
    <?php if (isset($formData['data_consulta']) && !empty($formData['data_consulta']) && isset($formData['especialidade_id']) && !empty($formData['especialidade_id'])): ?>
        setTimeout(function() {
            carregarHorarios();
        }, 1000);
    <?php endif; ?>
});

// === FUNÇÕES DE DEBUG ===
function limparConsole() {
    console.clear();
    document.getElementById('debug-result').innerHTML = '<small class="text-muted">Console limpo</small>';
}

function testarEspecialidades() {
    var clinicaId = document.getElementById('clinica_id').value;
    if (!clinicaId) {
        alert('Selecione uma clínica primeiro');
        return;
    }
    
    console.log('=== TESTE MANUAL - ESPECIALIDADES ===');
    document.getElementById('debug-result').innerHTML = '<small class="text-info">Testando especialidades... Veja o console.</small>';
    
    $.ajax({
        url: 'index.php?module=agendamentos&action=get_especialidades',
        type: 'POST',
        data: { clinica_id: clinicaId },
        dataType: 'json',
        success: function(data) {
            console.log('✅ Sucesso - Especialidades:', data);
            document.getElementById('debug-result').innerHTML = 
                '<small class="text-success">✅ Sucesso! ' + 
                (data.data ? data.data.length : (Array.isArray(data) ? data.length : 0)) + 
                ' especialidades encontradas. Veja o console.</small>';
        },
        error: function(xhr, status, error) {
            console.error('❌ Erro - Especialidades:', {status, error, response: xhr.responseText});
            document.getElementById('debug-result').innerHTML = 
                '<small class="text-danger">❌ Erro: ' + status + ' - ' + error + '</small>';
        }
    });
}

function testarProcedimentos() {
    var especialidadeId = document.getElementById('especialidade_id').value;
    if (!especialidadeId) {
        alert('Selecione uma especialidade primeiro');
        return;
    }
    
    console.log('=== TESTE MANUAL - PROCEDIMENTOS ===');
    document.getElementById('debug-result').innerHTML = '<small class="text-info">Testando procedimentos... Veja o console.</small>';
    
    $.ajax({
        url: 'index.php?module=agendamentos&action=get_procedimentos',
        type: 'GET',
        data: { especialidade_id: especialidadeId },
        dataType: 'json',
        success: function(data) {
            console.log('✅ Sucesso - Procedimentos:', data);
            document.getElementById('debug-result').innerHTML = 
                '<small class="text-success">✅ Sucesso! ' + 
                (Array.isArray(data) ? data.length : 0) + 
                ' procedimentos encontrados. Veja o console.</small>';
        },
        error: function(xhr, status, error) {
            console.error('❌ Erro - Procedimentos:', {status, error, response: xhr.responseText});
            document.getElementById('debug-result').innerHTML = 
                '<small class="text-danger">❌ Erro: ' + status + ' - ' + error + '</small>';
        }
    });
}
</script>