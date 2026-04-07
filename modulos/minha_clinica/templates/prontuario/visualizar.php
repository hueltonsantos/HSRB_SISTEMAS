<div class="container-fluid">

    <!-- Cabeçalho -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 text-gray-800 mb-0">
                <i class="fas fa-notes-medical text-primary mr-2"></i> Prontuário
            </h1>
            <small class="text-muted">
                <a href="index.php?module=pacientes&action=view&id=<?= $paciente['id'] ?>">
                    <?= htmlspecialchars($paciente['nome']) ?>
                </a>
            </small>
        </div>
        <div>
            <a href="index.php?module=pacientes&action=view&id=<?= $paciente['id'] ?>"
               class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <!-- Dados do Paciente -->
    <div class="card shadow mb-4 border-left-primary">
        <div class="card-body py-3">
            <div class="row">
                <div class="col-md-4">
                    <p class="mb-0"><strong><?= htmlspecialchars($paciente['nome']) ?></strong></p>
                    <small class="text-muted">
                        <?= $idade ?> anos &bull;
                        <?= $paciente['sexo'] == 'M' ? 'Masculino' : ($paciente['sexo'] == 'F' ? 'Feminino' : 'Outro') ?>
                        &bull; Nasc: <?= date('d/m/Y', strtotime($paciente['data_nascimento'])) ?>
                    </small>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">CPF</small>
                    <span><?= htmlspecialchars($paciente['cpf'] ?: '-') ?></span>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Convênio</small>
                    <span><?= htmlspecialchars($paciente['convenio'] ?: 'Particular') ?></span>
                </div>
                <div class="col-md-2 text-right">
                    <span class="badge badge-<?= count($evolucoes) > 0 ? 'primary' : 'secondary' ?> badge-pill px-3 py-2">
                        <?= count($evolucoes) ?> evolução(ões)
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros e Ações -->
    <div class="card shadow mb-4">
        <div class="card-body py-3">
            <form method="get" class="form-inline flex-wrap">
                <input type="hidden" name="module" value="minha_clinica">
                <input type="hidden" name="action" value="prontuario_visualizar">
                <input type="hidden" name="paciente_id" value="<?= $paciente['id'] ?>">

                <div class="form-group mr-2 mb-2">
                    <label class="mr-1 text-muted small">De:</label>
                    <input type="date" class="form-control form-control-sm" name="de"
                           value="<?= htmlspecialchars($de) ?>">
                </div>
                <div class="form-group mr-2 mb-2">
                    <label class="mr-1 text-muted small">Até:</label>
                    <input type="date" class="form-control form-control-sm" name="ate"
                           value="<?= htmlspecialchars($ate) ?>">
                </div>
                <button type="submit" class="btn btn-primary btn-sm mr-2 mb-2">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
                <a href="index.php?module=minha_clinica&action=prontuario_visualizar&paciente_id=<?= $paciente['id'] ?>"
                   class="btn btn-outline-secondary btn-sm mr-3 mb-2">
                    <i class="fas fa-times"></i> Limpar
                </a>

                <div class="ml-auto mb-2 d-flex">
                    <button type="button" id="btnSelecionarTodos" class="btn btn-outline-primary btn-sm mr-2">
                        <i class="fas fa-check-square"></i> Selecionar Todos
                    </button>
                    <button type="button" id="btnImprimirSelecionados" class="btn btn-success btn-sm mr-2" disabled>
                        <i class="fas fa-print"></i> Imprimir Selecionados
                    </button>
                    <?php if (!empty($evolucoes)): ?>
                    <a href="index.php?module=minha_clinica&action=imprimir_prontuario&paciente_id=<?= $paciente['id'] ?><?= !empty($de) ? '&de='.urlencode($de) : '' ?><?= !empty($ate) ? '&ate='.urlencode($ate) : '' ?>"
                       target="_blank" class="btn btn-danger btn-sm">
                        <i class="fas fa-file-pdf"></i> Imprimir Tudo
                    </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Evoluções -->
    <?php if (empty($evolucoes)): ?>
        <div class="card shadow">
            <div class="card-body text-center py-5">
                <i class="fas fa-notes-medical fa-3x text-gray-300 mb-3"></i>
                <h5 class="text-gray-600">Nenhuma evolução encontrada</h5>
                <p class="text-muted">
                    <?= (!empty($de) || !empty($ate)) ? 'Tente ampliar o período de busca.' : 'Este paciente ainda não possui registros de evolução.' ?>
                </p>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($evolucoes as $ev): ?>
            <div class="card shadow mb-3 evolucao-card">
                <div class="card-header py-2 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <input type="checkbox" class="ev-checkbox mr-3" value="<?= $ev['id'] ?>"
                               style="width:18px;height:18px;cursor:pointer;">
                        <div>
                            <strong class="text-primary">
                                <i class="fas fa-calendar-day mr-1"></i>
                                <?= date('d/m/Y', strtotime($ev['data_registro'])) ?>
                            </strong>
                            <small class="text-muted ml-2"><?= date('H:i', strtotime($ev['data_registro'])) ?></small>
                            <span class="ml-3 text-gray-700 small">
                                <i class="fas fa-user-md mr-1"></i>
                                Dr(a). <?= htmlspecialchars($ev['profissional_nome']) ?>
                            </span>
                            <?php if (!empty($ev['registro_profissional'])): ?>
                                <small class="text-muted ml-1">(<?= htmlspecialchars($ev['registro_profissional']) ?>)</small>
                            <?php endif; ?>
                            <?php if (!empty($ev['cid10'])): ?>
                                <span class="badge badge-light border ml-2">CID: <?= htmlspecialchars($ev['cid10']) ?></span>
                            <?php endif; ?>
                            <?php if (isset($ev['versao']) && $ev['versao'] > 1): ?>
                                <span class="badge badge-warning ml-1">v<?= $ev['versao'] ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="d-flex">
                        <button type="button" class="btn btn-link btn-sm text-secondary p-1 toggle-texto"
                                data-id="<?= $ev['id'] ?>" title="Expandir/Recolher">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <a href="index.php?module=minha_clinica&action=imprimir_evolucao&id=<?= $ev['id'] ?>"
                           target="_blank" class="btn btn-outline-primary btn-sm ml-1" title="Imprimir esta evolução">
                            <i class="fas fa-print"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body py-3 texto-evolucao" id="texto-<?= $ev['id'] ?>">
                    <div style="white-space: pre-wrap; font-size: 0.95rem; line-height: 1.7;">
                        <?= nl2br(htmlspecialchars($ev['texto'])) ?>
                    </div>
                    <?php if (!empty($ev['assinatura_digital_hash'])): ?>
                        <div class="mt-3 pt-2 border-top">
                            <small class="text-muted">
                                <i class="fas fa-lock mr-1 text-success"></i>
                                Assinado digitalmente &mdash;
                                <code style="font-size:10px;"><?= substr($ev['assinatura_digital_hash'], 0, 32) ?>...</code>
                            </small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
$(document).ready(function () {

    // Recolher todos por padrão (mostrar apenas cabeçalho)
    $('.texto-evolucao').hide();

    // Toggle expandir/recolher
    $(document).on('click', '.toggle-texto', function () {
        var id = $(this).data('id');
        var $texto = $('#texto-' + id);
        var $icon = $(this).find('i');
        $texto.slideToggle(200);
        $icon.toggleClass('fa-chevron-down fa-chevron-up');
    });

    // Selecionar todos
    $('#btnSelecionarTodos').on('click', function () {
        var allChecked = $('.ev-checkbox:checked').length === $('.ev-checkbox').length;
        $('.ev-checkbox').prop('checked', !allChecked);
        $(this).html(!allChecked
            ? '<i class="fas fa-minus-square"></i> Desmarcar Todos'
            : '<i class="fas fa-check-square"></i> Selecionar Todos'
        );
        atualizarBotaoImprimir();
    });

    // Atualizar botão ao marcar/desmarcar
    $(document).on('change', '.ev-checkbox', function () {
        atualizarBotaoImprimir();
        var total = $('.ev-checkbox').length;
        var marcados = $('.ev-checkbox:checked').length;
        if (marcados === 0) {
            $('#btnSelecionarTodos').html('<i class="fas fa-check-square"></i> Selecionar Todos');
        } else if (marcados === total) {
            $('#btnSelecionarTodos').html('<i class="fas fa-minus-square"></i> Desmarcar Todos');
        }
    });

    function atualizarBotaoImprimir() {
        var marcados = $('.ev-checkbox:checked').length;
        var $btn = $('#btnImprimirSelecionados');
        $btn.prop('disabled', marcados === 0);
        if (marcados > 0) {
            $btn.html('<i class="fas fa-print"></i> Imprimir Selecionados (' + marcados + ')');
        } else {
            $btn.html('<i class="fas fa-print"></i> Imprimir Selecionados');
        }
    }

    // Imprimir selecionados
    $('#btnImprimirSelecionados').on('click', function () {
        var ids = [];
        $('.ev-checkbox:checked').each(function () {
            ids.push($(this).val());
        });
        if (ids.length === 0) return;
        var url = 'index.php?module=minha_clinica&action=imprimir_prontuario&ids=' + ids.join(',');
        window.open(url, '_blank');
    });
});
</script>
