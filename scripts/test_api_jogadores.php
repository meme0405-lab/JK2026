<?php
require __DIR__ . '/../config.php';

try {
    $sql = "SELECT COUNT(*) AS total FROM usuarios";
    $stmt = $conexao->query($sql);
    $row = $stmt->fetch();
    echo "Usuarios no BD: " . ($row['total'] ?? 0) . PHP_EOL;

    $sql2 = "SELECT id, nome, nickname FROM usuarios ORDER BY id DESC LIMIT 5";
    $stmt2 = $conexao->query($sql2);
    $rows = $stmt2->fetchAll();
    foreach ($rows as $r) {
        echo "#{$r['id']} - {$r['nome']} (@{$r['nickname']})" . PHP_EOL;
    }

    exit(0);
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage() . PHP_EOL;
    exit(1);
}
