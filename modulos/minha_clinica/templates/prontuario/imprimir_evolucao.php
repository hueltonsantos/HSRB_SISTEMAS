<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Evolução Clínica - <?= htmlspecialchars($evolucao['paciente_nome']) ?></title>
    <style>
        @page { margin: 2cm; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333; line-height: 1.4; font-size: 14px; }
        .container { max-width: 800px; margin: 0 auto; }
        
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #444; padding-bottom: 10px; }
        .header h2 { margin: 0; font-size: 18px; text-transform: uppercase; color: #2c3e50; }
        .header h3 { margin: 5px 0 0; font-size: 14px; font-weight: normal; color: #7f8c8d; }

        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 20px; background: #f8f9fa; padding: 15px; border-radius: 5px; border: 1px solid #e9ecef; }
        .info-item label { display: block; font-size: 11px; color: #7f8c8d; text-transform: uppercase; font-weight: bold; }
        .info-item span { display: block; font-size: 14px; font-weight: 500; color: #2c3e50; }

        .content { margin-bottom: 30px; text-align: justify; }
        .content h4 { font-size: 14px; text-transform: uppercase; border-bottom: 1px solid #eee; padding-bottom: 5px; margin-bottom: 10px; color: #2c3e50; }
        .content-text { white-space: pre-wrap; }

        .signature-box { margin-top: 40px; page-break-inside: avoid; border: 1px solid #ccc; padding: 0; border-radius: 4px; overflow: hidden; max-width: 400px; margin-left: auto; margin-right: auto; }
        .signature-header { background: #eee; padding: 5px 10px; font-size: 11px; font-weight: bold; text-align: center; border-bottom: 1px solid #ccc; color: #555; }
        .signature-body { padding: 15px; text-align: center; }
        .signature-line { margin: 5px 0; font-size: 12px; }
        .signature-hash { margin-top: 10px; font-family: 'Courier New', monospace; font-size: 10px; background: #f8f9fa; padding: 4px; border: 1px solid #eee; }

        .footer { text-align: center; font-size: 10px; color: #999; margin-top: 30px; border-top: 1px solid #eee; padding-top: 10px; }

        @media print {
            body { margin: 0; -webkit-print-color-adjust: exact; }
            .no-print { display: none !important; }
            .container { max-width: 100%; }
        }
    </style>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="no-print" style="text-align: center; padding: 15px; background: #f0f0f0; border-bottom: 1px solid #ccc;">
        <button onclick="window.print()" style="padding: 8px 20px; font-size: 14px; cursor: pointer; background: #007bff; color: white; border: none; border-radius: 4px;">
            <i class="fas fa-print"></i> Imprimir Documento
        </button>
        <button onclick="window.close()" style="padding: 8px 20px; font-size: 14px; cursor: pointer; background: #6c757d; color: white; border: none; border-radius: 4px; margin-left: 10px;">
            Fechar
        </button>
    </div>

    <div class="container">
        <div class="header">
            <h2>Registro de Evolução Clínica</h2>
            <h3><?= defined('SYSTEM_NAME') ? SYSTEM_NAME : 'Minha Clínica' ?></h3>
        </div>

        <div class="info-grid">
            <div class="info-item">
                <label>Paciente</label>
                <span><?= htmlspecialchars($evolucao['paciente_nome']) ?></span>
            </div>
            <div class="info-item">
                <label>Data do Atendimento</label>
                <span><?= date('d/m/Y H:i', strtotime($evolucao['data_registro'])) ?></span>
            </div>
            <div class="info-item">
                <label>Profissional</label>
                <span>Dr(a). <?= htmlspecialchars($evolucao['profissional_nome']) ?></span>
            </div>
            <div class="info-item">
                <label>Registro Profissional</label>
                <span><?= htmlspecialchars($evolucao['registro_profissional']) ?></span>
            </div>
            <?php if (!empty($evolucao['cid10'])): ?>
                <div class="info-item" style="grid-column: 1 / -1;">
                    <label>CID-10 Principal</label>
                    <span><?= htmlspecialchars($evolucao['cid10']) ?></span>
                </div>
            <?php endif; ?>
        </div>

        <div class="content">
            <h4>Descrição da Evolução</h4>
            <div class="content-text"><?= nl2br(htmlspecialchars($evolucao['texto'])) ?></div>
        </div>

        <div class="signature-box">
            <div class="signature-header">
                <i class="fas fa-lock"></i> DOCUMENTO ASSINADO DIGITALMENTE
            </div>
            <div class="signature-body">
                <div class="signature-line"><strong>Dr(a). <?= htmlspecialchars($evolucao['profissional_nome']) ?></strong></div>
                <div class="signature-line text-muted">Licença: <?= htmlspecialchars($evolucao['registro_profissional']) ?></div>
                <div class="signature-line text-muted">Data: <?= date('d/m/Y H:i:s', strtotime($evolucao['data_registro'])) ?></div>
                
                <div class="signature-hash">
                    <strong>HASH:</strong> <?= $evolucao['assinatura_digital_hash'] ?? 'N/A' ?>
                </div>
            </div>
        </div>

        <div class="footer">
            Documento gerado eletronicamente em <?= date('d/m/Y H:i:s') ?>.<br>
            A validade deste documento pode ser verificada através do código Hash acima.
        </div>
    </div>
</body>
</html>