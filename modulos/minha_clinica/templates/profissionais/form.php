<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user-md mr-2"></i>
            <?= htmlspecialchars($pageTitle) ?>
        </h1>
        <a href="index.php?module=minha_clinica&action=profissionais" class="btn btn-secondary btn-sm">
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
                <?= $isEdit ? 'Editar Profissional' : 'Novo Profissional' ?>
            </h6>
        </div>
        <div class="card-body">
            <form action="<?= $formAction ?>" method="POST">
                <?php if ($isEdit && isset($profissional['id'])): ?>
                    <input type="hidden" name="id" value="<?= $profissional['id'] ?>">
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nome">Nome Completo <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control"
                                   id="nome"
                                   name="nome"
                                   value="<?= htmlspecialchars($profissional['nome'] ?? '') ?>"
                                   required
                                   placeholder="Nome do profissional">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="especialidade_id">Especialidade</label>
                            <select class="form-control" id="especialidade_id" name="especialidade_id">
                                <option value="">Selecione...</option>
                                <?php foreach ($especialidades as $esp): ?>
                                    <option value="<?= $esp['id'] ?>"
                                        <?= (isset($profissional['especialidade_id']) && $profissional['especialidade_id'] == $esp['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($esp['nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="registro_profissional">Registro Profissional</label>
                            <input type="text"
                                   class="form-control"
                                   id="registro_profissional"
                                   name="registro_profissional"
                                   value="<?= htmlspecialchars($profissional['registro_profissional'] ?? '') ?>"
                                   placeholder="CRM, CRO, CREFITO...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="telefone">Telefone</label>
                            <input type="text"
                                   class="form-control phone-mask"
                                   id="telefone"
                                   name="telefone"
                                   value="<?= htmlspecialchars($profissional['telefone'] ?? '') ?>"
                                   placeholder="(00) 00000-0000">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="email">E-mail</label>
                            <input type="email"
                                   class="form-control"
                                   id="email"
                                   name="email"
                                   value="<?= htmlspecialchars($profissional['email'] ?? '') ?>"
                                   placeholder="email@exemplo.com">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <div class="custom-control custom-switch mt-2">
                                <input type="checkbox"
                                       class="custom-control-input"
                                       id="status"
                                       name="status"
                                       value="1"
                                       <?= (!isset($profissional['status']) || $profissional['status']) ? 'checked' : '' ?>>
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
                        <a href="index.php?module=minha_clinica&action=profissionais" class="btn btn-secondary">
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
    // Mascara para telefone
    if (typeof $.fn.mask === 'function') {
        var SPMaskBehavior = function (val) {
            return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
        };
        var spOptions = {
            onKeyPress: function(val, e, field, options) {
                field.mask(SPMaskBehavior.apply({}, arguments), options);
            }
        };
        $('.phone-mask').mask(SPMaskBehavior, spOptions);
    }
});
</script>
