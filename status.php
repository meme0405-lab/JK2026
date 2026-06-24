<?php
// status.php
// Página de diagnóstico do sistema

session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status do Sistema - JK Game Match</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            background: #0d0d0d;
            color: #00ff00;
            padding: 20px;
            margin: 0;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: #1a1a1a;
            border: 2px solid #00ff00;
            border-radius: 8px;
            padding: 20px;
        }
        h1 {
            color: #00ff00;
            border-bottom: 2px solid #00ff00;
            padding-bottom: 10px;
        }
        h2 {
            color: #00d4ff;
            margin-top: 20px;
        }
        .status-ok {
            color: #44ff44;
        }
        .status-error {
            color: #ff4444;
        }
        .status-warning {
            color: #ffaa00;
        }
        .info-box {
            background: rgba(0, 255, 0, 0.05);
            border-left: 4px solid #00ff00;
            padding: 10px;
            margin: 10px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        th, td {
            border: 1px solid #00ff00;
            padding: 8px;
            text-align: left;
        }
        th {
            background: rgba(0, 255, 0, 0.1);
        }
        .link {
            color: #00d4ff;
            text-decoration: none;
        }
        .link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Status do Sistema - JK Game Match</h1>
        
        <h2>Informações da Sessão</h2>
        <table>
            <tr>
                <th>Parâmetro</th>
                <th>Valor</th>
            </tr>
            <tr>
                <td>Usuário Logado</td>
                <td><?php echo isset($_SESSION['usuario_id']) ? '<span class="status-ok">✅ Sim</span>' : '<span class="status-error">❌ Não</span>'; ?></td>
            </tr>
            <tr>
                <td>ID do Usuário</td>
                <td><?php echo $_SESSION['usuario_id'] ?? 'N/A'; ?></td>
            </tr>
            <tr>
                <td>Nome do Usuário</td>
                <td><?php echo $_SESSION['usuario_nome'] ?? 'N/A'; ?></td>
            </tr>
            <tr>
                <td>Email</td>
                <td><?php echo $_SESSION['usuario_email'] ?? 'N/A'; ?></td>
            </tr>
        </table>
        
        <h2>Verificação do PHP</h2>
        <table>
            <tr>
                <th>Componente</th>
                <th>Status</th>
            </tr>
            <tr>
                <td>PHP Version</td>
                <td><?php echo PHP_VERSION; ?></td>
            </tr>
            <tr>
                <td>PDO MySQL</td>
                <td><?php echo extension_loaded('pdo_mysql') ? '<span class="status-ok">✅ Ativo</span>' : '<span class="status-error">❌ Inativo</span>'; ?></td>
            </tr>
            <tr>
                <td>Sessions</td>
                <td><?php echo ini_get('session.auto_start') ? '<span class="status-ok">✅ Ativo</span>' : '<span class="status-ok">✅ Manual</span>'; ?></td>
            </tr>
        </table>
        
        <h2>Testes de Conectividade</h2>
        <div class="info-box">
            <a href="teste_banco.php" class="link">🔧 Testar Banco de Dados</a><br>
            <a href="db_debug.php" class="link">🔧 Debug do Banco</a><br>
            <a href="setup_banco.php" class="link">🔧 Setup do Banco</a>
        </div>
        
        <h2>Ações Disponíveis</h2>
        <div class="info-box">
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <a href="dashboard.html" class="link">📊 Ir para Dashboard</a><br>
                <a href="logout.php" class="link">🚪 Fazer Logout</a>
            <?php else: ?>
                <a href="index.html" class="link">🔐 Fazer Login</a><br>
                <a href="cadastro.html" class="link">📝 Criar Conta</a><br>
                <a href="criar_usuario.php" class="link">👤 Criar Usuário de Teste</a>
            <?php endif; ?>
        </div>
        
        <h2>Próximos Passos</h2>
        <div class="info-box">
            <strong>1. Setup Inicial:</strong><br>
            &nbsp;&nbsp;→ Acesse <a href="setup_banco.php" class="link">setup_banco.php</a> para criar o banco de dados<br>
            <br>
            <strong>2. Criar Usuário:</strong><br>
            &nbsp;&nbsp;→ Acesse <a href="criar_usuario.php" class="link">criar_usuario.php</a> ou <a href="cadastro.html" class="link">cadastro.html</a><br>
            <br>
            <strong>3. Fazer Login:</strong><br>
            &nbsp;&nbsp;→ Acesse <a href="index.html" class="link">index.html</a> com suas credenciais<br>
            <br>
            <strong>4. Explorar Plataforma:</strong><br>
            &nbsp;&nbsp;→ Após login, você será redirecionado para o dashboard
        </div>
    </div>
</body>
</html>
