<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<<<<<<< HEAD
    <title>Acesso Negado - HSRB_SISTEMAS</title>
    <link href="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.1.4/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fc;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card-negado {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 .15rem 1.75rem 0 rgba(58,59,69,.15);
            padding: 50px 40px;
            text-align: center;
            max-width: 520px;
            width: 90%;
            border-top: 4px solid #4e73df;
            transition: all 0.5s ease;
        }

        /* === CENA NORMAL (tentativas 1-3) === */
        .scene {
            position: relative;
            height: 130px;
            margin-bottom: 10px;
        }
        .doctor {
            position: absolute; font-size: 4.5rem; bottom: 0; left: 50%;
            transform: translateX(-140%);
            animation: walk-and-bump 3s ease-in-out infinite;
        }
        .door {
            position: absolute; font-size: 4.5rem; bottom: 0; left: 50%;
            transform: translateX(20%);
            animation: door-shake 3s ease-in-out infinite;
        }
        .padlock {
            position: absolute; font-size: 1.8rem; bottom: 50px; left: 50%;
            transform: translateX(120%);
            animation: lock-pulse 3s ease-in-out infinite;
        }
        .impact {
            position: absolute; font-size: 1.6rem; bottom: 60px; left: 50%;
            opacity: 0;
            animation: impact-appear 3s ease-in-out infinite;
        }

        @keyframes walk-and-bump {
            0%   { transform: translateX(-220%); }
            40%  { transform: translateX(-140%); }
            50%  { transform: translateX(-118%) rotate(-6deg); }
            60%  { transform: translateX(-140%) rotate(0deg); }
            80%  { transform: translateX(-140%); }
            100% { transform: translateX(-220%); }
        }
        @keyframes door-shake {
            0%,50%   { transform: translateX(20%) rotate(0deg); }
            53%      { transform: translateX(20%) rotate(-4deg); }
            56%      { transform: translateX(20%) rotate(4deg); }
            59%      { transform: translateX(20%) rotate(-2deg); }
            62%,100% { transform: translateX(20%) rotate(0deg); }
        }
        @keyframes lock-pulse {
            0%,50%   { transform: translateX(120%) scale(1); }
            55%      { transform: translateX(120%) scale(1.5); }
            65%,100% { transform: translateX(120%) scale(1); }
        }
        @keyframes impact-appear {
            0%,47%   { opacity: 0; transform: translateX(-30%) scale(0.5); }
            53%      { opacity: 1; transform: translateX(-30%) scale(1.4); }
            65%      { opacity: 1; transform: translateX(-30%) scale(1); }
            75%,100% { opacity: 0; }
        }

        /* === CENA ENFERMEIRA (4ª tentativa+) === */
        .scene-enfermeira {
            position: relative;
            height: 140px;
            margin-bottom: 10px;
            display: none;
        }

        .door-open {
            position: absolute; font-size: 4.5rem; bottom: 0; left: 50%;
            transform: translateX(-30%);
            animation: door-open-anim 1s ease-out forwards;
        }
        .nurse {
            position: absolute; font-size: 4.5rem; bottom: 0; left: 50%;
            transform: translateX(-200%);
            animation: nurse-enter 1.2s ease-out 0.3s forwards;
        }
        .bandeja {
            position: absolute; font-size: 2rem; bottom: 55px; left: 50%;
            transform: translateX(-200%);
            opacity: 0;
            animation: bandeja-enter 1.2s ease-out 0.5s forwards;
        }
        .brilho {
            position: absolute; font-size: 1.2rem; bottom: 90px; left: 50%;
            transform: translateX(-10%);
            opacity: 0;
            animation: brilho-pulse 1s ease-in-out 1.5s infinite;
        }

        @keyframes door-open-anim {
            0%   { transform: translateX(-30%) rotate(0deg); }
            100% { transform: translateX(-30%) rotate(15deg); }
        }
        @keyframes nurse-enter {
            0%   { transform: translateX(-200%); }
            100% { transform: translateX(-90%); }
        }
        @keyframes bandeja-enter {
            0%   { transform: translateX(-200%); opacity: 0; }
            100% { transform: translateX(-80%); opacity: 1; }
        }
        @keyframes brilho-pulse {
            0%,100% { opacity: 0; transform: translateX(-10%) scale(1); }
            50%     { opacity: 1; transform: translateX(-10%) scale(1.3); }
        }

        /* Card do contato TI */
        .cartao-ti {
            display: none;
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            border-radius: 10px;
            padding: 20px 25px;
            margin-bottom: 25px;
            color: #fff;
            text-align: left;
            box-shadow: 0 5px 20px rgba(78,115,223,0.4);
            animation: slide-in 0.8s ease-out 1.5s both;
        }
        @keyframes slide-in {
            0%   { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .cartao-ti .ti-label {
            font-size: 0.7rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: 2px;
            opacity: 0.7; margin-bottom: 10px;
        }
        .cartao-ti .ti-nome {
            font-size: 1.1rem; font-weight: 700; margin-bottom: 6px;
        }
        .cartao-ti .ti-fone {
            font-size: 1.4rem; font-weight: 900; letter-spacing: 1px;
        }
        .cartao-ti .ti-fone i {
            font-size: 1rem; margin-right: 8px; opacity: 0.8;
        }
        .cartao-ti .ti-whats {
            margin-top: 10px;
        }
        .cartao-ti .ti-whats a {
            color: #fff;
            font-size: 0.85rem;
            opacity: 0.85;
            text-decoration: none;
        }
        .cartao-ti .ti-whats a:hover { opacity: 1; text-decoration: underline; }

        /* Textos */
        .error-code {
            font-size: 5rem; font-weight: 900;
            color: #e74a3b; line-height: 1;
        }
        .error-title {
            font-size: 1.2rem; font-weight: 700;
            color: #5a5c69; text-transform: uppercase;
            letter-spacing: 2px; margin: 8px 0 5px;
        }
        .error-msg {
            font-size: 0.95rem; color: #858796;
            margin-bottom: 25px; line-height: 1.6;
        }

        /* Laudo */
        .diagnosis {
            background: #fff5f5;
            border: 1px solid #f5c6cb;
            border-radius: 6px;
            padding: 15px 20px;
            margin-bottom: 30px;
            text-align: left;
        }
        .diagnosis-title {
            font-size: 0.72rem; font-weight: 700;
            color: #e74a3b; text-transform: uppercase;
            letter-spacing: 2px; margin-bottom: 8px;
        }
        .diagnosis-item { font-size: 0.85rem; color: #5a5c69; margin: 5px 0; }
        .diagnosis-item i { width: 16px; margin-right: 6px; color: #e74a3b; }
        .diagnosis-item.ok i { color: #1cc88a; }

        .tentativas { margin-top: 15px; font-size: 0.75rem; color: #b7b9cc; }
    </style>
</head>
<body>

<div class="card-negado" id="card">

    <!-- CENA NORMAL -->
    <div class="scene" id="cena-normal">
        <div class="doctor">🧑‍⚕️</div>
        <div class="impact">💥</div>
        <div class="door">🚪</div>
        <div class="padlock">🔒</div>
    </div>

    <!-- CENA ENFERMEIRA (4ª+ tentativa) -->
    <div class="scene-enfermeira" id="cena-enfermeira">
        <div class="door-open">🚪</div>
        <div class="nurse">👩‍⚕️</div>
        <div class="bandeja">🍽️</div>
        <div class="brilho">✨</div>
    </div>

    <div class="error-code" id="error-code">403</div>
    <div class="error-title" id="error-title">Acesso Negado</div>
    <p class="error-msg" id="error-msg">
        Você tentou acessar uma área restrita.<br>
        Seu perfil não tem permissão para esta seção.
    </p>

    <!-- Laudo normal -->
    <div class="diagnosis" id="laudo-normal">
        <div class="diagnosis-title">📋 Laudo do Sistema</div>
        <div class="diagnosis-item"><i class="fas fa-times-circle"></i> Permissão insuficiente detectada</div>
        <div class="diagnosis-item"><i class="fas fa-times-circle"></i> Crachá sem liberação para esta ala</div>
        <div class="diagnosis-item ok"><i class="fas fa-check-circle"></i> Usuário encaminhado ao Dashboard</div>
    </div>

    <!-- Cartão TI (4ª+ tentativa) -->
    <div class="cartao-ti" id="cartao-ti">
        <div class="ti-label">🍽️ A enfermeira trouxe na bandeja...</div>
        <div class="ti-nome">👨‍💻 Huelton S R Borges — TI</div>
        <div class="ti-fone"><i class="fas fa-phone-alt"></i> (77) 99988-2930</div>
        <div class="ti-whats">
            <a href="https://wa.me/5577999882930?text=Ol%C3%A1%20Huelton%2C%20n%C3%A3o%20consigo%20acessar%20o%20sistema%20%F0%9F%98%85" target="_blank">
                <i class="fab fa-whatsapp"></i> Chamar no WhatsApp
            </a>
        </div>
    </div>

    <a href="index.php?module=dashboard" class="btn btn-primary btn-icon-split">
        <span class="icon text-white-50"><i class="fas fa-arrow-left"></i></span>
        <span class="text">Voltar ao Dashboard</span>
    </a>

    <div class="tentativas" id="tentativas"></div>
</div>

<script>
    let count = parseInt(sessionStorage.getItem('acesso_negado') || '0') + 1;
    sessionStorage.setItem('acesso_negado', count);

    const el = document.getElementById('tentativas');

    if (count >= 4) {
        // Troca cena
        document.getElementById('cena-normal').style.display = 'none';
        document.getElementById('cena-enfermeira').style.display = 'block';

        // Troca textos
        document.getElementById('error-code').textContent = '🆘';
        document.getElementById('error-code').style.fontSize = '3.5rem';
        document.getElementById('error-title').textContent = 'Socorro Enviado!';
        document.getElementById('error-msg').innerHTML =
            'Você tentou tantas vezes que a enfermeira foi buscar ajuda.<br>O contato do TI chegou na bandeja. 🍽️';

        // Troca laudo pelo cartão
        document.getElementById('laudo-normal').style.display = 'none';
        document.getElementById('cartao-ti').style.display = 'block';

        // Borda vermelha no card
        document.getElementById('card').style.borderTopColor = '#e74a3b';

        el.textContent = `${count}ª tentativa. A enfermeira já foi buscar o TI. 🏃‍♀️`;
    } else {
        const msgs = [
            '',
            'Tentativa de acesso registrada.',
            `${count}ª tentativa... persistente, hein? 🤨`,
            `${count}ª tentativa... isso vai pro prontuário. 📋`,
        ];
        el.textContent = msgs[count] || '';
    }
</script>
</body>
</html>
=======
    <title>Acesso Negado</title>
    <!-- SB Admin 2 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.1.4/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" type="text/css">
</head>
<body class="bg-light">
    <div class="container text-center" style="margin-top: 100px;">
        <div class="error mx-auto" data-text="403">403</div>
        <p class="lead text-gray-800 mb-5">Acesso Negado</p>
        <p class="text-gray-500 mb-0">Parece que você não tem permissão para acessar esta página.</p>
        <a href="index.php">&larr; Voltar para o Dashboard</a>
    </div>
</body>
</html>
>>>>>>> acfed81619c575d93a5d861738c0a6b65ada5750
