<?php
session_start();

// Limpar mensagens de sessão após exibir
$sucesso = $_SESSION['sucesso'] ?? null;
$erro = $_SESSION['erro'] ?? null;

if ($sucesso) unset($_SESSION['sucesso']);
if ($erro) unset($_SESSION['erro']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JK Game Match - Bem-vindo</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .welcome-container {
            background: linear-gradient(135deg, #1a1a1a 0%, #0d0d0d 100%);
            min-height: 100vh;
            padding: 40px 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .welcome-box {
            background: rgba(34, 34, 34, 0.95);
            border: 2px solid #00d4ff;
            border-radius: 12px;
            padding: 50px;
            max-width: 600px;
            text-align: center;
            box-shadow: 0 0 30px rgba(0, 212, 255, 0.2);
        }
        .welcome-logo {
            font-size: 60px;
            margin-bottom: 20px;
        }
        .welcome-title {
            color: #00d4ff;
            font-size: 32px;
            margin-bottom: 10px;
        }
        .welcome-subtitle {
            color: #888;
            font-size: 16px;
            margin-bottom: 30px;
        }
        .action-buttons {
            display: flex;
            gap: 15px;
            flex-direction: column;
            margin-bottom: 30px;
        }
        .btn-action {
            padding: 12px 30px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            font-weight: bold;
        }
        .btn-primary {
            background: #00d4ff;
            color: #000;
        }
        .btn-primary:hover {
            background: #00a8cc;
            transform: translateY(-2px);
        }
        .btn-secondary {
            background: transparent;
            color: #00d4ff;
            border: 2px solid #00d4ff;
        }
        .btn-secondary:hover {
            background: rgba(0, 212, 255, 0.1);
        }
        .info-section {
            text-align: left;
            background: rgba(0, 212, 255, 0.05);
            border-left: 4px solid #00d4ff;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .info-section h4 {
            color: #00d4ff;
            margin: 0 0 10px 0;
        }
        .info-section p {
            margin: 5px 0;
            color: #ccc;
        }
        .message {
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .message.success {
            background: rgba(68, 255, 68, 0.1);
            color: #44ff44;
            border-left: 4px solid #44ff44;
        }
        .message.error {
            background: rgba(255, 68, 68, 0.1);
            color: #ff4444;
            border-left: 4px solid #ff4444;
        }
    </style>
</head>
<body>
    <div class="welcome-container">
        <div class="welcome-box">
            <div class="welcome-logo">
                <i class="fas fa-gamepad"></i>
            </div>
            <h1 class="welcome-title">JK Game Match</h1>
            <p class="welcome-subtitle">Encontre seus Companheiros de Jogo</p>
            
            <?php if ($sucesso): ?>
                <div class="message success">✅ <?php echo $sucesso; ?></div>
            <?php endif; ?>
            
            <?php if ($erro): ?>
                <div class="message error">❌ <?php echo $erro; ?></div>
            <?php endif; ?>
            
            <div class="info-section">
                <h4>🚀 Bem-vindo ao JK Game Match!</h4>
                <p>Uma plataforma moderna para encontrar companheiros de jogo e criar matches incríveis.</p>
            </div>
            
            <div class="action-buttons">
                <a href="index.html" class="btn-action btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Fazer Login
                </a>
                <a href="cadastro.html" class="btn-action btn-secondary">
                    <i class="fas fa-user-plus"></i> Criar Conta
                </a>
            </div>
            
            <div class="info-section">
                <h4>⚙️ Primeiro acesso?</h4>
                <p>1. Clique em <strong>"Criar Conta"</strong></p>
                <p>2. Preencha seus dados e escolha seu rank</p>
                <p>3. Após cadastro, você será redirecionado para o dashboard</p>
                <p>4. Explore jogadores, crie matches e faça novas amizades!</p>
            </div>
            
            <div class="info-section">
                <h4>🔧 Ferramentas de Desenvolvimento</h4>
                <p><a href="setup_banco.php" style="color: #00d4ff;">Setup Banco de Dados</a> | 
                   <a href="teste_banco.php" style="color: #00d4ff;">Testar Conexão</a> | 
                   <a href="criar_usuario.php" style="color: #00d4ff;">Criar Usuário</a></p>
            </div>
        </div>
    </div>
</body>
</html>
