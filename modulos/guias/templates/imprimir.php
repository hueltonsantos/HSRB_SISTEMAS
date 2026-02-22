<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guia de Encaminhamento #<?php echo $guiaData['codigo']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', Arial, sans-serif;
            font-size: 8pt;
            margin: 0;
            padding: 10px;
            color: #333;
            background-color: #f8f9fa;
        }

        .guia-container {
            max-width: 148mm;
            /* Largura A5 */
            margin: 0 auto;
            background: #fff;
            padding: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 4px;
        }

        .cabecalho {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 8px;
            border-bottom: 1.5px solid #0056b3;
            padding-bottom: 6px;
        }

        .titulo-principal {
            text-align: left;
            flex: 1;
        }

        .titulo-principal h1 {
            color: #0056b3;
            font-size: 12pt;
            margin: 0;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .titulo-principal h1 span {
            font-size: 16pt !important;
        }

        .titulo-principal h2 {
            font-size: 7pt;
            color: #666;
            margin: 2px 0 0;
            font-weight: 400;
        }

        .info-guia {
            text-align: right;
            min-width: 80px;
        }

        .numero-guia {
            font-size: 10pt;
            font-weight: bold;
            color: #333;
            background: #e9ecef;
            padding: 3px 8px;
            border-radius: 3px;
            display: inline-block;
        }

        .data-emissao {
            font-size: 7pt;
            color: #666;
            margin-top: 2px;
        }

        .secao-box {
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 8px 10px;
            margin-bottom: 6px;
            position: relative;
        }

        .bg-light-blue {
            background-color: #f8fbff;
            border-color: #b6d4fe;
        }

        .titulo-secao {
            position: absolute;
            top: -8px;
            left: 10px;
            background: #fff;
            padding: 0 6px;
            font-size: 7pt;
            font-weight: bold;
            color: #0056b3;
            text-transform: uppercase;
        }

        .bg-light-blue .titulo-secao {
            background: #f8fbff;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }

        .campo {
            margin-bottom: 4px;
            line-height: 1.2;
        }

        .campo:last-child {
            margin-bottom: 0;
        }

        .label {
            font-size: 6.5pt;
            color: #666;
            display: block;
            margin-bottom: 1px;
            text-transform: uppercase;
        }

        .valor {
            font-size: 9pt;
            font-weight: 500;
            color: #000;
        }

        .destaque-clinica {
            font-size: 9pt;
            color: #0056b3;
        }

        .proc-destaque {
            font-size: 10pt;
            font-weight: bold;
        }

        .instrucoes {
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            color: #856404;
            padding: 6px 8px;
            border-radius: 4px;
            font-size: 7.5pt;
            margin-top: 8px;
            line-height: 1.3;
        }

        .assinatura-area {
            margin-top: 15px;
            display: flex;
            justify-content: center;
        }

        .assinatura-box {
            text-align: center;
            width: 55%;
            border-top: 1px solid #333;
            padding-top: 4px;
            font-size: 8pt;
        }

        .assinatura-box small {
            font-size: 6.5pt;
            color: #666;
        }

        .rodape {
            margin-top: 10px;
            text-align: center;
            font-size: 6.5pt;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 6px;
        }

        .no-print {
            margin-top: 15px;
            text-align: center;
        }

        .btn {
            padding: 6px 12px;
            border-radius: 3px;
            text-decoration: none;
            color: #fff;
            font-weight: bold;
            cursor: pointer;
            border: none;
            display: inline-block;
            margin: 0 3px;
            font-size: 8pt;
        }

        .btn-primary {
            background-color: #0056b3;
        }

        .btn-secondary {
            background-color: #6c757d;
        }

        .btn-info {
            background-color: #17a2b8;
        }

        hr {
            border: none;
            border-top: 1px dashed #ccc;
            margin: 6px 0;
        }

        @media screen and (max-width: 768px) {
            body {
                padding: 5px;
            }

            .guia-container {
                padding: 10px;
                max-width: 100%;
            }

            .cabecalho {
                flex-direction: column;
                text-align: center;
            }

            .info-guia {
                text-align: center;
                margin-top: 5px;
            }

            .grid-2 {
                grid-template-columns: 1fr;
                gap: 6px;
            }

            .assinatura-box {
                width: 80%;
            }

            .no-print .btn {
                display: block;
                width: 100%;
                margin: 3px 0;
            }
        }

        @media print {
            @page {
                size: A5 portrait;
                margin: 4mm;
            }

            body {
                background: #fff;
                padding: 0;
                font-size: 8pt;
            }

            .guia-container {
                box-shadow: none;
                padding: 0;
                width: 100%;
                max-width: 100%;
                margin: 0;
            }

            .cabecalho {
                margin-bottom: 6px;
                padding-bottom: 4px;
            }

            .titulo-principal h1 {
                font-size: 11pt;
            }

            .titulo-principal h1 span {
                font-size: 14pt !important;
            }

            .secao-box {
                padding: 6px 8px;
                margin-bottom: 5px;
            }

            .titulo-secao {
                font-size: 6.5pt;
                top: -7px;
            }

            .valor {
                font-size: 8.5pt;
            }

            .proc-destaque {
                font-size: 9pt;
            }

            .assinatura-area {
                margin-top: 12px;
            }

            .rodape {
                margin-top: 8px;
                padding-top: 4px;
            }

            .no-print {
                display: none !important;
            }

            .titulo-secao,
            .bg-light-blue .titulo-secao {
                background: #fff;
            }

            .secao-box.bg-light-blue {
                background-color: #fff !important;
                border: 1px solid #ccc;
            }
        }
    </style>
    <base target="_blank">
</head>

<body>
    <div class="guia-container">
        <!-- Cabeçalho -->
        <div class="cabecalho">
            <div class="titulo-principal">
                <h1>
                    <span>🏥</span>
                    <?php echo htmlspecialchars($configs['nome_clinica'] ?? 'Guia de Encaminhamento'); ?>
                </h1>
                <h2>Documento de Solicitação de Exame/Procedimento</h2>
            </div>
            <div class="info-guia">
                <div class="numero-guia">Nº <?php echo $guiaData['codigo']; ?></div>
                <div class="data-emissao">Emissão: <?php echo $guiaData['data_emissao']; ?></div>
            </div>
        </div>

        <!-- Seção Paciente e Agendamento -->
        <div class="grid-2">
            <div class="secao-box">
                <div class="titulo-secao">Dados do Paciente</div>
                <div class="campo">
                    <span class="label">Nome Completo</span>
                    <span class="valor"><?php echo htmlspecialchars($guiaData['paciente_nome']); ?></span>
                </div>
                <?php if (!empty($guiaData['paciente_documento'])): ?>
                    <div class="campo">
                        <span class="label">CPF/Documento</span>
                        <span class="valor"><?php echo htmlspecialchars($guiaData['paciente_documento']); ?></span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="secao-box bg-light-blue">
                <div class="titulo-secao">Dados do Agendamento</div>
                <div class="campo">
                    <span class="label">Data Prevista</span>
                    <span class="valor"><?php echo date('d/m/Y', strtotime($guiaData['data_agendamento'])); ?></span>
                </div>
                <?php if (!empty($guiaData['horario_agendamento'])): ?>
                    <div class="campo">
                        <span class="label">Horário</span>
                        <span class="valor"><?php echo substr($guiaData['horario_agendamento'], 0, 5); ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Seção Procedimento e Clínica -->
        <div class="secao-box">
            <div class="titulo-secao">Solicitação</div>

            <?php foreach ($guiaData['procedimentos'] as $index => $proc): ?>
                <?php if ($index > 0)
                    echo '<hr>'; ?>
                <div class="grid-2">
                    <div>
                        <div class="campo">
                            <span class="label">Procedimento</span>
                            <span
                                class="valor proc-destaque"><?php echo htmlspecialchars($proc['procedimento'] ?? 'N/A'); ?></span>
                        </div>
                        <div class="campo">
                            <span class="label">Especialidade</span>
                            <span class="valor"><?php echo htmlspecialchars($proc['especialidade_nome'] ?? 'N/A'); ?></span>
                        </div>
                    </div>
                    <div>
                        <div class="campo">
                            <span class="label">Clínica Executante</span>
                            <span
                                class="valor destaque-clinica"><?php echo htmlspecialchars($proc['clinica_nome'] ?? 'A DEFINIR'); ?></span>
                        </div>
                        <?php if (!empty($proc['endereco'])): ?>
                            <div class="campo">
                                <span class="label">Endereço</span>
                                <span class="valor"><?php echo htmlspecialchars($proc['endereco']); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($proc['telefone'])): ?>
                            <div class="campo">
                                <span class="label">Telefone</span>
                                <span class="valor"><?php echo htmlspecialchars($proc['telefone']); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Instruções e Observações -->
        <?php if (!empty($guiaData['observacoes']) || !empty($guiaData['procedimento']['clinica_observacoes'])): ?>
            <div class="secao-box">
                <div class="titulo-secao">Observações</div>
                <?php if (!empty($guiaData['observacoes'])): ?>
                    <div class="campo">
                        <span class="label">Instruções do Médico:</span>
                        <p style="margin: 2px 0; font-size: 8pt;">
                            <?php echo nl2br(htmlspecialchars($guiaData['observacoes'])); ?>
                        </p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($guiaData['procedimento']['clinica_observacoes'])): ?>
                    <div class="campo" style="margin-top: 6px;">
                        <span class="label">Avisos da Clínica:</span>
                        <p style="margin: 2px 0; font-size: 8pt;">
                            <?php echo htmlspecialchars($guiaData['procedimento']['clinica_observacoes']); ?>
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Aviso Importante -->
        <div class="instrucoes">
            <strong>⚠️ Atenção:</strong> Apresente este documento e documento oficial com foto no dia do atendimento. Em
            caso de desistência, comunicar com 24h de antecedência.
        </div>

        <!-- Assinatura -->
        <div class="assinatura-area">
            <div class="assinatura-box">
                Assinatura do Profissional
                <br>
                <small>(Carimbo ou Assinatura Digital)</small>
            </div>
        </div>

        <div class="rodape">
            Sistema HSRB SISTEMAS © <?php echo date('Y'); ?>
        </div>
    </div>

    <div class="no-print">
        <button onclick="window.print();" class="btn btn-primary">🖨️ Imprimir</button>
        <button onclick="window.history.back();" class="btn btn-secondary">↩️ Voltar</button>
        <?php if (isset($guiaData['id'])): ?>
            <a href="index.php?module=guias&action=view&id=<?php echo $guiaData['id']; ?>" class="btn btn-info">📋
                Detalhes</a>
        <?php endif; ?>
    </div>
</body>

</html>