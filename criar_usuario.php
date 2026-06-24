<?php
session_start();
include 'config.php';

// Se chegou via POST, processa o cadastro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = sanitizar($_POST['nome'] ?? '');
    $email = sanitizar($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $nickname = sanitizar($_POST['nickname'] ?? '');
    
    $erros = [];
    
    if (empty($nome)) $erros[] = 'Nome é obrigatório';
    if (empty($email) || !validar_email($email)) $erros[] = 'Email inválido';
    if (empty($senha) || strlen($senha) < 6) $erros[] = 'Senha deve ter no mínimo 6 caracteres';
    if (empty($nickname)) $erros[] = 'Nickname é obrigatório';
    
    if (empty($erros)) {
        try {
            // Usar password_hash para armazenar a senha com segurança
            $senhaHash = password_hash($senha, PASSWORD_BCRYPT);
            
            $stmt = $conexao->prepare('INSERT INTO usuarios (nome, email, senha, nickname, rank, plataforma) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([
                $nome,
                $email,
                $senhaHash,
                $nickname,
                'Iniciante',
                'PC'
            ]);
            
            $sucesso = 'Usuário criado com sucesso! Faça login com suas credenciais.';
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate') !== false) {
                $erros[] = 'Email ou nickname já estão em uso';
            } else {
                $erros[] = 'Erro ao criar usuário: ' . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Usuário de Teste - JK Game Match</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #1a1a1a;
            color: #fff;
            padding: 20px;
        }
        .container {
            max-width: 500px;
            margin: 50px auto;
            background: #222;
            padding: 30px;
            border-radius: 8px;
            border: 1px solid #00d4ff;
        }
        h1 {
            color: #00d4ff;
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #00d4ff;
        }
        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #00d4ff;
            border-radius: 4px;
            background: #111;
            color: #fff;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #00d4ff;
            border: none;
            border-radius: 4px;
            color: #000;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover {
            background: #00a8cc;
        }
        .erro {
            color: #ff4444;
            background: #222;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            border-left: 3px solid #ff4444;
        }
        .sucesso {
            color: #44ff44;
            background: #222;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            border-left: 3px solid #44ff44;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
        }
        a {
            color: #00d4ff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎮 Criar Usuário</h1>
        
        <?php if (!empty($erros)): ?>
            <?php foreach ($erros as $erro): ?>
                <div class="erro">❌ <?php echo $erro; ?></div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <?php if (isset($sucesso)): ?>
            <div class="sucesso">✅ <?php echo $sucesso; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="nome">Nome</label>
                <input type="text" id="nome" name="nome" value="<?php echo $_POST['nome'] ?? ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo $_POST['email'] ?? ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="nickname">Nickname</label>
                <input type="text" id="nickname" name="nickname" value="<?php echo $_POST['nickname'] ?? ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" required>
            </div>
            
            <button type="submit">Criar Usuário</button>
        </form>
        
        <div class="footer">
            <p>Após criar o usuário, <a href="index.html">faça login aqui</a></p>
        </div>
    </div>
</body>
</html>
