<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guia de Encaminhamento #<?php echo $guiaData['codigo']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', Arial, sans-serif;
            font-size: 11pt;
            margin: 0;
            padding: 20px;
            color: #333;
            background-color: #f8f9fa;
        }
        .guia-container {
            max-width: 210mm; /* A4 width */
            margin: 0 auto;
            background: #fff;
            padding: 40px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        .cabecalho {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #0056b3;
            padding-bottom: 20px;
        }
        .titulo-principal {
            text-align: left;
        }
        .titulo-principal h1 {
            color: #0056b3;
            font-size: 24pt;
            margin: 0;
            text-transform: uppercase;
        }
        .titulo-principal h2 {
            font-size: 12pt;
            color: #666;
            margin: 5px 0 0;
            font-weight: 400;
        }
        .info-guia {
            text-align: right;
        }
        .numero-guia {
            font-size: 14pt;
            font-weight: bold;
            color: #333;
            background: #e9ecef;
            padding: 5px 15px;
            border-radius: 4px;
        }
        .data-emissao {
            font-size: 9pt;
            color: #666;
            margin-top: 5px;
        }
        
        .secao-box {
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 20px;
            position: relative;
        }
        .bg-light-blue {
            background-color: #f8fbff;
            border-color: #b6d4fe;
        }
        .titulo-secao {
            position: absolute;
            top: -12px;
            left: 20px;
            background: #fff;
            padding: 0 10px;
            font-size: 11pt;
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
            gap: 20px;
        }
        
        .campo {
            margin-bottom: 10px;
        }
        .label {
            font-size: 9pt;
            color: #666;
            display: block;
            margin-bottom: 2px;
            text-transform: uppercase;
        }
        .valor {
            font-size: 11pt;
            font-weight: 500;
            color: #000;
        }

        .destaque-clinica {
            font-size: 12pt;
            color: #0056b3;
        }

        .instrucoes {
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            color: #856404;
            padding: 15px;
            border-radius: 6px;
            font-size: 10pt;
            margin-top: 30px;
        }

        .assinatura-area {
            margin-top: 60px;
            display: flex;
            justify-content: center;
        }
        .assinatura-box {
            text-align: center;
            width: 60%;
            border-top: 1px solid #333;
            padding-top: 10px;
        }

        .rodape {
            margin-top: 40px;
            text-align: center;
            font-size: 9pt;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }

        .no-print {
            margin-top: 30px;
            text-align: center;
        }
        .btn {
            padding: 10px 20px;
            border-radius: 4px;
            text-decoration: none;
            color: #fff;
            font-weight: bold;
            cursor: pointer;
            border: none;
            display: inline-block;
            margin: 0 5px;
        }
        .btn-primary { background-color: #0056b3; }
        .btn-secondary { background-color: #6c757d; }
        .btn-info { background-color: #17a2b8; }

        @media screen and (max-width: 768px) {
            body { padding: 10px; }
            .guia-container { padding: 15px; max-width: 100%; }
            .cabecalho { flex-direction: column; text-align: center; }
            .info-guia { text-align: center; margin-top: 10px; }
            .titulo-principal h1 { font-size: 16pt; flex-direction: column; }
            .titulo-principal h1 span { margin-right: 0 !important; margin-bottom: 5px; }
            .titulo-principal h2 { font-size: 10pt; }
            .grid-2 { grid-template-columns: 1fr; gap: 10px; }
            .assinatura-box { width: 90%; }
            .no-print .btn { display: block; width: 100%; margin: 5px 0; }
        }

        @media print {
            body { background: #fff; padding: 0; }
            .guia-container { box-shadow: none; padding: 0; width: 100%; max-width: 100%; margin: 0; }
            .no-print { display: none; }
            .titulo-secao, .bg-light-blue .titulo-secao { background: #fff; } /* Fix print transparency */
            .secao-box.bg-light-blue { background-color: #fff !important; border: 1px solid #ccc; }
        }
    </style>
</head>
<body>
    <div class="guia-container">
        <!-- Cabeçalho -->
        <div class="cabecalho">
            <div class="titulo-principal">
                <h1 style="display: flex; align-items: center;">
                    <span style="font-size: 30pt; margin-right: 10px;">🏥</span>
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
                <!-- Telefone removed as not available in current controller data -->
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
            <div class="grid-2">
                <div>
                    <div class="campo">
                        <span class="label">Procedimento Solicitado</span>
                        <span class="valor" style="font-size: 13pt; font-weight: bold;"><?php echo htmlspecialchars($guiaData['procedimento']['procedimento'] ?? 'N/A'); ?></span>
                    </div>
                    <div class="campo">
                        <span class="label">Especialidade</span>
                        <span class="valor"><?php echo htmlspecialchars($guiaData['procedimento']['especialidade_nome'] ?? 'N/A'); ?></span>
                    </div>
                    <!-- Preço removido conforme solicitado -->
                </div>
                <div>
                    <div class="campo">
                        <span class="label">Clínica Executante</span>
                        <span class="valor destaque-clinica"><?php echo htmlspecialchars($guiaData['procedimento']['clinica_nome'] ?? 'A DEFINIR'); ?></span>
                    </div>
                    <?php if (!empty($guiaData['procedimento']['endereco'])): ?>
                    <div class="campo">
                        <span class="label">Endereço</span>
                        <span class="valor"><?php echo htmlspecialchars($guiaData['procedimento']['endereco']); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($guiaData['procedimento']['telefone'])): ?>
                    <div class="campo">
                        <span class="label">Telefone para Contato</span>
                        <span class="valor"><?php echo htmlspecialchars($guiaData['procedimento']['telefone']); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Instruções e Observações -->
        <?php if (!empty($guiaData['observacoes']) || !empty($guiaData['procedimento']['clinica_observacoes'])): ?>
        <div class="secao-box">
            <div class="titulo-secao">Observações</div>
            <?php if (!empty($guiaData['observacoes'])): ?>
            <div class="campo">
                <span class="label">Instruções do Médico/Solicitante:</span>
                <p style="margin: 5px 0;"><?php echo nl2br(htmlspecialchars($guiaData['observacoes'])); ?></p>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($guiaData['procedimento']['clinica_observacoes'])): ?>
            <div class="campo" style="margin-top: 15px;">
                <span class="label">Avisos da Clínica Parceira:</span>
                <p style="margin: 5px 0;"><?php echo htmlspecialchars($guiaData['procedimento']['clinica_observacoes']); ?></p>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Aviso Importante -->
        <div class="instrucoes">
            <strong>⚠️ Atenção Paciente:</strong> É obrigatória a apresentação deste documento e de um documento oficial com foto no dia do atendimento. Em caso de desistência, favor comunicar com 24h de antecedência.
        </div>

        <!-- Assinatura -->
        <div class="assinatura-area">
            <div class="assinatura-box">
                Assinatura do Profissional Responsável
                <br>
                <small>(Carimbo ou Assinatura Digital)</small>
            </div>
        </div>

        <div class="rodape">
            Sistema de Gestão Clínica - HSRB SISTEMAS © <?php echo date('Y'); ?>
        </div>
    </div>
    
    <div class="no-print">
        <button onclick="window.print();" class="btn btn-primary">🖨️ Imprimir Guia</button>
        <button onclick="window.history.back();" class="btn btn-secondary">↩️ Voltar</button>
        <?php if(isset($guiaData['id'])): ?>
        <a href="index.php?module=guias&action=view&id=<?php echo $guiaData['id']; ?>" class="btn btn-info">📋 Detalhes do Sistema</a>
        <?php endif; ?>
    </div>
</body>
</html>