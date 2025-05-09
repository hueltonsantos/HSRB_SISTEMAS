<?php
// Verifica se o acesso é direto
if (!defined('BASEPATH')) exit('Acesso negado');

// Verificar se a classe EspecialidadeController já existe
if (!class_exists('EspecialidadeController')) {
    // Definição temporária da classe EspecialidadeController
    class EspecialidadeController {
        private $db;
        
        public function __construct($database) {
            $this->db = $database;
        }
        
        public function listarAtivas() {
            $query = "SELECT * FROM especialidades WHERE status = 1 ORDER BY nome";
            return $this->db->query($query);
        }
    }
}

// Inicializa os controladores
$controller = new ProcedimentoController($db);
$especialidadeController = new EspecialidadeController($db);

// Resto do código...

// Busca o procedimento se estiver editando
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$procedimento = $id ? $controller->buscarPorId($id) : null;

// Lista todas as especialidades para o select
$especialidades = $especialidadeController->listarAtivas();

// Título da página
$titulo = $id ? "Editar Procedimento" : "Novo Procedimento";
?>

<div class="container-fluid">
    <h1 class="mt-4"><?= $titulo ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="index.php?page=procedimentos">Procedimentos</a></li>
        <li class="breadcrumb-item active"><?= $titulo ?></li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-edit mr-1"></i> <?= $titulo ?>
        </div>
        <div class="card-body">
            <form id="form-procedimento" method="post" action="ajax/salvar_procedimento.php">
                <input type="hidden" name="id" value="<?= $id ?>">
                
                <div class="form-row">
                    <div class="form-group col-md-8">
                        <label for="procedimento">Nome do Procedimento</label>
                        <input type="text" class="form-control" id="procedimento" name="procedimento" 
                               value="<?= isset($procedimento) ? $procedimento['procedimento'] : '' ?>" required>
                    </div>
                    
                    <div class="form-group col-md-4">
                        <label for="especialidade_id">Especialidade</label>
                        <select class="form-control" id="especialidade_id" name="especialidade_id" required>
                            <option value="">Selecione...</option>
                            <?php while ($esp = $especialidades->fetch_assoc()): ?>
                                <option value="<?= $esp['id'] ?>" 
                                        <?= (isset($procedimento) && $procedimento['especialidade_id'] == $esp['id']) ? 'selected' : '' ?>>
                                    <?= $esp['nome'] ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="valor">Valor Padrão (R$)</label>
                        <input type="text" class="form-control money" id="valor" name="valor" 
                               value="<?= isset($procedimento) ? number_format($procedimento['valor'], 2, ',', '.') : '' ?>" required>
                    </div>
                    
                    <div class="form-group col-md-6">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="1" <?= (!isset($procedimento) || $procedimento['status'] == 1) ? 'selected' : '' ?>>Ativo</option>
                            <option value="0" <?= (isset($procedimento) && $procedimento['status'] == 0) ? 'selected' : '' ?>>Inativo</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group text-right">
                    <a href="index.php?page=procedimentos" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Inicializa máscaras
    $('.money').mask('#.##0,00', {reverse: true});
    
    // Manipulação do formulário
    $('#form-procedimento').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            type: "POST",
            url: $(this).attr('action'),
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.erro) {
                    alert(response.erro);
                } else {
                    window.location.href = 'index.php?page=procedimentos';
                }
            },
            error: function() {
                alert("Erro ao processar a requisição!");
            }
        });
    });
});
</script>