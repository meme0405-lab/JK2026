<?php
include __DIR__ . '/../config.php';

verificar_autenticacao_json();

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'mensagem' => 'Método inválido']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$jogo_id = isset($data['jogo_id']) ? (int) $data['jogo_id'] : 0;
$usuario_id = (int) $_SESSION['usuario_id'];

if ($jogo_id <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'mensagem' => 'ID do jogo inválido']);
    exit();
}

try {
    $delete = $conexao->prepare('DELETE FROM jogos_favoritos WHERE usuario_id = ? AND jogo_id = ?');
    $result = $delete->execute([$usuario_id, $jogo_id]);

    if ($delete->rowCount() > 0) {
        fazer_log('FAVORITO_REMOVIDO', "Jogo ID: $jogo_id", $usuario_id);
        echo json_encode(['status' => 'ok', 'mensagem' => 'Jogo removido dos favoritos']);
    } else {
        echo json_encode(['status' => 'ok', 'mensagem' => 'Jogo não estava nos favoritos']);
    }
    exit();
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'mensagem' => 'Erro ao remover favorito']);
    exit();
}
