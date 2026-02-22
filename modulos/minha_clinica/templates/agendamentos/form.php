<div class="container-fluid">
    <!-- Titulo -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-calendar-plus text-primary"></i> <?= $titulo ?>
        </h1>
        <a href="index.php?module=minha_clinica&action=agendamentos" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>

    <!-- Formulario -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="POST" action="<?= $actionUrl ?>" id="formAgendamento">
                <?php if (!empty($agendamento['id'])): ?>
                    <input type="hidden" name="id" value="<?= $agendamento['id'] ?>">
                <?php endif; ?>

                <div class="row">
                    <!-- Paciente -->
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold">Paciente <span class="text-danger">*</span></label>
                        <select name="paciente_id" class="form-control select2" required>
                            <option value="">Selecione o paciente</option>
                            <?php foreach ($pacientes as $pac): ?>
                                <option value="<?= $pac['id'] ?>" <?= ($agendamento['paciente_id'] ?? '') == $pac['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($pac['nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Especialidade -->
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold">Especialidade <span class="text-danger">*</span></label>
                        <select name="especialidade_id" id="especialidade_id" class="form-control" required>
                            <option value="">Selecione a especialidade</option>
                            <?php foreach ($especialidades as $esp): ?>
                                <option value="<?= $esp['id'] ?>" <?= ($agendamento['especialidade_id'] ?? '') == $esp['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($esp['nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Procedimentos (Multiplos) -->
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="font-weight-bold">Procedimentos</label>
                        <div class="card p-3 bg-light">
                            <div class="row">
                                <div class="col-md-10 mb-2 mb-md-0">
                                    <select class="form-control" id="procedimento_select">
                                        <option value="">Selecione uma especialidade primeiro</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-success btn-block" id="btn_add_procedimento">
                                        <i class="fas fa-plus"></i> Adicionar
                                    </button>
                                </div>
                            </div>

                            <div class="table-responsive mt-3">
                                <table class="table table-bordered table-striped bg-white" id="tabela_procedimentos">
                                    <thead>
                                        <tr>
                                            <th>Procedimento</th>
                                            <th width="150">Valor (R$)</th>
                                            <th width="50">Acao</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr id="linha_vazia">
<<<<<<< HEAD
                                            <td colspan="3" class="text-center text-muted">Nenhum procedimento
                                                selecionado</td>
=======
                                            <td colspan="3" class="text-center text-muted">Nenhum procedimento selecionado</td>
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr class="font-weight-bold">
                                            <td class="text-right">TOTAL:</td>
                                            <td id="valor_total_display">R$ 0,00</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <!-- Inputs ocultos para envio -->
                            <div id="procedimentos_container"></div>
                            <input type="hidden" name="valor_total" id="valor_total_input" value="0.00">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Profissional -->
                    <div class="col-md-4 mb-3">
                        <label class="font-weight-bold">Profissional</label>
                        <select name="profissional_id" id="profissional_id" class="form-control">
                            <option value="">Selecione o profissional</option>
                            <?php foreach ($profissionais as $prof): ?>
<<<<<<< HEAD
                                <option value="<?= $prof['id'] ?>" data-especialidade="<?= $prof['especialidade_id'] ?>"
=======
                                <option value="<?= $prof['id'] ?>"
                                    data-especialidade="<?= $prof['especialidade_id'] ?>"
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
                                    <?= ($agendamento['profissional_id'] ?? '') == $prof['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($prof['nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Data -->
                    <div class="col-md-4 mb-3">
                        <label class="font-weight-bold">Data <span class="text-danger">*</span></label>
<<<<<<< HEAD
                        <input type="date" name="data_consulta" class="form-control"
                            value="<?= $agendamento['data_consulta'] ?? date('Y-m-d') ?>" required>
=======
                        <input type="date" name="data_consulta" class="form-control" value="<?= $agendamento['data_consulta'] ?? date('Y-m-d') ?>" required>
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
                    </div>

                    <!-- Hora -->
                    <div class="col-md-4 mb-3">
                        <label class="font-weight-bold">Hora <span class="text-danger">*</span></label>
<<<<<<< HEAD
                        <input type="time" name="hora_consulta" class="form-control"
                            value="<?= isset($agendamento['hora_consulta']) ? substr($agendamento['hora_consulta'], 0, 5) : '' ?>"
                            required>
=======
                        <input type="time" name="hora_consulta" class="form-control" value="<?= isset($agendamento['hora_consulta']) ? substr($agendamento['hora_consulta'], 0, 5) : '' ?>" required>
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
                    </div>
                </div>

                <div class="row">
<<<<<<< HEAD
                    <!-- Convênio e Guia -->
                    <div class="col-md-4 mb-3">
                        <label class="font-weight-bold">Convênio</label>
                        <select name="convenio_id" id="convenio_id" class="form-control">
                            <option value="">Particular</option>
                            <?php foreach ($convenios as $conv): ?>
                                <option value="<?= $conv['id'] ?>" <?= ($agendamento['convenio_id'] ?? '') == $conv['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($conv['nome_fantasia']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3" id="div_guia" style="display: none;">
                        <label class="font-weight-bold">Nº Guia / Carteirinha</label>
                        <input type="text" name="numero_guia" class="form-control"
                            value="<?= htmlspecialchars($agendamento['numero_guia'] ?? '') ?>" placeholder="Opcional">
                    </div>

                    <!-- Forma de Pagamento -->
                    <div class="col-md-4 mb-3" id="div_forma_pagamento">
=======
                    <!-- Forma de Pagamento -->
                    <div class="col-md-4 mb-3">
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
                        <label class="font-weight-bold">Forma de Pagamento</label>
                        <select name="forma_pagamento" class="form-control">
                            <option value="">Selecione</option>
                            <option value="Dinheiro" <?= ($agendamento['forma_pagamento'] ?? '') == 'Dinheiro' ? 'selected' : '' ?>>Dinheiro</option>
<<<<<<< HEAD
                            <option value="PIX" <?= ($agendamento['forma_pagamento'] ?? '') == 'PIX' ? 'selected' : '' ?>>
                                PIX</option>
                            <option value="Cartao Debito" <?= ($agendamento['forma_pagamento'] ?? '') == 'Cartao Debito' ? 'selected' : '' ?>>Cartao Debito</option>
                            <option value="Cartao Credito" <?= ($agendamento['forma_pagamento'] ?? '') == 'Cartao Credito' ? 'selected' : '' ?>>Cartao Credito</option>
=======
                            <option value="PIX" <?= ($agendamento['forma_pagamento'] ?? '') == 'PIX' ? 'selected' : '' ?>>PIX</option>
                            <option value="Cartao Debito" <?= ($agendamento['forma_pagamento'] ?? '') == 'Cartao Debito' ? 'selected' : '' ?>>Cartao Debito</option>
                            <option value="Cartao Credito" <?= ($agendamento['forma_pagamento'] ?? '') == 'Cartao Credito' ? 'selected' : '' ?>>Cartao Credito</option>
                            <option value="Convenio" <?= ($agendamento['forma_pagamento'] ?? '') == 'Convenio' ? 'selected' : '' ?>>Convenio</option>
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
                        </select>
                    </div>

                    <!-- Observacoes -->
                    <div class="col-md-8 mb-3">
                        <label class="font-weight-bold">Observacoes</label>
<<<<<<< HEAD
                        <textarea name="observacoes" class="form-control"
                            rows="2"><?= htmlspecialchars($agendamento['observacoes'] ?? '') ?></textarea>
=======
                        <textarea name="observacoes" class="form-control" rows="2"><?= htmlspecialchars($agendamento['observacoes'] ?? '') ?></textarea>
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
                    </div>
                </div>

                <hr>

                <div class="text-right">
                    <a href="index.php?module=minha_clinica&action=agendamentos" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar Agendamento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
<<<<<<< HEAD
    $(document).ready(function () {
        // Select2 para pacientes
        if ($.fn.select2) {
            $('.select2').select2({
                placeholder: 'Selecione...',
                allowClear: true,
                width: '100%'
            });
        }

        // Variavel para armazenar procedimentos selecionados
        var procedimentosSelecionados = [];

        // Carregar procedimentos ao mudar especialidade ou Convenio
        $('#especialidade_id, #convenio_id').change(function () {
            var espId = $('#especialidade_id').val();
            var convId = $('#convenio_id').val();
            var $procSelect = $('#procedimento_select');
            var $prof = $('#profissional_id');

            // Mostrar/Ocultar campo de guia e forma de pagamento
            if (convId) {
                $('#div_guia').show();
                $('#div_forma_pagamento').hide();
                $('select[name="forma_pagamento"]').val('');
            } else {
                $('#div_guia').hide();
                $('#div_forma_pagamento').show();
            }

            $procSelect.html('<option value="">Carregando...</option>');

            if (espId) {
                var url = 'index.php?module=minha_clinica&action=api&api_action=get_procedimentos&especialidade_id=' + espId;
                if (convId) {
                    url += '&convenio_id=' + convId;
                }

                $.get(url, function (data) {
                    var options = '<option value="">Selecione um procedimento</option>';
                    if (data && data.length > 0) {
                        data.forEach(function (p) {
                            var valorFormatado = parseFloat(p.valor).toLocaleString('pt-BR', { minimumFractionDigits: 2 });
                            var nomeExibicao = p.procedimento;
                            if (p.codigo) {
                                nomeExibicao += ' (' + p.codigo + ')';
                            }
                            options += '<option value="' + p.id + '" data-valor="' + p.valor + '" data-nome="' + nomeExibicao + '">' + nomeExibicao + ' - R$ ' + valorFormatado + '</option>';
                        });
                    }
                    $procSelect.html(options);
                });

                // Filtrar profissionais (mantido)
                $prof.find('option').each(function () {
                    var profEsp = $(this).data('especialidade');
                    if (profEsp && profEsp != espId) {
                        $(this).hide();
                    } else {
                        $(this).show();
                    }
                });
            } else {
                $procSelect.html('<option value="">Selecione uma especialidade primeiro</option>');
                $prof.find('option').show();
            }
        });

        // Trigger inicial para exibir campo de guia se convênio estiver selecionado e ocultar forma de pagamento
        if ($('#convenio_id').val()) {
            $('#div_guia').show();
            $('#div_forma_pagamento').hide();
        }

        // Adicionar procedimento a tabela
        $('#btn_add_procedimento').click(function () {
            var select = $('#procedimento_select option:selected');
            var id = select.val();
            var nome = select.data('nome');
            var valor = parseFloat(select.data('valor'));

            if (!id) {
                alert('Selecione um procedimento para adicionar.');
                return;
            }

            // Verifica se ja existe
            if (procedimentosSelecionados.find(p => p.id == id)) {
                alert('Este procedimento ja foi adicionado.');
                return;
            }

            procedimentosSelecionados.push({ id: id, nome: nome, valor: valor });
            atualizarTabelaProcedimentos();
        });

        // Atualizar tabela e total
        function atualizarTabelaProcedimentos() {
            var tbody = $('#tabela_procedimentos tbody');
            var container = $('#procedimentos_container');
            var total = 0;

            tbody.empty();
            container.empty();

            if (procedimentosSelecionados.length === 0) {
                tbody.html('<tr id="linha_vazia"><td colspan="3" class="text-center text-muted">Nenhum procedimento selecionado</td></tr>');
            } else {
                procedimentosSelecionados.forEach(function (proc, index) {
                    total += proc.valor;
                    var valorFormatado = proc.valor.toLocaleString('pt-BR', { minimumFractionDigits: 2 });

                    tbody.append(`
=======
$(document).ready(function() {
    // Select2 para pacientes
    if ($.fn.select2) {
        $('.select2').select2({
            placeholder: 'Selecione...',
            allowClear: true,
            width: '100%'
        });
    }

    // Variavel para armazenar procedimentos selecionados
    var procedimentosSelecionados = [];

    // Carregar procedimentos ao mudar especialidade
    $('#especialidade_id').change(function() {
        var espId = $(this).val();
        var $procSelect = $('#procedimento_select');
        var $prof = $('#profissional_id');

        $procSelect.html('<option value="">Carregando...</option>');

        if (espId) {
            $.get('index.php?module=minha_clinica&action=api&api_action=get_procedimentos&especialidade_id=' + espId, function(data) {
                var options = '<option value="">Selecione um procedimento</option>';
                if (data && data.length > 0) {
                    data.forEach(function(p) {
                        var valorFormatado = parseFloat(p.valor).toLocaleString('pt-BR', { minimumFractionDigits: 2 });
                        options += '<option value="' + p.id + '" data-valor="' + p.valor + '" data-nome="' + p.procedimento + '">' + p.procedimento + ' - R$ ' + valorFormatado + '</option>';
                    });
                }
                $procSelect.html(options);
            });

            // Filtrar profissionais
            $prof.find('option').each(function() {
                var profEsp = $(this).data('especialidade');
                if (profEsp && profEsp != espId) {
                    $(this).hide();
                } else {
                    $(this).show();
                }
            });
        } else {
            $procSelect.html('<option value="">Selecione uma especialidade primeiro</option>');
            $prof.find('option').show();
        }
    });

    // Adicionar procedimento a tabela
    $('#btn_add_procedimento').click(function() {
        var select = $('#procedimento_select option:selected');
        var id = select.val();
        var nome = select.data('nome');
        var valor = parseFloat(select.data('valor'));

        if (!id) {
            alert('Selecione um procedimento para adicionar.');
            return;
        }

        // Verifica se ja existe
        if (procedimentosSelecionados.find(p => p.id == id)) {
            alert('Este procedimento ja foi adicionado.');
            return;
        }

        procedimentosSelecionados.push({ id: id, nome: nome, valor: valor });
        atualizarTabelaProcedimentos();
    });

    // Atualizar tabela e total
    function atualizarTabelaProcedimentos() {
        var tbody = $('#tabela_procedimentos tbody');
        var container = $('#procedimentos_container');
        var total = 0;

        tbody.empty();
        container.empty();

        if (procedimentosSelecionados.length === 0) {
            tbody.html('<tr id="linha_vazia"><td colspan="3" class="text-center text-muted">Nenhum procedimento selecionado</td></tr>');
        } else {
            procedimentosSelecionados.forEach(function(proc, index) {
                total += proc.valor;
                var valorFormatado = proc.valor.toLocaleString('pt-BR', { minimumFractionDigits: 2 });

                tbody.append(`
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
                    <tr data-id="${proc.id}">
                        <td>${proc.nome}</td>
                        <td>R$ ${valorFormatado}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-sm btn-remover-proc" data-id="${proc.id}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `);

<<<<<<< HEAD
                    container.append(`<input type="hidden" name="procedimentos[]" value="${proc.id}">`);
                });
            }

            var totalFormatado = total.toLocaleString('pt-BR', { minimumFractionDigits: 2 });
            $('#valor_total_display').text('R$ ' + totalFormatado);
            $('#valor_total_input').val(total.toFixed(2));
        }

        // Remover procedimento
        $(document).on('click', '.btn-remover-proc', function () {
            var id = $(this).data('id');
            procedimentosSelecionados = procedimentosSelecionados.filter(p => p.id != id);
            atualizarTabelaProcedimentos();
        });

        // Trigger para carregar procedimentos se especialidade ja selecionada
        if ($('#especialidade_id').val()) {
            $('#especialidade_id').trigger('change');
        }
    });
</script>
=======
                container.append(`<input type="hidden" name="procedimentos[]" value="${proc.id}">`);
            });
        }

        var totalFormatado = total.toLocaleString('pt-BR', { minimumFractionDigits: 2 });
        $('#valor_total_display').text('R$ ' + totalFormatado);
        $('#valor_total_input').val(total.toFixed(2));
    }

    // Remover procedimento
    $(document).on('click', '.btn-remover-proc', function() {
        var id = $(this).data('id');
        procedimentosSelecionados = procedimentosSelecionados.filter(p => p.id != id);
        atualizarTabelaProcedimentos();
    });

    // Trigger para carregar procedimentos se especialidade ja selecionada
    if ($('#especialidade_id').val()) {
        $('#especialidade_id').trigger('change');
    }
});
</script>
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
