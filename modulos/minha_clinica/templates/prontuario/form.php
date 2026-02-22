<div class="container-fluid">
    <!-- Cabeçalho do Paciente -->
    <div class="card shadow mb-4 border-left-info">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="font-weight-bold text-gray-800 mb-1"><?= htmlspecialchars($pacienteNome) ?></h4>
                    <span class="mr-3"><i class="fas fa-birthday-cake text-gray-500"></i> <?= $idade ?> anos
                        (<?= $dataNasc ?>)</span>
                    <span class="mr-3"><i class="fas fa-venus-mars text-gray-500"></i>
                        <?= htmlspecialchars($agendamento['sexo']) ?></span>
                    <span><i class="fas fa-notes-medical text-gray-500"></i>
                        <?= htmlspecialchars($agendamento['convenio_nome'] ?? 'Particular') ?></span>
                </div>
                <div class="col-md-4 text-right">
                    <a href="index.php?module=minha_clinica&action=painel_profissional"
                        class="btn btn-secondary btn-sm">
                        <i class="fas fa-times"></i> Fechar Atendimento
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Nova Evolução -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div
                    class="card-header py-3 bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">Registro de Atendimento (Evolução)</h6>
                    <small><?= date('d/m/Y H:i') ?></small>
                </div>
                <div class="card-body">
                    <?php if (in_array($agendamento['status'], ['realizado', 'cancelado'])): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-lock fa-3x text-gray-300 mb-3"></i>
                            <h5 class="text-gray-800">Atendimento Encerrado</h5>
                            <p class="text-muted">Este agendamento já foi finalizado ou cancelado.</p>
                            <p class="mb-0">Para registrar uma nova evolução, inicie um <strong>novo atendimento</strong>.
                            </p>
                        </div>
                    <?php else: ?>
                        <form action="index.php?module=minha_clinica&action=salvar_evolucao" method="POST">
                            <input type="hidden" name="agendamento_id" value="<?= $agendamento['id'] ?>">
                            <input type="hidden" name="paciente_id" value="<?= $agendamento['paciente_id'] ?>">

                            <div class="form-group">
                                <label for="cid10">CID-10 Principal <small class="text-muted">(Opcional)</small></label>
                                <input type="text" class="form-control" name="cid10" id="cid10" placeholder="Ex: J00"
                                    style="max-width: 200px;">
                            </div>

                            <div class="form-group">
                                <label for="texto">Descrição da Evolução</label>
                                <textarea class="form-control" name="texto" id="texto" rows="10" required
                                    placeholder="Descreva a anamnese, exame físico e conduta..."></textarea>
                            </div>

                            <hr>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted"><i class="fas fa-lock"></i> Este registro será assinado
                                    digitalmente.</small>
                                <button type="submit" class="btn btn-success btn-sm btn-lg">
                                    <i class="fas fa-save mr-2"></i> Finalizar e Assinar
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Histórico -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Histórico do Paciente</h6>
                </div>
                <div class="card-body p-0" style="max-height: 700px; overflow-y: auto;">
                    <?php if (empty($historico)): ?>
                        <div class="p-4 text-center text-muted">
                            <p>Nenhum histórico encontrado.</p>
                        </div>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($historico as $ev): ?>
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1 font-weight-bold text-primary">
                                            <?= date('d/m/Y', strtotime($ev['data_registro'])) ?></h6>
                                        <small><?= date('H:i', strtotime($ev['data_registro'])) ?></small>
                                    </div>
                                    <p class="mb-1 text-xs font-weight-bold text-gray-600">
                                        Dr(a). <?= htmlspecialchars($ev['profissional_nome']) ?>
                                    </p>
                                    <p class="mb-1" style="white-space: pre-wrap; font-size: 0.9rem;">
                                        <?= htmlspecialchars(substr($ev['texto'], 0, 150)) . (strlen($ev['texto']) > 150 ? '...' : '') ?>
                                    </p>

                                    <?php if (strlen($ev['texto']) > 150): ?>
                                        <button type="button" class="btn btn-link btn-sm p-0 ver-completo"
                                            data-texto="<?= htmlspecialchars($ev['texto']) ?>"
                                            data-data="<?= date('d/m/Y H:i', strtotime($ev['data_registro'])) ?>"
                                            data-prof="<?= htmlspecialchars($ev['profissional_nome']) ?>">
                                            Ver completo
                                        </button>
                                    <?php endif; ?>

                                    <?php if ($ev['cid10']): ?>
                                        <small class="badge badge-light border">CID: <?= $ev['cid10'] ?></small>
                                    <?php endif; ?>

                                    <?php if (isset($ev['versao']) && $ev['versao'] > 1): ?>
                                        <small class="badge badge-warning">v<?= $ev['versao'] ?></small>
                                    <?php endif; ?>

                                    <div class="mt-2 text-right">
                                        <a href="index.php?module=minha_clinica&action=imprimir_evolucao&id=<?= $ev['id'] ?>" target="_blank" class="btn btn-sm btn-outline-primary" title="Imprimir / Visualizar Melhor">
                                            <i class="fas fa-print"></i> Imprimir
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Visualizar Histórico Completo -->
<div class="modal fade" id="modalHistorico" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="histDataProf"></h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div id="histTexto" style="white-space: pre-wrap;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('.ver-completo').click(function () {
            var texto = $(this).data('texto');
            var data = $(this).data('data');
            var prof = $(this).data('prof');

            $('#histDataProf').text(data + ' - ' + prof);
            $('#histTexto').text(texto);
            $('#modalHistorico').modal('show');
        });
    });
</script>