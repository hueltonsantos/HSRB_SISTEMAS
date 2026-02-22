<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Gerar Repasse</h1>

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
            <h6 class="m-0 font-weight-bold text-primary">Selecione a Clínica e o Período</h6>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                O sistema irá buscar todos os agendamentos com status <strong>"realizado"</strong> no período selecionado
                para a clínica parceira e calcular o valor de repasse baseado na tabela de preços (<code>valor_repasse</code>).
                Agendamentos já incluídos em repasses anteriores serão ignorados.
            </div>

            <form action="index.php?module=caixa&action=salvar_repasse" method="post">
                <input type="hidden" name="acao" value="gerar">

                <div class="row">
                    <div class="col-sm-12 col-md-4 form-group">
                        <label for="clinica_id">Clínica Parceira *</label>
                        <select class="form-control select2" id="clinica_id" name="clinica_id" required>
                            <option value="">Selecione...</option>
                            <?php foreach ($clinicas as $clinica): ?>
                                <option value="<?php echo $clinica['id']; ?>" <?php echo (isset($formData['clinica_id']) && $formData['clinica_id'] == $clinica['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($clinica['nome']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-sm-12 col-md-3 form-group">
                        <label for="periodo_inicio">Período Início *</label>
                        <input type="text" class="form-control datepicker" id="periodo_inicio" name="periodo_inicio"
                            value="<?php echo htmlspecialchars($formData['periodo_inicio'] ?? ''); ?>"
                            placeholder="DD/MM/AAAA" required>
                    </div>

                    <div class="col-sm-12 col-md-3 form-group">
                        <label for="periodo_fim">Período Fim *</label>
                        <input type="text" class="form-control datepicker" id="periodo_fim" name="periodo_fim"
                            value="<?php echo htmlspecialchars($formData['periodo_fim'] ?? ''); ?>"
                            placeholder="DD/MM/AAAA" required>
                    </div>
                </div>

                <hr>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-calculator"></i> Gerar Repasse
                    </button>
                    <a href="index.php?module=caixa&action=repasses" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.datepicker').datepicker({
        format: 'dd/mm/yyyy',
        language: 'pt-BR',
        autoclose: true,
        todayHighlight: true
    });

    $('.select2').select2({
        placeholder: 'Selecione...',
        allowClear: true,
        width: '100%'
    });
});
</script>
