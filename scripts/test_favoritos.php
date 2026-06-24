<?php
require __DIR__ . '/../config.php';

echo "=== TESTE API JOGOS ===" . PHP_EOL;

try {
    // Testar api/jogos.php
    $sql = "SELECT COUNT(*) AS total FROM jogos";
    $stmt = $conexao->query($sql);
    $row = $stmt->fetch();
    echo "Total de jogos no BD: " . ($row['total'] ?? 0) . PHP_EOL;

    $sql2 = "SELECT id, nome, genero FROM jogos LIMIT 5";
    $stmt2 = $conexao->query($sql2);
    $rows = $stmt2->fetchAll();
    foreach ($rows as $r) {
        echo "  - #{$r['id']} {$r['nome']} ({$r['genero']})" . PHP_EOL;
    }

    // Testar api/favoritos.php
    echo PHP_EOL . "Testando endpoints de favoritos..." . PHP_EOL;
    echo "  - GET /api/favoritos.php (requer login)" . PHP_EOL;
    echo "  - POST /api/add-favorito.php (requer login + jogo_id)" . PHP_EOL;
    echo "  - POST /api/remove-favorito.php (requer login + jogo_id)" . PHP_EOL;

    exit(0);
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage() . PHP_EOL;
    exit(1);
}
