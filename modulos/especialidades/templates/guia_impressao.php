<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guia de Encaminhamento #<?php echo $guia['codigo']; ?></title>
    <style>
        /* Estilos permanecem os mesmos */
        body {
            font-family: Arial, sans-serif;
            font-size: 12pt;
            margin: 0;
            padding: 0;
        }
        .guia {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #000;
        }
        .cabecalho {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #000;
            padding-bottom: 10px;
        }
        .logo {
            max-width: 150px;
            max-height: 80px;
            float: right;
        }
        .titulo {
            font-size: 18pt;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .subtitulo {
            font-size: 14pt;
            margin-bottom: 20px;
        }
        .numero-guia {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 10px;
            text-align: center;
        }
        .secao {
            margin-bottom: 15px;
        }
        .secao h3 {
            margin: 0 0 5px 0;
            border-bottom: 1px solid #ccc;
            padding-bottom: 3px;
        }
        .campo {
            margin-bottom: 8px;
        }
        .campo strong {
            font-weight: bold;
        }
        .assinatura {
            margin-top: 50px;
            text-align: center;
        }
        .linha-assinatura {
            border-top: 1px solid #000;
            width: 70%;
            margin: 10px auto;
            padding-top: 5px;
        }
        .instrucoes {
            margin-top: 30px;
            border-top: 1px dashed #000;
            padding-top: 10px;
            font-size: 10pt;
        }
        .data-emissao {
            margin-top: 20px;
            font-style: italic;
            text-align: right;
            font-size: 10pt;
        }
        .no-print {
            margin-top: 20px;
            text-align: center;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="guia">
        <div class="cabecalho">
            <div class="logo">
                <!-- <img src="caminho/para/seu/logo.png" alt="Logo da Clínica"> -->
                <!-- Substitua pelo caminho correto do seu logo, se disponível -->
            </div>
            <div class="titulo">GUIA DE ENCAMINHAMENTO</div>
            <div class="subtitulo">Nós cuidamos da sua saúde!</div>
        </div>
        
        <div class="numero-guia">
            Guia Nº: <?php echo $guia['codigo']; ?>
        </div>
        
        <div class="secao">
            <h3>Paciente</h3>
            <div class="campo"><strong>Nome:</strong> <?php echo htmlspecialchars($guia['paciente_nome']); ?></div>
            <?php if (!empty($guia['paciente_documento'])): ?>
            <div class="campo"><strong>Documento:</strong> <?php echo htmlspecialchars($guia['paciente_documento']); ?></div>
            <?php endif; ?>
        </div>
        
        <div class="secao">
            <h3>Parceiro</h3>
            <div class="campo"><strong><?php echo htmlspecialchars($guia['procedimento']['clinica_nome'] ?? 'CLÍNICA NÃO DEFINIDA'); ?></strong></div>
            <?php if (!empty($guia['procedimento']['endereco'])): ?>
            <div class="campo"><strong>Endereço:</strong> <?php echo htmlspecialchars($guia['procedimento']['endereco']); ?></div>
            <?php endif; ?>
            <?php if (!empty($guia['procedimento']['telefone'])): ?>
            <div class="campo"><strong>Telefone:</strong> <?php echo htmlspecialchars($guia['procedimento']['telefone']); ?></div>
            <?php endif; ?>
        </div>
        
        <div class="secao">
            <h3>Procedimento</h3>
            <div class="campo"><strong><?php echo htmlspecialchars($guia['procedimento']['procedimento']); ?></strong></div>
            <div class="campo"><strong>Especialidade:</strong> <?php echo htmlspecialchars($guia['procedimento']['especialidade_nome']); ?></div>
            <div class="campo"><strong>Valor:</strong> R$ <?php echo number_format($guia['procedimento']['valor'], 2, ',', '.'); ?></div>
        </div>
        
        <div class="secao">
            <h3>Agendamento</h3>
            <div class="campo"><strong>Data:</strong> <?php echo date('d/m/Y', strtotime($guia['data_agendamento'])); ?></div>
            <?php if (!empty($guia['horario_agendamento'])): ?>
            <div class="campo"><strong>Horário:</strong> <?php echo $guia['horario_agendamento']; ?></div>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($guia['observacoes'])): ?>
        <div class="secao">
            <h3>Instruções/Observações</h3>
            <div class="campo"><?php echo nl2br(htmlspecialchars($guia['observacoes'])); ?></div>
        </div>
        <?php endif; ?>
        
        <div class="assinatura">
            <div class="linha-assinatura"></div>
            <div>Assinatura do Responsável</div>
        </div>
        
        <div class="instrucoes">
            <p><strong>Atenção:</strong> É necessário apresentar esta guia no dia do atendimento. Qualquer dúvida, entre em contato com a clínica parceira informada neste documento.</p>
            <?php if (!empty($guia['procedimento']['clinica_observacoes'])): ?>
            <p><strong>Observações da Clínica:</strong> <?php echo htmlspecialchars($guia['procedimento']['clinica_observacoes']); ?></p>
            <?php endif; ?>
        </div>
        
        <div class="data-emissao">
            <div>Emissão: <?php echo $guia['data_emissao']; ?></div>
        </div>
    </div>
    
    <div class="no-print">
        <button onclick="window.print();" class="btn btn-primary">Imprimir Guia</button>
        <button onclick="window.history.back();" class="btn btn-secondary">Voltar</button>
        <a href="index.php?module=guias&action=view&id=<?php echo $guia['id']; ?>" class="btn btn-info">Ver Detalhes da Guia</a>
    </div>
</body>
</html>