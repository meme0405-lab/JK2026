<?php
// debug_login.php
// Script para debugar o fluxo de login

session_start();
include 'config.php';

header('Content-Type: text/html; charset=utf-8');

echo "<h2>🔍 Debug Completo do Login</h2>";

// Teste 1: Verificar se pode conectar no banco
echo "<h3>Teste 1: Conexão com Banco</h3>";
try {
    $resultado = $conexao->query("SELECT COUNT(*) as cnt FROM usuarios");
    $row = $resultado->fetch();
    echo "<p>✅ Banco conectado. Usuários no banco: " . $row['cnt'] . "</p>";
    
    // Listar todos os usuários
    $resultado = $conexao->query("SELECT id, email, nome, senha FROM usuarios");
    $usuarios = $resultado->fetchAll();
    
    if (count($usuarios) > 0) {
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>ID</th><th>Email</th><th>Nome</th><th>Senha (primeiros 20 char)</th></tr>";
        foreach ($usuarios as $u) {
            echo "<tr>";
            echo "<td>" . $u['id'] . "</td>";
            echo "<td>" . $u['email'] . "</td>";
            echo "<td>" . $u['nome'] . "</td>";
            echo "<td>" . substr($u['senha'], 0, 20) . "...</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>⚠️ Nenhum usuário no banco. <a href='criar_usuario.php'>Criar um</a></p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Erro: " . $e->getMessage() . "</p>";
}

// Teste 2: Testar password_hash e password_verify
echo "<h3>Teste 2: Criptografia de Senha</h3>";
$testSenha = "teste123";
$testHash = password_hash($testSenha, PASSWORD_BCRYPT);
$testVerify1 = password_verify($testSenha, $testHash);
$testVerify2 = password_verify("senhaerrada", $testHash);

echo "<p>Senha teste: <code>$testSenha</code></p>";
echo "<p>Hash gerado: <code>" . substr($testHash, 0, 30) . "...</code></p>";
echo "<p>password_verify('teste123'): " . ($testVerify1 ? "✅ Correto" : "❌ Errado") . "</p>";
echo "<p>password_verify('senhaerrada'): " . ($testVerify2 ? "❌ Não deveria validar" : "✅ Correto (rejeitou)") . "</p>";

// Teste 3: Simular login com um usuário real
echo "<h3>Teste 3: Simular Login</h3>";
if (count($usuarios) > 0) {
    $usuarioTeste = $usuarios[0];
    echo "<p>Testando login com email: <code>" . $usuarioTeste['email'] . "</code></p>";
    
    // Tentar buscar pela query de login
    $stmt = $conexao->prepare('SELECT id, nome, nickname, email, senha, rank, plataforma FROM usuarios WHERE email = ?');
    $stmt->execute([$usuarioTeste['email']]);
    $usuarioEncontrado = $stmt->fetch();
    
    if ($usuarioEncontrado) {
        echo "<p>✅ Usuário encontrado na query: " . $usuarioEncontrado['nome'] . "</p>";
        
        // A senha armazenada está em formato hash?
        $senhaArmazenada = $usuarioEncontrado['senha'];
        if (strpos($senhaArmazenada, '$2y$') === 0 || strpos($senhaArmazenada, '$2a$') === 0) {
            echo "<p>✅ Senha está em formato hash (bcrypt)</p>";
        } else {
            echo "<p>❌ Senha NÃO está em formato hash. Está em texto plano!</p>";
            echo "<p>Valor: <code>" . $senhaArmazenada . "</code></p>";
        }
    } else {
        echo "<p>❌ Usuário não foi encontrado pela query</p>";
    }
}

// Teste 4: Verificar sessão
echo "<h3>Teste 4: Estado da Sessão</h3>";
echo "<p>Sessão ID: " . session_id() . "</p>";
echo "<p>Usuário logado: " . (isset($_SESSION['usuario_id']) ? "✅ Sim (ID: " . $_SESSION['usuario_id'] . ")" : "❌ Não") . "</p>";
echo "<p>Nome na sessão: " . (isset($_SESSION['usuario_nome']) ? $_SESSION['usuario_nome'] : "N/A") . "</p>";

// Teste 5: Testar formulário POST
echo "<h3>Teste 5: Enviar Dados POST</h3>";
echo "<form method='POST' action='debug_login_post.php'>";
echo "<label>Email: <input type='email' name='email' required></label><br>";
echo "<label>Senha: <input type='password' name='senha' required></label><br>";
echo "<button type='submit'>Testar Login</button>";
echo "</form>";

echo "<hr>";
echo "<h3>Links úteis:</h3>";
echo "<ul>";
echo "<li><a href='criar_usuario.php'>Criar novo usuário</a></li>";
echo "<li><a href='index.html'>Ir para login real</a></li>";
echo "<li><a href='logout.php'>Fazer logout</a></li>";
echo "<li><a href='status.php'>Ver status do sistema</a></li>";
echo "</ul>";
?>
