<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Prontuário - <?= htmlspecialchars($paciente['nome']) ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        @page { margin: 2cm; }

        * { box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            line-height: 1.5;
            font-size: 13px;
            margin: 0;
            padding: 0;
        }

        .no-print {
            text-align: center;
            padding: 12px 20px;
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .no-print button, .no-print a {
            padding: 7px 18px;
            font-size: 13px;
            cursor: pointer;
            border-radius: 4px;
            border: none;
            margin: 0 4px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-print  { background: #007bff; color: #fff; }
        .btn-close  { background: #6c757d; color: #fff; }

        .container { max-width: 820px; margin: 20px auto; padding: 0 20px 40px; }

        /* Cabeçalho do documento */
        .doc-header {
            text-align: center;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }
        .doc-header h2 {
            margin: 0 0 4px;
            font-size: 17px;
            text-transform: uppercase;
            color: #2c3e50;
            letter-spacing: 1px;
        }
        .doc-header h3 {
            margin: 0;
            font-size: 13px;
            font-weight: normal;
            color: #7f8c8d;
        }

        /* Dados do paciente */
        .paciente-box {
            background: #f0f4f8;
            border: 1px solid #d0dde8;
            border-radius: 5px;
            padding: 12px 16px;
            margin-bottom: 20px;
        }
        .paciente-box .titulo {
            font-size: 10px;
            text-transform: uppercase;
            color: #7f8c8d;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .paciente-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px 16px;
        }
        .paciente-grid .item label {
            display: block;
            font-size: 10px;
            color: #7f8c8d;
            text-transform: uppercase;
            font-weight: bold;
        }
        .paciente-grid .item span {
            font-size: 13px;
            color: #2c3e50;
            font-weight: 500;
        }

        /* Período filtrado */
        .periodo-box {
            font-size: 11px;
            color: #666;
            text-align: center;
            margin-bottom: 16px;
        }

        /* Cada evolução */
        .evolucao {
            border: 1px solid #d0d7de;
            border-radius: 5px;
            margin-bottom: 22px;
            page-break-inside: avoid;
            overflow: hidden;
        }

        .evolucao-header {
            background: #2c3e50;
            color: #fff;
            padding: 8px 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .evolucao-header .data {
            font-weight: bold;
            font-size: 13px;
        }
        .evolucao-header .profissional {
            font-size: 12px;
            opacity: 0.9;
        }

        .evolucao-body {
            padding: 14px 16px;
        }

        .evolucao-meta {
            display: flex;
            gap: 16px;
            margin-bottom: 10px;
            flex-wrap: wrap;
        }
        .evolucao-meta .meta-item label {
            display: block;
            font-size: 10px;
            text-transform: uppercase;
            color: #999;
            font-weight: bold;
        }
        .evolucao-meta .meta-item span {
            font-size: 12px;
            color: #444;
        }

        .evolucao-texto {
            white-space: pre-wrap;
            font-size: 13px;
            line-height: 1.7;
            color: #333;
            border-top: 1px solid #eee;
            padding-top: 10px;
            margin-top: 4px;
        }

        .assinatura {
            margin-top: 12px;
            padding: 8px 12px;
            background: #f8f9fa;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            font-size: 11px;
            color: #555;
        }
        .assinatura .hash {
            font-family: 'Courier New', monospace;
            font-size: 10px;
            color: #888;
            word-break: break-all;
        }

        /* Separador de página entre evoluções longas */
        .page-break { page-break-after: always; }

        /* Rodapé */
        .doc-footer {
            text-align: center;
            font-size: 10px;
            color: #aaa;
            border-top: 1px solid #eee;
            padding-top: 10px;
            margin-top: 30px;
        }

        .total-badge {
            display: inline-block;
            background: #2c3e50;
            color: #fff;
            border-radius: 20px;
            padding: 3px 12px;
            font-size: 11px;
            margin-bottom: 16px;
        }

        @media print {
            body { margin: 0; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
            .container { max-width: 100%; margin: 0; padding: 0; }
        }
    </style>
</head>
<body>

    <!-- Barra de ações (não imprime) -->
    <div class="no-print">
        <button class="btn-print" onclick="window.print()">
            <i class="fas fa-print"></i> Imprimir / Salvar PDF
        </button>
        <button class="btn-close" onclick="window.close()">
            Fechar
        </button>
        <span style="margin-left:16px; color:#666; font-size:12px;">
            <?= count($evolucoes) ?> evolução(ões) &bull;
            <?= htmlspecialchars($paciente['nome']) ?>
        </span>
    </div>

    <div class="container">

        <!-- Cabeçalho -->
        <div class="doc-header">
            <h2>Prontuário Eletrônico</h2>
            <h3><?= defined('SYSTEM_NAME') ? htmlspecialchars(SYSTEM_NAME) : 'Minha Clínica' ?></h3>
        </div>

        <!-- Dados do Paciente -->
        <div class="paciente-box">
            <div class="titulo"><i class="fas fa-user"></i> Dados do Paciente</div>
            <div class="paciente-grid">
                <div class="item">
                    <label>Nome</label>
                    <span><?= htmlspecialchars($paciente['nome']) ?></span>
                </div>
                <div class="item">
                    <label>Data de Nascimento</label>
                    <span>
                        <?= !empty($paciente['data_nascimento']) ? date('d/m/Y', strtotime($paciente['data_nascimento'])) : '-' ?>
                        <?= !empty($idade) ? "($idade)" : '' ?>
                    </span>
                </div>
                <div class="item">
                    <label>Sexo</label>
                    <span>
                        <?= $paciente['sexo'] == 'M' ? 'Masculino' : ($paciente['sexo'] == 'F' ? 'Feminino' : 'Outro') ?>
                    </span>
                </div>
                <div class="item">
                    <label>CPF</label>
                    <span><?= htmlspecialchars($paciente['cpf'] ?: '-') ?></span>
                </div>
                <div class="item">
                    <label>Convênio</label>
                    <span><?= htmlspecialchars($paciente['convenio'] ?: 'Particular') ?></span>
                </div>
                <div class="item">
                    <label>Telefone / Celular</label>
                    <span><?= htmlspecialchars($paciente['celular'] ?: $paciente['telefone_fixo'] ?: '-') ?></span>
                </div>
            </div>
        </div>

        <?php if (!empty($de) || !empty($ate)): ?>
            <div class="periodo-box">
                <i class="fas fa-calendar-alt"></i>
                Período:
                <?= !empty($de)  ? date('d/m/Y', strtotime($de))  : 'início' ?>
                até
                <?= !empty($ate) ? date('d/m/Y', strtotime($ate)) : 'hoje' ?>
            </div>
        <?php endif; ?>

        <div style="text-align:center;">
            <span class="total-badge">
                <i class="fas fa-notes-medical"></i>
                <?= count($evolucoes) ?> Registro(s) de Evolução
            </span>
        </div>

        <!-- Evoluções -->
        <?php foreach ($evolucoes as $i => $ev): ?>
            <div class="evolucao">
                <div class="evolucao-header">
                    <div>
                        <div class="data">
                            <i class="fas fa-calendar-day"></i>
                            <?= date('d/m/Y', strtotime($ev['data_registro'])) ?>
                            às <?= date('H:i', strtotime($ev['data_registro'])) ?>
                        </div>
                    </div>
                    <div class="profissional">
                        <i class="fas fa-user-md"></i>
                        Dr(a). <?= htmlspecialchars($ev['profissional_nome']) ?>
                        <?php if (!empty($ev['registro_profissional'])): ?>
                            &mdash; <?= htmlspecialchars($ev['registro_profissional']) ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="evolucao-body">
                    <?php if (!empty($ev['cid10']) || isset($ev['versao'])): ?>
                        <div class="evolucao-meta">
                            <?php if (!empty($ev['cid10'])): ?>
                                <div class="meta-item">
                                    <label>CID-10</label>
                                    <span><?= htmlspecialchars($ev['cid10']) ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if (isset($ev['versao']) && $ev['versao'] > 1): ?>
                                <div class="meta-item">
                                    <label>Versão</label>
                                    <span>v<?= $ev['versao'] ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="evolucao-texto"><?= nl2br(htmlspecialchars($ev['texto'])) ?></div>

                    <?php if (!empty($ev['assinatura_digital_hash'])): ?>
                        <div class="assinatura">
                            <i class="fas fa-lock text-success"></i>
                            <strong>Documento assinado digitalmente</strong><br>
                            <span class="hash">HASH: <?= $ev['assinatura_digital_hash'] ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Rodapé -->
        <div class="doc-footer">
            Documento gerado eletronicamente em <?= date('d/m/Y \à\s H:i:s') ?> &bull;
            <?= defined('SYSTEM_NAME') ? htmlspecialchars(SYSTEM_NAME) : 'Sistema de Gestão Clínica' ?><br>
            Este documento possui validade legal quando assinado digitalmente.
        </div>

    </div>
</body>
</html>
