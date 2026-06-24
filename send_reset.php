<?php

session_start();
require_once 'config.php'; // fornece $conexao PDO

function geraToken($len = 48) {
    return bin2hex(random_bytes($len/2));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: recuperar_senha.php'); exit;
}

$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
if (!$email) {
    echo "Email inválido"; exit;
}

// Procurar usuário
try {
    $stmt = $conexao->prepare('SELECT id,email FROM usuarios WHERE email = :email LIMIT 1');
    $stmt->execute([':email'=>$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo 'Erro no banco'; exit;
}

if (!$user) {
    echo "Se o e-mail existir, um link será gerado (modo teste)."; exit;
}

$token = geraToken(48);
$expires = date('Y-m-d H:i:s', time() + 3600); // 1h

// Inserir token na tabela password_resets (criar se necessário)
try {
    $stmt = $conexao->prepare('INSERT INTO password_resets (user_id, token, expires_at) VALUES (:uid,:token,:exp)');
    $stmt->execute([':uid'=>$user['id'],':token'=>$token,':exp'=>$expires]);
} catch (Exception $e) {
    echo 'Erro ao gravar token: '.$e->getMessage(); exit;
}

$resetLink = sprintf('%s/reset_senha.php?token=%s', rtrim(dirname((isset($_SERVER['HTTPS'])&&$_SERVER['HTTPS']?'https://':'http://').$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']), '/'), $token);

// No modo sem SMTP, exibimos o link para testes
?>
<!doctype html>
<html lang="pt-BR">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Link de Reset</title><link rel="stylesheet" href="css/style.css"></head>
<body>
  <main class="container">
    <h1>Link de Recuperação (modo de teste)</h1>
    <p>Clique no link abaixo para redefinir sua senha (válido por 1 hora):</p>
    <p><a href="<?= htmlspecialchars($resetLink) ?>"><?php echo htmlspecialchars($resetLink); ?></a></p>
    <p><a href="index.html">Voltar ao login</a></p>
  </main>
</body>
</html>
