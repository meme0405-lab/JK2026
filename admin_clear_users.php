<?php
/**
 * Admin: Limpar usuários e dados relacionados (APENAS DEVELOPMENT)
 * Uso: acessar este arquivo via navegador e confirmar a operação.
 */
include 'config.php';

// Proteção: permitir apenas em ambiente de desenvolvimento
if (defined('APP_ENV') && APP_ENV === 'production') {
    http_response_code(403);
    echo 'Operação não permitida em ambiente de produção.';
    exit();
}

// Gerar token simples para evitar CSRF básico
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (empty($_SESSION['admin_clear_token'])) {
    $_SESSION['admin_clear_token'] = bin2hex(random_bytes(16));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    if (!hash_equals($_SESSION['admin_clear_token'], $token)) {
        http_response_code(400);
        echo 'Token inválido.';
        exit();
    }

    try {
        // Desabilitar checagem de FK para truncar em sequência
        $conexao->beginTransaction();
        $conexao->exec('SET FOREIGN_KEY_CHECKS=0');

        // Truncar tabelas conhecidas que referenciam `usuarios`
        $tables = ['mensagens', 'chamadas_video', 'matches', 'usuarios'];
        foreach ($tables as $t) {
            // Usar TRUNCATE se existir
            try {
                $conexao->exec("TRUNCATE TABLE `$t`");
            } catch (PDOException $inner) {
                // Se TRUNCATE falhar (por permissões), tentar DELETE
                $conexao->exec("DELETE FROM `$t`");
                $conexao->exec("ALTER TABLE `$t` AUTO_INCREMENT = 1");
            }
        }

        $conexao->exec('SET FOREIGN_KEY_CHECKS=1');
        $conexao->commit();

        fazer_log('ADMIN', 'Limpeza completa das tabelas de usuários e relacionadas');
        echo 'Operação concluída: todas as tabelas selecionadas foram truncadas.';
        exit();
    } catch (PDOException $e) {
        if ($conexao->inTransaction()) $conexao->rollBack();
        fazer_log('ADMIN_ERRO', 'Erro ao truncar tabelas: ' . $e->getMessage());
        http_response_code(500);
        echo 'Erro ao executar operação: ' . htmlspecialchars($e->getMessage());
        exit();
    }
}

// Página de confirmação
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Admin - Limpar usuários</title>
    <style>body{font-family:Arial,Helvetica,sans-serif;background:#f5f5f5;padding:30px;} .box{background:#fff;padding:20px;border-radius:8px;max-width:720px;margin:0 auto;box-shadow:0 6px 18px rgba(0,0,0,0.06);} button{background:#ff006e;color:#fff;border:none;padding:10px 14px;border-radius:6px;cursor:pointer;} .warn{color:#aa0000;font-weight:bold;}</style>
</head>
<body>
<div class="box">
    <h2>Admin — Limpar usuários e dados relacionados</h2>
    <p class="warn">Atenção: esta operação apagará permanentemente todos os usuários listados nas tabelas e dados relacionados (mensagens, chamadas, matches).</p>
    <p>Este script só funciona em ambiente de desenvolvimento. Se você realmente deseja prosseguir, clique em "Confirmar".</p>

    <form method="post">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($_SESSION['admin_clear_token']); ?>">
        <button type="submit" onclick="return confirm('Confirmar exclusão de TODOS os usuários e dados relacionados? Esta ação é irreversível.')">Confirmar e Limpar</button>
    </form>
    <p style="margin-top:12px;"><a href="index.php">Voltar ao site</a></p>
</div>
</body>
</html>
