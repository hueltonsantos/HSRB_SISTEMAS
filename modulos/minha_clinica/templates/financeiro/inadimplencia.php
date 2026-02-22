<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-calendar-times mr-2 text-danger"></i>
            <?= htmlspecialchars($pageTitle) ?>
        </h1>
        <button class="btn btn-secondary shadow-sm" onclick="window.print()">
            <i class="fas fa-print fa-sm text-white-50"></i> Imprimir
        </button>
    </div>

    <div class="card shadow mb-4 d-print-none">
        <div class="card-body">
            <form method="GET" class="form-row align-items-end">
                <input type="hidden" name="module" value="minha_clinica">
                <input type="hidden" name="action" value="financeiro_inadimplencia">

                <div class="col-md-4 mb-3">
                    <label>Convênio</label>
                    <select name="convenio_id" class="form-control">
                        <option value="">Todos</option>
                        <?php foreach ($convenios as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= ($filtroConvenio == $c['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['nome_fantasia']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label>Atraso Mínimo (Dias)</label>
                    <input type="number" name="dias_atraso" class="form-control" value="<?= $diasAtraso ?>" min="0">
                </div>

                <div class="col-md-2 mb-3">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Total Inadimplente (Vencido)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                R$ <?= number_format($totalVencido, 2, ',', '.') ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabela -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-gradient-danger text-white">
            <h6 class="m-0 font-weight-bold">Guias Faturadas e Não Pagas</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" id="dataTableInadimplencia">
                    <thead>
                        <tr>
                            <th>Guia / Data Rec.</th>
                            <th>Convênio</th>
                            <th>Paciente</th>
                            <th>Prazo (Dias)</th>
                            <th>Vencimento</th>
                            <th>Dias Atraso</th>
                            <th>Valor</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($guiasVencidas as $g): ?>
                            <tr>
                                <td>
                                    <strong><?= $g['numero_guia'] ?></strong><br>
                                    <small class="text-muted">Emissão: <?= date('d/m/Y', strtotime($g['data_emissao'])) ?></small>
                                </td>
                                <td><?= htmlspecialchars($g['convenio_nome']) ?></td>
                                <td><?= htmlspecialchars($g['paciente_nome']) ?></td>
                                <td class="text-center"><?= $g['prazo_recebimento_dias'] ?></td>
                                <td class="text-danger font-weight-bold">
                                    <?= date('d/m/Y', strtotime($g['data_prevista_pagamento'])) ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-danger"><?= $g['dias_atrasado'] ?> dias</span>
                                </td>
                                <td class="text-right">R$ <?= number_format($g['valor_total'], 2, ',', '.') ?></td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-info" onclick="alert('Funcionalidade de cobrar: Abrir modal de e-mail/notificação')">
                                        <i class="fas fa-envelope"></i> Cobrar
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($guiasVencidas)): ?>
                            <tr>
                                <td colspan="8" class="text-center">Nenhuma guia vencida encontrada com os filtros atuais.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                <small class="text-muted"><i class="fas fa-info-circle"></i> O atraso é calculado com base na Data de Emissão + Prazo de Recebimento cadastrado no convênio.</small>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#dataTableInadimplencia').DataTable({
        language: { url: 'https://cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese-Brasil.json' },
        order: [[5, "desc"]] // Ordenar por Dias Atraso descendant
    });
});
</script>
