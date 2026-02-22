<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-stethoscope mr-2"></i>
            <?= htmlspecialchars($pageTitle) ?>
        </h1>
        <a href="index.php?module=minha_clinica&action=especialidades" class="btn btn-secondary btn-sm">
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
                <?= $isEdit ? 'Editar Especialidade' : 'Nova Especialidade' ?>
            </h6>
        </div>
        <div class="card-body">
            <form action="<?= $formAction ?>" method="POST">
                <?php if ($isEdit && isset($especialidade['id'])): ?>
                    <input type="hidden" name="id" value="<?= $especialidade['id'] ?>">
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nome">Nome da Especialidade <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control"
                                   id="nome"
                                   name="nome"
                                   value="<?= htmlspecialchars($especialidade['nome'] ?? '') ?>"
                                   required
                                   placeholder="Ex: Cardiologia, Dermatologia...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <div class="custom-control custom-switch mt-2">
                                <input type="checkbox"
                                       class="custom-control-input"
                                       id="status"
                                       name="status"
                                       value="1"
                                       <?= (!isset($especialidade['status']) || $especialidade['status']) ? 'checked' : '' ?>>
                                <label class="custom-control-label" for="status">Ativo</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="descricao">Descricao</label>
                            <textarea class="form-control"
                                      id="descricao"
                                      name="descricao"
                                      rows="3"
                                      placeholder="Descricao opcional da especialidade"><?= htmlspecialchars($especialidade['descricao'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Salvar
                        </button>
                        <a href="index.php?module=minha_clinica&action=especialidades" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
