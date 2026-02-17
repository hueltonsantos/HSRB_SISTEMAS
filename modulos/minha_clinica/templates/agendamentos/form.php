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
                                            <td colspan="3" class="text-center text-muted">Nenhum procedimento selecionado</td>
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
                                <option value="<?= $prof['id'] ?>"
                                    data-especialidade="<?= $prof['especialidade_id'] ?>"
                                    <?= ($agendamento['profissional_id'] ?? '') == $prof['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($prof['nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Data -->
                    <div class="col-md-4 mb-3">
                        <label class="font-weight-bold">Data <span class="text-danger">*</span></label>
                        <input type="date" name="data_consulta" class="form-control" value="<?= $agendamento['data_consulta'] ?? date('Y-m-d') ?>" required>
                    </div>

                    <!-- Hora -->
                    <div class="col-md-4 mb-3">
                        <label class="font-weight-bold">Hora <span class="text-danger">*</span></label>
                        <input type="time" name="hora_consulta" class="form-control" value="<?= isset($agendamento['hora_consulta']) ? substr($agendamento['hora_consulta'], 0, 5) : '' ?>" required>
                    </div>
                </div>

                <div class="row">
                    <!-- Forma de Pagamento -->
                    <div class="col-md-4 mb-3">
                        <label class="font-weight-bold">Forma de Pagamento</label>
                        <select name="forma_pagamento" class="form-control">
                            <option value="">Selecione</option>
                            <option value="Dinheiro" <?= ($agendamento['forma_pagamento'] ?? '') == 'Dinheiro' ? 'selected' : '' ?>>Dinheiro</option>
                            <option value="PIX" <?= ($agendamento['forma_pagamento'] ?? '') == 'PIX' ? 'selected' : '' ?>>PIX</option>
                            <option value="Cartao Debito" <?= ($agendamento['forma_pagamento'] ?? '') == 'Cartao Debito' ? 'selected' : '' ?>>Cartao Debito</option>
                            <option value="Cartao Credito" <?= ($agendamento['forma_pagamento'] ?? '') == 'Cartao Credito' ? 'selected' : '' ?>>Cartao Credito</option>
                            <option value="Convenio" <?= ($agendamento['forma_pagamento'] ?? '') == 'Convenio' ? 'selected' : '' ?>>Convenio</option>
                        </select>
                    </div>

                    <!-- Observacoes -->
                    <div class="col-md-8 mb-3">
                        <label class="font-weight-bold">Observacoes</label>
                        <textarea name="observacoes" class="form-control" rows="2"><?= htmlspecialchars($agendamento['observacoes'] ?? '') ?></textarea>
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
