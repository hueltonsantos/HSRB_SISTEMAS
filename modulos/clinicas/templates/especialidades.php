<?php
/**
 * Template para gerenciar especialidades de uma clínica
 */
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <?php echo $pageTitle; ?>
                    </h4>
                    <p>
                        <a href="index.php?module=clinicas&action=view&id=<?php echo $clinicaId; ?>" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar para Detalhes da Clínica
                        </a>
                    </p>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['mensagem'])): ?>
                        <div class="alert alert-<?php echo $_SESSION['mensagem']['tipo']; ?> alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['mensagem']['texto']; ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Fechar">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <?php unset($_SESSION['mensagem']); ?>
                    <?php endif; ?>

                    <!-- Formulário para adicionar especialidade -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Adicionar Especialidade</h5>
                        </div>
                        <div class="card-body">
                            <form method="post" action="index.php?module=clinicas&action=especialidades&id=<?php echo $clinicaId; ?>">
                                <div class="form-row align-items-center">
                                    <div class="col-md-8">
                                        <select name="especialidade_id" id="especialidade_id" class="form-control select2" required>
                                            <option value="">Selecione uma especialidade</option>
                                            <?php foreach ($especialidades as $especialidade): ?>
                                                <option value="<?php echo $especialidade['id']; ?>">
                                                    <?php echo $especialidade['nome']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" name="adicionar_especialidade" class="btn btn-primary">
                                            <i class="fas fa-plus"></i> Adicionar Especialidade
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Lista de especialidades da clínica -->
                    <div class="card">
                        <div class="card-header">
                            <h5>Especialidades Vinculadas</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($especialidadesClinica)): ?>
                                <div class="alert alert-info">
                                    Nenhuma especialidade cadastrada para esta clínica.
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th width="80%">Nome da Especialidade</th>
                                                <th width="20%">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($especialidadesClinica as $esp): ?>
                                                <tr>
                                                    <td><?php echo $esp['nome']; ?></td>
                                                    <td>
                                                        <a href="index.php?module=clinicas&action=especialidades&id=<?php echo $clinicaId; ?>&remove=<?php echo $esp['id']; ?>" 
                                                           class="btn btn-sm btn-danger" 
                                                           onclick="return confirm('Tem certeza que deseja remover esta especialidade?');">
                                                            <i class="fas fa-trash"></i> Remover
                                                        </a>
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
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Inicializa o Select2 se estiver disponível
        if ($.fn.select2) {
            $('.select2').select2({
                placeholder: "Selecione uma especialidade",
                allowClear: true
            });
        }
    });
</script>