<?php
// Verifica se o acesso é direto
if (!defined('BASEPATH')) exit('Acesso negado');

// Verificar se a classe ClinicaController já existe
if (!class_exists('ClinicaController')) {
    // Definição temporária da classe ClinicaController
    class ClinicaController {
        private $db;
        
        public function __construct($database) {
            $this->db = $database;
        }
        
        public function listarAtivas() {
            $query = "SELECT * FROM clinicas_parceiras WHERE status = 1 ORDER BY nome";
            return $this->db->query($query);
        }
    }
}

// Inicializa os controladores
$procedimentoController = new ProcedimentoController($db);
$clinicaController = new ClinicaController($db);
$procClinicaController = new ProcedimentoClinicaController($db);

// Resto do código...

// Busca o procedimento
$procedimento_id = isset($_GET['procedimento_id']) ? intval($_GET['procedimento_id']) : 0;
if (!$procedimento_id) {
    header('Location: index.php?page=procedimentos');
    exit;
}

$procedimento = $procedimentoController->buscarPorId($procedimento_id);
$clinicas = $clinicaController->listarAtivas();
$vinculosExistentes = $procClinicaController->buscarPorProcedimento($procedimento_id);

// Array para armazenar os vínculos por clinica_id
$vinculosPorClinica = [];
while ($v = $vinculosExistentes->fetch_assoc()) {
    $vinculosPorClinica[$v['clinica_id']] = $v;
}
?>

<div class="container-fluid">
    <h1 class="mt-4">Vincular Procedimento a Clínicas</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="index.php?page=procedimentos">Procedimentos</a></li>
        <li class="breadcrumb-item active">Vincular a Clínicas</li>
    </ol>
    
    <div class="alert alert-info">
        <strong>Procedimento:</strong> <?= $procedimento['procedimento'] ?> 
        <br>
        <strong>Valor Padrão:</strong> R$ <?= number_format($procedimento['valor'], 2, ',', '.') ?>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-link mr-1"></i> Clínicas que oferecem este procedimento
        </div>
        <div class="card-body">
            <form id="form-vinculos" method="post" action="ajax/salvar_vinculos_procedimentos.php">
                <input type="hidden" name="procedimento_id" value="<?= $procedimento_id ?>">
                
                <table class="table table-bordered" id="tabelaVinculos">
                    <thead>
                        <tr>
                            <th width="5%">Vincular</th>
                            <th width="25%">Clínica</th>
                            <th width="45%">Endereço</th>
                            <th width="15%">Valor (R$)</th>
                            <th width="10%">Observações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($c = $clinicas->fetch_assoc()): ?>
                            <?php 
                            $vinculado = isset($vinculosPorClinica[$c['id']]);
                            $valor = $vinculado ? $vinculosPorClinica[$c['id']]['valor'] : $procedimento['valor'];
                            $observacoes = $vinculado ? $vinculosPorClinica[$c['id']]['observacoes'] : '';
                            ?>
                            <tr>
                                <td class="text-center">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input clinica-check" 
                                               id="check_<?= $c['id'] ?>" name="clinicas[<?= $c['id'] ?>][ativo]" 
                                               value="1" <?= $vinculado ? 'checked' : '' ?>>
                                        <label class="custom-control-label" for="check_<?= $c['id'] ?>"></label>
                                    </div>
                                </td>
                                <td><?= $c['nome'] ?></td>
                                <td>
                                    <?= $c['endereco'] ?>, <?= $c['numero'] ?> - <?= $c['bairro'] ?>
                                    <br>
                                    <?= $c['cidade'] ?>/<?= $c['estado'] ?>
                                </td>
                                <td>
                                    <input type="text" class="form-control money clinica-valor" 
                                           name="clinicas[<?= $c['id'] ?>][valor]" 
                                           value="<?= number_format($valor, 2, ',', '.') ?>"
                                           <?= $vinculado ? '' : 'disabled' ?>>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-info btn-observacoes"
                                            data-clinica-id="<?= $c['id'] ?>"
                                            data-clinica-nome="<?= $c['nome'] ?>"
                                            data-observacoes="<?= htmlspecialchars($observacoes) ?>"
                                            <?= $vinculado ? '' : 'disabled' ?>>
                                        <i class="fas fa-comment"></i>
                                    </button>
                                    <input type="hidden" name="clinicas[<?= $c['id'] ?>][observacoes]" 
                                           id="obs_<?= $c['id'] ?>" value="<?= htmlspecialchars($observacoes) ?>">
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                
                <div class="form-group text-right mt-4">
                    <a href="index.php?page=procedimentos" class="btn btn-secondary">Voltar</a>
                    <button type="submit" class="btn btn-primary">Salvar Vínculos</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Observações -->
<div class="modal fade" id="modalObservacoes" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Observações - <span id="clinica-nome"></span></h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-observacoes">
                    <input type="hidden" id="modal-clinica-id">
                    <div class="form-group">
                        <label for="observacoes">Observações:</label>
                        <textarea class="form-control" id="observacoes" rows="5"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" id="btn-salvar-obs" class="btn btn-primary">Salvar</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Inicializa a tabela com DataTables
    $('#tabelaVinculos').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese-Brasil.json'
        }
    });
    
    // Inicializa máscaras
    $('.money').mask('#.##0,00', {reverse: true});
    
    // Gerencia ativação/desativação de campos
    $('.clinica-check').change(function() {
        var clinicaId = $(this).attr('id').replace('check_', '');
        var isChecked = $(this).is(':checked');
        
        $(this).closest('tr').find('.clinica-valor, .btn-observacoes').prop('disabled', !isChecked);
    });
    
    // Manipulação do modal de observações
    $('.btn-observacoes').click(function() {
        var clinicaId = $(this).data('clinica-id');
        var clinicaNome = $(this).data('clinica-nome');
        var observacoes = $(this).data('observacoes');
        
        $('#modal-clinica-id').val(clinicaId);
        $('#clinica-nome').text(clinicaNome);
        $('#observacoes').val(observacoes);
        
        $('#modalObservacoes').modal('show');
    });
    
    // Salvar observações do modal
    $('#btn-salvar-obs').click(function() {
        var clinicaId = $('#modal-clinica-id').val();
        var observacoes = $('#observacoes').val();
        
        // Atualiza o campo hidden com as observações
        $('#obs_' + clinicaId).val(observacoes);
        
        // Atualiza o data-observacoes para uso futuro
        $('.btn-observacoes[data-clinica-id="' + clinicaId + '"]').data('observacoes', observacoes);
        
        $('#modalObservacoes').modal('hide');
    });
    
    // Manipulação do formulário
    $('#form-vinculos').submit(function(e) {
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