<?php
include __DIR__ . '/../config.php';

verificar_autenticacao_json();

header('Content-Type: application/json; charset=utf-8');

try {
    $usuario_id = (int) $_SESSION['usuario_id'];

    $sql = "SELECT j.id, j.nome, j.genero, j.plataforma, j.descricao 
            FROM jogos_favoritos jf
            JOIN jogos j ON jf.jogo_id = j.id
            WHERE jf.usuario_id = ?
            ORDER BY jf.data_adicao DESC";

    $stmt = $conexao->prepare($sql);
    $stmt->execute([$usuario_id]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['status' => 'ok', 'dados' => $rows], JSON_UNESCAPED_UNICODE);
    exit();
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'mensagem' => 'Erro ao buscar favoritos']);
    exit();
}
