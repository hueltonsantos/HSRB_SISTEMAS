<div class="container-fluid">
    <div class="mb-3 d-print-none">
        <a href="index.php?module=caixa&action=listar" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
        <button onclick="window.print();" class="btn btn-primary">
            <i class="fas fa-print"></i> Imprimir Recibo
        </button>
    </div>

    <div class="card shadow" id="recibo-area">
        <div class="card-body" style="max-width: 600px; margin: 0 auto;">
            <!-- Cabeçalho -->
            <div class="text-center mb-4">
                <h4 class="font-weight-bold"><?php echo htmlspecialchars($nomeClinica); ?></h4>
                <?php if ($enderecoClinica): ?>
                    <p class="mb-0 small"><?php echo htmlspecialchars($enderecoClinica); ?></p>
                <?php endif; ?>
                <?php if ($telefoneClinica): ?>
                    <p class="mb-0 small">Tel: <?php echo htmlspecialchars($telefoneClinica); ?></p>
                <?php endif; ?>
                <hr>
                <h5>RECIBO DE PAGAMENTO</h5>
                <p class="text-muted small">N&ordm; <?php echo str_pad($lancamento['id'], 6, '0', STR_PAD_LEFT); ?></p>
            </div>

            <!-- Dados -->
            <table class="table table-borderless">
                <tr>
                    <td><strong>Data:</strong></td>
                    <td><?php echo $caixaModel->formatDateForDisplay($lancamento['data']); ?></td>
                </tr>
                <?php if ($lancamento['paciente_nome']): ?>
                <tr>
                    <td><strong>Paciente:</strong></td>
                    <td><?php echo htmlspecialchars($lancamento['paciente_nome']); ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($lancamento['paciente_cpf'])): ?>
                <tr>
                    <td><strong>CPF:</strong></td>
                    <td><?php echo htmlspecialchars($lancamento['paciente_cpf']); ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($lancamento['especialidade_nome'])): ?>
                <tr>
                    <td><strong>Especialidade:</strong></td>
                    <td><?php echo htmlspecialchars($lancamento['especialidade_nome']); ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td><strong>Descrição:</strong></td>
                    <td><?php echo htmlspecialchars($lancamento['descricao']); ?></td>
                </tr>
                <tr>
                    <td><strong>Forma de Pagamento:</strong></td>
                    <td><?php echo $formasPagamento[$lancamento['forma_pagamento']] ?? $lancamento['forma_pagamento']; ?></td>
                </tr>
            </table>

            <hr>

            <div class="text-center">
                <h4 class="font-weight-bold">
                    Valor: R$ <?php echo number_format($lancamento['valor'], 2, ',', '.'); ?>
                </h4>
            </div>

            <hr>

            <!-- Assinatura -->
            <div class="row mt-5">
                <div class="col-6 text-center">
                    <div style="border-top: 1px solid #333; padding-top: 5px; margin-top: 40px;">
                        Recebedor
                    </div>
                </div>
                <div class="col-6 text-center">
                    <div style="border-top: 1px solid #333; padding-top: 5px; margin-top: 40px;">
                        Paciente/Responsável
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <p class="text-muted small">
                    Emitido em <?php echo date('d/m/Y H:i'); ?> por <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>
                </p>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #recibo-area, #recibo-area * {
        visibility: visible;
    }
    #recibo-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        box-shadow: none !important;
        border: none !important;
    }
    .sidebar, .topbar, .sticky-footer, .scroll-to-top, .whatsapp-float {
        display: none !important;
    }
}
</style>
