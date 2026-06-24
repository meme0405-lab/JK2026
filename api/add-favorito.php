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
    // Verificar se jogo existe
    $check = $conexao->prepare('SELECT id FROM jogos WHERE id = ? LIMIT 1');
    $check->execute([$jogo_id]);
    if (!$check->fetch()) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'mensagem' => 'Jogo não encontrado']);
        exit();
    }

    // Verificar se já está nos favoritos
    $checkFav = $conexao->prepare('SELECT id FROM jogos_favoritos WHERE usuario_id = ? AND jogo_id = ? LIMIT 1');
    $checkFav->execute([$usuario_id, $jogo_id]);
    if ($checkFav->fetch()) {
        echo json_encode(['status' => 'ok', 'mensagem' => 'Jogo já estava nos favoritos']);
        exit();
    }

    // Inserir favorito
    $insert = $conexao->prepare('INSERT INTO jogos_favoritos (usuario_id, jogo_id) VALUES (?, ?)');
    $insert->execute([$usuario_id, $jogo_id]);

    fazer_log('FAVORITO_ADICIONADO', "Jogo ID: $jogo_id", $usuario_id);

    echo json_encode(['status' => 'ok', 'mensagem' => 'Jogo adicionado aos favoritos']);
    exit();
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'mensagem' => 'Erro ao adicionar favorito']);
    exit();
}
