<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-file-medical-alt mr-2"></i>
            <?= htmlspecialchars($pageTitle) ?>
        </h1>
    </div>

    <!-- Filtros -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" class="form-row align-items-end">
                <input type="hidden" name="module" value="minha_clinica">
                <input type="hidden" name="action" value="guias">

                <div class="col-md-3 mb-3">
                    <label>Período</label>
                    <div class="input-group">
                        <input type="date" name="data_inicio" class="form-control form-control-sm" value="<?= $dataInicio ?>">
                        <input type="date" name="data_fim" class="form-control form-control-sm" value="<?= $dataFim ?>">
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <label>Convênio</label>
                    <select name="convenio_id" class="form-control form-control-sm">
                        <option value="">Todos</option>
                        <?php foreach ($convenios as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= ($convenioId == $c['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['nome_fantasia']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2 mb-3">
                    <label>Status</label>
                    <select name="status" class="form-control form-control-sm">
                        <option value="">Todos</option>
                        <option value="solicitada" <?= ($status == 'solicitada') ? 'selected' : '' ?>>Solicitada</option>
                        <option value="autorizada" <?= ($status == 'autorizada') ? 'selected' : '' ?>>Autorizada</option>
                        <option value="negada" <?= ($status == 'negada') ? 'selected' : '' ?>>Negada</option>
                        <option value="faturada" <?= ($status == 'faturada') ? 'selected' : '' ?>>Faturada</option>
                        <option value="paga" <?= ($status == 'paga') ? 'selected' : '' ?>>Paga</option>
                        <option value="glosada" <?= ($status == 'glosada') ? 'selected' : '' ?>>Glosada</option>
                    </select>
                </div>

                <div class="col-md-2 mb-3">
                     <button type="submit" class="btn btn-primary btn-sm btn-block">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                    <a href="index.php?module=minha_clinica&action=guias" class="btn btn-secondary btn-sm btn-block mt-1">Limpar</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dataTableGuias" width="100%">
                    <thead>
                        <tr>
                            <th>Data/Hora</th>
                            <th>Guia</th>
                            <th>Paciente</th>
                            <th>Convênio</th>
                            <th>Procedimentos</th>
                            <th>Status</th>
                            <th width="120">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($guias as $g): ?>
                            <?php
                                // Cores do Status
                                $badgeClass = 'secondary';
                                switch ($g['status']) {
                                    case 'autorizada': $badgeClass = 'primary'; break;
                                    case 'faturada': $badgeClass = 'info'; break;
                                    case 'paga': $badgeClass = 'success'; break;
                                    case 'negada': 
                                    case 'glosada': $badgeClass = 'danger'; break;
                                    case 'solicitada': $badgeClass = 'warning'; break;
                                }
                            ?>
                            <tr>
                                <td>
                                    <?= date('d/m/Y', strtotime($g['data_consulta'])) ?><br>
                                    <small><?= substr($g['hora_consulta'], 0, 5) ?></small>
                                </td>
                                <td><strong><?= htmlspecialchars($g['numero_guia']) ?></strong></td>
                                <td><?= htmlspecialchars($g['paciente_nome']) ?></td>
                                <td><?= htmlspecialchars($g['convenio_nome']) ?></td>
                                <td><small><?= htmlspecialchars($g['procedimentos_lista'] ?? '-') ?></small></td>
                                <td class="text-center">
                                    <span class="badge badge-<?= $badgeClass ?> p-2"><?= strtoupper($g['status']) ?></span>
                                    <?php if ($g['status'] == 'glosada' && $g['motivo_glosa']): ?>
                                        <br><small class="text-danger" title="<?= $g['motivo_glosa'] ?>"><i class="fas fa-exclamation-triangle"></i> Motivo</small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-info dropdown-toggle" data-toggle="dropdown">
                                            Ação
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item btn-mudar-status" href="#" data-id="<?= $g['id'] ?>" data-status="autorizada">
                                                <i class="fas fa-check text-primary"></i> Autorizar
                                            </a>
                                            <a class="dropdown-item btn-mudar-status" href="#" data-id="<?= $g['id'] ?>" data-status="faturada">
                                                <i class="fas fa-file-invoice-dollar text-info"></i> Faturar (Enviar)
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item btn-mudar-status" href="#" data-id="<?= $g['id'] ?>" data-status="paga">
                                                <i class="fas fa-dollar-sign text-success"></i> Marcar Paga
                                            </a>
                                            <a class="dropdown-item btn-glosar" href="#" data-id="<?= $g['id'] ?>">
                                                <i class="fas fa-times-circle text-danger"></i> Glosar
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Glosa -->
<div class="modal fade" id="modalGlosa" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Registrar Glosa</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="glosa_guia_id">
                <div class="form-group">
                    <label>Motivo da Glosa</label>
                    <textarea id="motivo_glosa" class="form-control" rows="3" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmarGlosa">Confirmar Glosa</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#dataTableGuias').DataTable({
        language: { url: 'https://cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese-Brasil.json' },
        order: [[0, 'desc']]
    });

    // Mudar Status Simples
    $('.btn-mudar-status').click(function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var status = $(this).data('status');
        
        if (confirm('Deseja alterar o status para ' + status.toUpperCase() + '?')) {
            alterarStatus(id, status);
        }
    });

    // Abrir Modal Glosa
    $('.btn-glosar').click(function(e) {
        e.preventDefault();
        $('#glosa_guia_id').val($(this).data('id'));
        $('#motivo_glosa').val('');
        $('#modalGlosa').modal('show');
    });

    // Confirmar Glosa
    $('#btnConfirmarGlosa').click(function() {
        var id = $('#glosa_guia_id').val();
        var motivo = $('#motivo_glosa').val();
        
        if (!motivo) {
            alert('Informe o motivo.');
            return;
        }

        alterarStatus(id, 'glosada', motivo);
        $('#modalGlosa').modal('hide');
    });

    function alterarStatus(id, status, motivo = null) {
        $.post('index.php?module=minha_clinica&action=alterar_status_guia', {
            id: id,
            status: status,
            motivo_glosa: motivo
        }, function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Erro: ' + response.message);
            }
        }, 'json');
    }
});
</script>
