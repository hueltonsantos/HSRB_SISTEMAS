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
                            id="especialidade_id" name="especialidade_id" required <?php echo empty($formData['clinica_id']) ? 'disabled' : ''; ?>>
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
                </div>
                
                <div class="row">
                    <!-- Data da Consulta -->
                    <div class="col-md-6 form-group">
                        <label for="data_consulta">Data da Consulta</label>
                        <input type="text" class="form-control datepicker <?php echo isset($formErrors['data_consulta']) ? 'is-invalid' : ''; ?>" 
                            id="data_consulta" name="data_consulta" value="<?php echo isset($formData['data_consulta']) ? htmlspecialchars($formData['data_consulta']) : ''; ?>" 
                            required <?php echo (empty($formData['clinica_id']) || empty($formData['especialidade_id'])) ? 'disabled' : ''; ?>>
                        <?php if (isset($formErrors['data_consulta'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $formErrors['data_consulta']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Hora da Consulta -->
                    <div class="col-md-6 form-group">
                        <label for="hora_consulta">Hora da Consulta</label>
                        <select class="form-control <?php echo isset($formErrors['hora_consulta']) ? 'is-invalid' : ''; ?>" 
                            id="hora_consulta" name="hora_consulta" required
                            <?php echo (empty($formData['clinica_id']) || empty($formData['especialidade_id']) || empty($formData['data_consulta'])) ? 'disabled' : ''; ?>>
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
                </div>
                
                <div class="row">
                    <!-- Observações -->
                    <div class="col-md-12 form-group">
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
$(document).ready(function(){
    // Inicializa o datepicker
    $('.datepicker').datepicker({
        format: 'dd/mm/yyyy',
        language: 'pt-BR',
        autoclose: true,
        todayHighlight: true,
        startDate: new Date() // Permite apenas datas a partir de hoje
    });
    
    // Carrega especialidades quando seleciona uma clínica
    $('#clinica_id').change(function() {
        var clinicaId = $(this).val();
        
        if (clinicaId) {
            // Ativa os campos dependentes
            $('#especialidade_id').prop('disabled', false);
            
            // Limpa os campos relacionados
            $('#especialidade_id').html('<option value="">Carregando...</option>');
            $('#data_consulta').val('').prop('disabled', true);
            $('#hora_consulta').html('<option value="">Selecione primeiro uma data</option>').prop('disabled', true);
            
            // Busca as especialidades da clínica selecionada
            $.ajax({
                url: 'index.php?module=agendamentos&action=get_especialidades',
                type: 'POST',
                data: {clinica_id: clinicaId},
                dataType: 'json',
                success: function(data) {
                    var options = '<option value="">Selecione uma especialidade</option>';
                    
                    $.each(data, function(index, especialidade) {
                        options += '<option value="' + especialidade.id + '">' + especialidade.nome + '</option>';
                    });
                    
                    $('#especialidade_id').html(options);
                    
                    // Se já tiver uma especialidade selecionada previamente (em caso de edição)
                    <?php if (isset($formData['especialidade_id']) && !empty($formData['especialidade_id'])): ?>
                        $('#especialidade_id').val('<?php echo $formData['especialidade_id']; ?>');
                        $('#data_consulta').prop('disabled', false);
                    <?php endif; ?>
                }
            });
        } else {
            // Desativa os campos dependentes
            $('#especialidade_id').html('<option value="">Selecione primeiro uma clínica</option>').prop('disabled', true);
            $('#data_consulta').val('').prop('disabled', true);
            $('#hora_consulta').html('<option value="">Selecione primeiro uma data</option>').prop('disabled', true);
        }
    });
    
    // Ativa o campo de data quando seleciona uma especialidade
    $('#especialidade_id').change(function() {
        var especialidadeId = $(this).val();
        
        if (especialidadeId) {
            // Ativa o campo de data
            $('#data_consulta').prop('disabled', false);
            
            // Limpa os campos relacionados
            $('#data_consulta').val('');
            $('#hora_consulta').html('<option value="">Selecione primeiro uma data</option>').prop('disabled', true);
        } else {
            // Desativa os campos dependentes
            $('#data_consulta').val('').prop('disabled', true);
            $('#hora_consulta').html('<option value="">Selecione primeiro uma data</option>').prop('disabled', true);
        }
    });
    
    // Carrega horários disponíveis quando seleciona uma data
    $('#data_consulta').change(function() {
        var dataConsulta = $(this).val();
        var clinicaId = $('#clinica_id').val();
        var especialidadeId = $('#especialidade_id').val();
        
        if (dataConsulta && clinicaId && especialidadeId) {
            // Ativa o campo de hora
            $('#hora_consulta').prop('disabled', false);
            
            // Limpa o campo
            $('#hora_consulta').html('<option value="">Carregando horários...</option>');
            
            // Busca os horários disponíveis
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
                    
                    $.each(data, function(index, horario) {
                        // Formata o horário para exibição (remove os segundos)
                        var horarioFormatado = horario.substring(0, 5);
                        options += '<option value="' + horario + '">' + horarioFormatado + '</option>';
                    });
                    
                    $('#hora_consulta').html(options);
                    
                    // Se já tiver um horário selecionado previamente (em caso de edição)
                    <?php if (isset($formData['hora_consulta']) && !empty($formData['hora_consulta'])): ?>
                        $('#hora_consulta').val('<?php echo $formData['hora_consulta']; ?>');
                    <?php endif; ?>
                }
            });
        } else {
            // Desativa o campo de hora
            $('#hora_consulta').html('<option value="">Selecione primeiro uma data</option>').prop('disabled', true);
        }
    });
    
    // Busca pacientes no modal
    $('#btn_buscar_paciente').click(function() {
        var termo = $('#busca_paciente').val();
        
        if (termo.length >= 3) {
            // Aqui você pode implementar uma busca AJAX para filtrar os pacientes
            // Por simplicidade, vamos apenas filtrar os que já estão na tabela
            $('#tabela_pacientes tbody tr').hide();
            $('#tabela_pacientes tbody tr').each(function() {
                var nome = $(this).find('td:eq(1)').text().toLowerCase();
                var cpf = $(this).find('td:eq(2)').text().toLowerCase();
                
                if (nome.indexOf(termo.toLowerCase()) >= 0 || cpf.indexOf(termo.toLowerCase()) >= 0) {
                    $(this).show();
                }
            });
        } else {
            // Mostra todos os pacientes
            $('#tabela_pacientes tbody tr').show();
        }
    });
    
    // Evento de tecla Enter no campo de busca
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
        
        // Validar paciente
        if ($('#paciente_id').val() === '') {
            $('#paciente_nome').addClass('is-invalid');
            valid = false;
        } else {
            $('#paciente_nome').removeClass('is-invalid');
        }
        
        // Validar clínica
        if ($('#clinica_id').val() === '') {
            $('#clinica_id').addClass('is-invalid');
            valid = false;
        } else {
            $('#clinica_id').removeClass('is-invalid');
        }
        
        // Validar especialidade
        if ($('#especialidade_id').val() === '') {
            $('#especialidade_id').addClass('is-invalid');
            valid = false;
        } else {
            $('#especialidade_id').removeClass('is-invalid');
        }
        
        // Validar data da consulta
        if ($('#data_consulta').val() === '') {
            $('#data_consulta').addClass('is-invalid');
            valid = false;
        } else {
            $('#data_consulta').removeClass('is-invalid');
        }
        
        // Validar hora da consulta
        if ($('#hora_consulta').val() === '') {
            $('#hora_consulta').addClass('is-invalid');
            valid = false;
        } else {
            $('#hora_consulta').removeClass('is-invalid');
        }
        
        return valid;
    });
    
    // Dispara o evento change para carregar dados pré-selecionados (em caso de edição)
    <?php if (isset($formData['clinica_id']) && !empty($formData['clinica_id'])): ?>
        $('#clinica_id').trigger('change');
    <?php endif; ?>
    
    <?php if (isset($formData['data_consulta']) && !empty($formData['data_consulta']) && 
              isset($formData['especialidade_id']) && !empty($formData['especialidade_id'])): ?>
        $('#data_consulta').trigger('change');
    <?php endif; ?>
});

// Função para carregar os horários disponíveis
$(document).ready(function() {
    // Função para carregar especialidades de uma clínica
    function carregarEspecialidades(clinicaId) {
        console.log("Carregando especialidades para clínica ID:", clinicaId);
        
        // Mostra "Carregando..."
        $("#especialidade_id").html('<option value="">Carregando...</option>');
        
        if (!clinicaId) {
            $("#especialidade_id").html('<option value="">Selecione uma clínica primeiro</option>');
            return;
        }
        
        // Faz a requisição AJAX para buscar especialidades
        $.ajax({
            url: 'index.php?module=agendamentos&action=get_especialidades',
            type: 'GET',
            dataType: 'json',
            data: {
                clinica_id: clinicaId
            },
            success: function(response) {
                console.log("Resposta recebida:", response);
                
                if (response.success) {
                    // Limpa e preenche o dropdown de especialidades
                    var options = '<option value="">Selecione uma especialidade</option>';
                    
                    if (response.data && response.data.length > 0) {
                        $.each(response.data, function(index, especialidade) {
                            options += '<option value="' + especialidade.id + '">' + especialidade.nome + '</option>';
                        });
                        
                        console.log("Especialidades carregadas com sucesso:", response.data.length);
                    } else {
                        options = '<option value="">Nenhuma especialidade disponível para esta clínica</option>';
                        console.log("Nenhuma especialidade encontrada para esta clínica");
                    }
                    
                    $("#especialidade_id").html(options);
                    
                    // Se houver uma especialidade pré-selecionada
                    <?php if (isset($formData['especialidade_id'])): ?>
                    $("#especialidade_id").val(<?php echo $formData['especialidade_id']; ?>);
                    <?php endif; ?>
                } else {
                    console.error("Erro na resposta:", response.message);
                    $("#especialidade_id").html('<option value="">Erro: ' + response.message + '</option>');
                }
            },
            error: function(xhr, status, error) {
                console.error("Erro na requisição AJAX:", error);
                console.error("Status:", status);
                console.error("Resposta:", xhr.responseText);
                
                $("#especialidade_id").html('<option value="">Erro na conexão</option>');
            }
        });
    }
    
    // Evento quando a clínica é alterada
    $("#clinica_id").change(function() {
        var clinicaId = $(this).val();
        console.log("Clínica selecionada:", clinicaId);
        carregarEspecialidades(clinicaId);
    });
    
    // Carrega especialidades inicialmente se houver uma clínica selecionada
    var initialClinicaId = $("#clinica_id").val();
    if (initialClinicaId) {
        console.log("Clínica inicial:", initialClinicaId);
        carregarEspecialidades(initialClinicaId);
    }
});
</script>