<?php
include __DIR__ . '/../config.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $sql = "SELECT id, nome, genero, plataforma, descricao FROM jogos ORDER BY nome ASC";
    $stmt = $conexao->query($sql);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Mapear dados para formato esperado pelo JS
    $result = array_map(function($r) {
        return [
            'id' => (int) $r['id'],
            'nome' => $r['nome'],
            'genero' => $r['genero'],
            'plataforma' => $r['plataforma'],
            'descricao' => $r['descricao'],
            'icon' => substr($r['nome'], 0, 1) // Usar primeira letra como fallback
        ];
    }, $rows);

    echo json_encode(['status' => 'ok', 'dados' => $result], JSON_UNESCAPED_UNICODE);
    exit();
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'mensagem' => 'Erro ao buscar jogos']);
    exit();
}
