<?php
session_start();
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Recuperar Senha</title>
  <link rel="stylesheet" href="css/style.css">
  </head>
<body>
  <main class="container">
    <h1>Recuperar Senha</h1>
    <p>Informe o e-mail cadastrado. Um link de reset será exibido para testes.</p>
    <form action="send_reset.php" method="post">
      <label for="email">E-mail</label>
      <input type="email" name="email" id="email" required>
      <button type="submit">Gerar link de reset</button>
    </form>
    <p><a href="index.html">Voltar ao login</a></p>
  </main>
</body>
</html>
