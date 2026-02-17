<div class="container-fluid">
    <!-- Titulo -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-calendar-alt text-primary"></i> Agendamentos - Minha Clinica
        </h1>
        <a href="index.php?module=minha_clinica&action=novo_agendamento" class="btn btn-primary">
            <i class="fas fa-plus"></i> Novo Agendamento
        </a>
    </div>

    <!-- Filtros -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter"></i> Filtros</h6>
        </div>
        <div class="card-body">
            <form method="GET" class="row">
                <input type="hidden" name="module" value="minha_clinica">
                <input type="hidden" name="action" value="agendamentos">

                <div class="col-md-2 mb-2">
                    <label>Data Inicio</label>
                    <input type="date" name="data_inicio" class="form-control" value="<?= htmlspecialchars($filtros['data_inicio']) ?>">
                </div>
                <div class="col-md-2 mb-2">
                    <label>Data Fim</label>
                    <input type="date" name="data_fim" class="form-control" value="<?= htmlspecialchars($filtros['data_fim']) ?>">
                </div>
                <div class="col-md-2 mb-2">
                    <label>Especialidade</label>
                    <select name="especialidade_id" class="form-control">
                        <option value="">Todas</option>
                        <?php foreach ($especialidades as $esp): ?>
                            <option value="<?= $esp['id'] ?>" <?= $filtros['especialidade_id'] == $esp['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($esp['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label>Profissional</label>
                    <select name="profissional_id" class="form-control">
                        <option value="">Todos</option>
                        <?php foreach ($profissionais as $prof): ?>
                            <option value="<?= $prof['id'] ?>" <?= $filtros['profissional_id'] == $prof['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($prof['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="">Todos</option>
                        <option value="agendado" <?= $filtros['status'] == 'agendado' ? 'selected' : '' ?>>Agendado</option>
                        <option value="confirmado" <?= $filtros['status'] == 'confirmado' ? 'selected' : '' ?>>Confirmado</option>
                        <option value="realizado" <?= $filtros['status'] == 'realizado' ? 'selected' : '' ?>>Realizado</option>
                        <option value="cancelado" <?= $filtros['status'] == 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                        <option value="faltou" <?= $filtros['status'] == 'faltou' ? 'selected' : '' ?>>Faltou</option>
                    </select>
                </div>
                <div class="col-md-2 mb-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabela de Agendamentos -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <?php if (empty($agendamentos)): ?>
                <div class="text-center text-muted py-5">
                    <i class="fas fa-calendar-times fa-4x mb-3"></i>
                    <h5>Nenhum agendamento encontrado</h5>
                    <p>Ajuste os filtros ou crie um novo agendamento</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="dataTable">
                        <thead class="thead-light">
                            <tr>
                                <th>ID</th>
                                <th>Data/Hora</th>
                                <th>Paciente</th>
                                <th>Especialidade</th>
                                <th>Procedimento</th>
                                <th>Profissional</th>
                                <th>Valor</th>
                                <th>Status</th>
                                <th>Acoes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($agendamentos as $ag): ?>
                                <tr>
                                    <td><?= $ag['id'] ?></td>
                                    <td>
                                        <strong><?= date('d/m/Y', strtotime($ag['data_consulta'])) ?></strong><br>
                                        <small><?= substr($ag['hora_consulta'], 0, 5) ?></small>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($ag['paciente_nome']) ?>
                                        <?php if ($ag['paciente_celular']): ?>
                                            <br><small class="text-muted"><?= $ag['paciente_celular'] ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($ag['especialidade_nome'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($ag['procedimento_nome'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($ag['profissional_nome'] ?? '-') ?></td>
                                    <td>R$ <?= number_format($ag['valor'] ?? 0, 2, ',', '.') ?></td>
                                    <td>
                                        <?php
                                        $statusClass = [
                                            'agendado' => 'warning',
                                            'confirmado' => 'info',
                                            'em_atendimento' => 'primary',
                                            'realizado' => 'success',
                                            'cancelado' => 'danger',
                                            'faltou' => 'secondary'
                                        ];
                                        $class = $statusClass[$ag['status']] ?? 'secondary';
                                        ?>
                                        <span class="badge badge-<?= $class ?>"><?= ucfirst(str_replace('_', ' ', $ag['status'])) ?></span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="index.php?module=minha_clinica&action=ver_agendamento&id=<?= $ag['id'] ?>" class="btn btn-sm btn-info" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="index.php?module=minha_clinica&action=editar_agendamento&id=<?= $ag['id'] ?>" class="btn btn-sm btn-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown">
                                                    Status
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" href="index.php?module=minha_clinica&action=api&api_action=alterar_status&id=<?= $ag['id'] ?>&status=confirmado">Confirmar</a>
                                                    <a class="dropdown-item" href="index.php?module=minha_clinica&action=api&api_action=alterar_status&id=<?= $ag['id'] ?>&status=realizado">Realizado</a>
                                                    <a class="dropdown-item" href="index.php?module=minha_clinica&action=api&api_action=alterar_status&id=<?= $ag['id'] ?>&status=cancelado">Cancelar</a>
                                                    <a class="dropdown-item" href="index.php?module=minha_clinica&action=api&api_action=alterar_status&id=<?= $ag['id'] ?>&status=faltou">Faltou</a>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#dataTable').DataTable({
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese-Brasil.json"
        },
        "order": [[1, "asc"]],
        "pageLength": 25
    });
});
</script>
