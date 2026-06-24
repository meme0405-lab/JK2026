<?php
// debug_login_post.php
// Processa login de debug

session_start();
include 'config.php';

header('Content-Type: text/html; charset=utf-8');

$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';

echo "<h2>🔍 Teste de Login POST</h2>";

echo "<p>Email recebido: <code>$email</code></p>";
echo "<p>Senha recebida: <code>" . str_repeat('*', strlen($senha)) . "</code></p>";

if (empty($email) || empty($senha)) {
    echo "<p>❌ Email ou senha vazios</p>";
    echo "<a href='debug_login.php'>Voltar</a>";
    exit();
}

// Buscar usuário
echo "<h3>Etapa 1: Buscando usuário</h3>";
$stmt = $conexao->prepare('SELECT id, nome, nickname, email, senha, rank, plataforma FROM usuarios WHERE email = ?');
$stmt->execute([$email]);
$usuario = $stmt->fetch();

if (!$usuario) {
    echo "<p>❌ Usuário não encontrado com email: $email</p>";
    echo "<a href='debug_login.php'>Voltar</a>";
    exit();
}

echo "<p>✅ Usuário encontrado:</p>";
echo "<ul>";
echo "<li>ID: " . $usuario['id'] . "</li>";
echo "<li>Nome: " . $usuario['nome'] . "</li>";
echo "<li>Email: " . $usuario['email'] . "</li>";
echo "<li>Nickname: " . $usuario['nickname'] . "</li>";
echo "<li>Senha armazenada (primeiros 30 chars): <code>" . substr($usuario['senha'], 0, 30) . "...</code></li>";
echo "</ul>";

// Validar senha
echo "<h3>Etapa 2: Validando Senha</h3>";

$senhaArmazenada = $usuario['senha'];

// Verificar se é hash bcrypt
if (strpos($senhaArmazenada, '$2y$') === 0 || strpos($senhaArmazenada, '$2a$') === 0) {
    echo "<p>✅ Senha está em formato hash bcrypt</p>";
    $senhaValida = password_verify($senha, $senhaArmazenada);
    echo "<p>password_verify resultado: " . ($senhaValida ? "✅ VÁLIDA" : "❌ INVÁLIDA") . "</p>";
} else {
    echo "<p>⚠️ Senha NÃO está em hash. Comparação de texto plano:</p>";
    $senhaValida = ($senha === $senhaArmazenada);
    echo "<p>Comparação: " . ($senhaValida ? "✅ VÁLIDA" : "❌ INVÁLIDA") . "</p>";
    echo "<p>Senha armazenada: <code>$senhaArmazenada</code></p>";
    echo "<p>Senha enviada: <code>$senha</code></p>";
}

if (!$senhaValida) {
    echo "<p>❌ Senha está incorreta</p>";
    echo "<a href='debug_login.php'>Voltar</a>";
    exit();
}

// Criar sessão
echo "<h3>Etapa 3: Criando Sessão</h3>";

$_SESSION['usuario_id'] = $usuario['id'];
$_SESSION['usuario_nome'] = $usuario['nome'];
$_SESSION['usuario_nickname'] = $usuario['nickname'];
$_SESSION['usuario_email'] = $usuario['email'];
$_SESSION['usuario_rank'] = $usuario['rank'];
$_SESSION['usuario_plataforma'] = $usuario['plataforma'];

echo "<p>✅ Sessão criada:</p>";
echo "<ul>";
echo "<li>Session ID: " . session_id() . "</li>";
echo "<li>usuario_id: " . $_SESSION['usuario_id'] . "</li>";
echo "<li>usuario_nome: " . $_SESSION['usuario_nome'] . "</li>";
echo "<li>usuario_email: " . $_SESSION['usuario_email'] . "</li>";
echo "</ul>";

// Atualizar último acesso
echo "<h3>Etapa 4: Atualizando Banco</h3>";
$stmtUpdate = $conexao->prepare('UPDATE usuarios SET ultimo_acesso = NOW() WHERE id = ?');
$stmtUpdate->execute([$usuario['id']]);
echo "<p>✅ Último acesso atualizado</p>";

echo "<h3>✅ Login Bem-Sucedido!</h3>";
echo "<p><a href='dashboard.html'>Ir para Dashboard</a></p>";
echo "<p><a href='status.php'>Ver Status</a></p>";
echo "<p><a href='debug_login.php'>Voltar para Debug</a></p>";
?>
