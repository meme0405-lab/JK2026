<?php
session_start();
require_once 'config.php';

// Validar token
$token = filter_input(INPUT_GET, 'token', FILTER_SANITIZE_STRING);
if (!$token) {
    echo 'Token inválido'; exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new = $_POST['password'] ?? '';
    $confirm = $_POST['password_confirm'] ?? '';
    if (empty($new) || $new !== $confirm) { $error = 'Senha inválida ou não conferem.'; }
    else {
        try {
          $stmt = $conexao->prepare('SELECT user_id,expires_at FROM password_resets WHERE token = :token LIMIT 1');
          $stmt->execute([':token'=>$token]);
          $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) { $error = 'Token não encontrado.'; }
            elseif (strtotime($row['expires_at']) < time()) { $error = 'Token expirado.'; }
            else {
                $hash = password_hash($new, PASSWORD_DEFAULT);
            $uStmt = $conexao->prepare('UPDATE usuarios SET senha = :senha WHERE id = :id');
            $uStmt->execute([':senha'=>$hash, ':id'=>$row['user_id']]);
                // invalidar token
            $d = $conexao->prepare('DELETE FROM password_resets WHERE token = :token');
            $d->execute([':token'=>$token]);
                echo 'Senha alterada com sucesso. <a href="index.html">Entrar</a>'; exit;
            }
        } catch (Exception $e) { $error = 'Erro no servidor: '.$e->getMessage(); }
    }
}

?>
<!doctype html>
<html lang="pt-BR">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Redefinir Senha</title><link rel="stylesheet" href="css/style.css"></head>
<body>
  <main class="container">
    <h1>Redefinir Senha</h1>
    <?php if (!empty($error)): ?><div class="error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <form method="post">
      <label for="password">Nova senha</label>
      <input type="password" name="password" id="password" required>
      <label for="password_confirm">Confirme a senha</label>
      <input type="password" name="password_confirm" id="password_confirm" required>
      <button type="submit">Alterar minha senha</button>
    </form>
  </main>
</body>
</html>
