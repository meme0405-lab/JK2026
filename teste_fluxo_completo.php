<?php
// teste_fluxo_completo.php
// Testa o fluxo completo de cadastro e login

session_start();
include 'config.php';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste Fluxo Completo - JK Game Match</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #1a1a1a;
            color: #fff;
            padding: 20px;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: #222;
            padding: 30px;
            border-radius: 8px;
            border: 2px solid #00d4ff;
        }
        h1, h2 {
            color: #00d4ff;
        }
        .teste-section {
            background: rgba(0, 212, 255, 0.05);
            border-left: 4px solid #00d4ff;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .ok {
            color: #44ff44;
        }
        .erro {
            color: #ff4444;
        }
        .warn {
            color: #ffaa00;
        }
        form {
            background: #111;
            padding: 20px;
            border-radius: 4px;
            margin: 20px 0;
        }
        input, select, textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #00d4ff;
            border-radius: 4px;
            background: #1a1a1a;
            color: #fff;
            box-sizing: border-box;
        }
        button {
            background: #00d4ff;
            color: #000;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        button:hover {
            background: #00a8cc;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Teste Fluxo Completo de Login</h1>
        
        <div class="teste-section">
            <h2>Estado Atual do Sistema</h2>
            <p>Usuário logado: <?php echo isset($_SESSION['usuario_id']) ? '<span class="ok">✅ Sim</span>' : '<span class="erro">❌ Não</span>'; ?></p>
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <p>Nome: <?php echo $_SESSION['usuario_nome'] ?? 'N/A'; ?></p>
                <p>Email: <?php echo $_SESSION['usuario_email'] ?? 'N/A'; ?></p>
                <p><a href="logout.php" style="color: #00d4ff;">Fazer Logout</a></p>
            <?php endif; ?>
        </div>
        
        <div class="teste-section">
            <h2>Teste 1: Criar Nova Conta</h2>
            <form action="cadastro.php" method="POST">
                <input type="text" name="nome" placeholder="Nome Completo" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="text" name="nickname" placeholder="Nickname" required>
                <input type="password" name="senha" placeholder="Senha (ex: Teste123!)" required>
                <input type="password" name="confirmSenha" placeholder="Confirmar Senha" required>
                <select name="plataforma" required>
                    <option value="">Plataforma</option>
                    <option value="PC">PC</option>
                    <option value="PlayStation">PlayStation</option>
                    <option value="Xbox">Xbox</option>
                </select>
                <select name="rank" required>
                    <option value="">Rank</option>
                    <option value="Iniciante">Iniciante</option>
                    <option value="Casual">Casual</option>
                    <option value="Intermediario">Intermediário</option>
                </select>
                <textarea name="bio" placeholder="Sua bio (opcional)"></textarea>
                <button type="submit">Cadastrar e Testar</button>
            </form>
            <p class="warn">💡 Após cadastrar, você será redirecionado para o dashboard se o login for bem-sucedido</p>
        </div>
        
        <div class="teste-section">
            <h2>Teste 2: Fazer Login</h2>
            <form action="debug_login_post.php" method="POST">
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="senha" placeholder="Senha" required>
                <button type="submit">Fazer Login (Debug)</button>
            </form>
            <p class="warn">💡 Este formulário mostra os detalhes do processo de login</p>
        </div>
        
        <div class="teste-section">
            <h2>Informações do Banco de Dados</h2>
            <?php
            try {
                $result = $conexao->query("SELECT COUNT(*) as cnt FROM usuarios");
                $row = $result->fetch();
                echo "<p>Total de usuários: <span class='ok'>" . $row['cnt'] . "</span></p>";
                
                $result = $conexao->query("SELECT id, email, nome, nickname FROM usuarios LIMIT 5");
                $usuarios = $result->fetchAll();
                
                if (count($usuarios) > 0) {
                    echo "<h3>Últimos usuários cadastrados:</h3>";
                    echo "<table border='1' cellpadding='8' style='width: 100%;'>";
                    echo "<tr><th>ID</th><th>Email</th><th>Nome</th><th>Nickname</th></tr>";
                    foreach ($usuarios as $u) {
                        echo "<tr>";
                        echo "<td>" . $u['id'] . "</td>";
                        echo "<td>" . $u['email'] . "</td>";
                        echo "<td>" . $u['nome'] . "</td>";
                        echo "<td>" . $u['nickname'] . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                }
            } catch (Exception $e) {
                echo "<p class='erro'>Erro: " . $e->getMessage() . "</p>";
            }
            ?>
        </div>
        
        <div class="teste-section">
            <h2>Links Úteis</h2>
            <ul style="line-height: 2;">
                <li><a href="debug_login.php" style="color: #00d4ff;">Debug Completo</a> - Ver logs detalhados</li>
                <li><a href="index.html" style="color: #00d4ff;">Login Real</a> - Página de login oficial</li>
                <li><a href="cadastro.html" style="color: #00d4ff;">Cadastro Real</a> - Página de cadastro oficial</li>
                <li><a href="status.php" style="color: #00d4ff;">Status Sistema</a> - Verificar status</li>
            </ul>
        </div>
    </div>
</body>
</html>
