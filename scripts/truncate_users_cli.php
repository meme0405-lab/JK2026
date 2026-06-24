<?php
// Script CLI para truncar usuários e tabelas relacionadas.
// Uso: php truncate_users_cli.php --yes

if (php_sapi_name() !== 'cli') {
    echo "Este script deve ser executado via linha de comando (CLI).\n";
    exit(1);
}

$args = array_slice($argv, 1);
$confirmed = in_array('--yes', $args, true) || in_array('-y', $args, true);

require __DIR__ . '/../config.php';

if (defined('APP_ENV') && APP_ENV === 'production') {
    echo "Operação bloqueada: APP_ENV=production\n";
    exit(1);
}

if (! $confirmed) {
    echo "AVISO: Isto apagará permanentemente todos os usuários e dados relacionados.\n";
    echo "Para confirmar, execute: php truncate_users_cli.php --yes\n";
    exit(1);
}

try {
    echo "Iniciando truncagem das tabelas...\n";
    // Não usar transações porque TRUNCATE pode causar commit implícito
    $conexao->exec('SET FOREIGN_KEY_CHECKS=0');

    $tables = ['mensagens', 'chamadas_video', 'matches', 'usuarios'];
    foreach ($tables as $t) {
        // Verificar existência da tabela via information_schema
        $stmtCheck = $conexao->prepare("SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ? LIMIT 1");
        $stmtCheck->execute([$t]);
        $exists = $stmtCheck->fetchColumn();
        if (! $exists) {
            echo "Tabela não encontrada, pulando: $t\n";
            continue;
        }

        echo "Truncando: $t\n";
        try {
            $conexao->exec("TRUNCATE TABLE `$t`");
        } catch (PDOException $inner) {
            echo "TRUNCATE falhou para $t, tentando DELETE...\n";
            $conexao->exec("DELETE FROM `$t`");
            $conexao->exec("ALTER TABLE `$t` AUTO_INCREMENT = 1");
        }
    }

    $conexao->exec('SET FOREIGN_KEY_CHECKS=1');

    fazer_log('ADMIN', 'Execução CLI: Limpeza completa das tabelas de usuários e relacionadas');

    echo "Conclusão: tabelas truncadas com sucesso.\n";
    exit(0);
} catch (PDOException $e) {
    if ($conexao->inTransaction()) $conexao->rollBack();
    fazer_log('ADMIN_ERRO', 'Execução CLI: Erro ao truncar tabelas: ' . $e->getMessage());
    fwrite(STDERR, "Erro: " . $e->getMessage() . "\n");
    exit(2);
}
