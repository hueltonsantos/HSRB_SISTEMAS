<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-procedures mr-2"></i>
            <?= htmlspecialchars($pageTitle) ?>
        </h1>
        <a href="index.php?module=minha_clinica&action=procedimentos" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>

    <?php if (isset($_SESSION['mensagem'])): ?>
        <div class="alert alert-<?= $_SESSION['mensagem']['tipo'] ?> alert-dismissible fade show">
            <?= $_SESSION['mensagem']['texto'] ?>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
        <?php unset($_SESSION['mensagem']); ?>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <?= $isEdit ? 'Editar Procedimento' : 'Novo Procedimento' ?>
            </h6>
        </div>
        <div class="card-body">
            <form action="<?= $formAction ?>" method="POST">
                <?php if ($isEdit && isset($procedimento['id'])): ?>
                    <input type="hidden" name="id" value="<?= $procedimento['id'] ?>">
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="especialidade_id">Especialidade <span class="text-danger">*</span></label>
                            <select class="form-control" id="especialidade_id" name="especialidade_id" required>
                                <option value="">Selecione...</option>
                                <?php foreach ($especialidades as $esp): ?>
                                    <option value="<?= $esp['id'] ?>"
                                        <?= (isset($procedimento['especialidade_id']) && $procedimento['especialidade_id'] == $esp['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($esp['nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="procedimento">Nome do Procedimento <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control"
                                   id="procedimento"
                                   name="procedimento"
                                   value="<?= htmlspecialchars($procedimento['procedimento'] ?? '') ?>"
                                   required
                                   placeholder="Ex: Consulta, Exame de sangue...">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="valor">Valor (R$) <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control money-mask"
                                   id="valor"
                                   name="valor"
                                   value="<?= isset($procedimento['valor']) ? number_format($procedimento['valor'], 2, ',', '.') : '' ?>"
                                   required
                                   placeholder="0,00">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="duracao_minutos">Duracao (minutos)</label>
                            <input type="number"
                                   class="form-control"
                                   id="duracao_minutos"
                                   name="duracao_minutos"
                                   value="<?= htmlspecialchars($procedimento['duracao_minutos'] ?? 30) ?>"
                                   min="5"
                                   max="480"
                                   placeholder="30">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <div class="custom-control custom-switch mt-2">
                                <input type="checkbox"
                                       class="custom-control-input"
                                       id="status"
                                       name="status"
                                       value="1"
                                       <?= (!isset($procedimento['status']) || $procedimento['status']) ? 'checked' : '' ?>>
                                <label class="custom-control-label" for="status">Ativo</label>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Salvar
                        </button>
                        <a href="index.php?module=minha_clinica&action=procedimentos" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Mascara para valor monetario
    if (typeof $.fn.mask === 'function') {
        $('.money-mask').mask('#.##0,00', {reverse: true});
    }
});
</script>
