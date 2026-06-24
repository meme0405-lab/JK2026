<?php
header('Content-Type: application/javascript; charset=UTF-8');
if (session_status() === PHP_SESSION_NONE) session_start();
// Se não houver sessão de usuário, emitir script vazio
if (empty($_SESSION['usuario_id'])) {
    echo "window.usuarioAtual = null;\n";
    exit();
}

$user = [
    'id' => (int)($_SESSION['usuario_id'] ?? 0),
    'nome' => $_SESSION['usuario_nome'] ?? '',
    'nickname' => $_SESSION['usuario_nickname'] ?? '',
    'email' => $_SESSION['usuario_email'] ?? '',
    'foto' => $_SESSION['usuario_foto'] ?? '',
    'bio' => $_SESSION['usuario_bio'] ?? '',
    'rank' => $_SESSION['usuario_rank'] ?? '',
    'plataforma' => $_SESSION['usuario_plataforma'] ?? '',
    'nivel' => (int)($_SESSION['usuario_nivel'] ?? 1),
    'matches' => (int)($_SESSION['usuario_matches'] ?? 0),
    'jogos' => (int)($_SESSION['usuario_jogos'] ?? 0),
    'online' => true,
    'data_criacao' => $_SESSION['usuario_data_criacao'] ?? ''
];

echo "window.usuarioAtual = ".json_encode($user, JSON_UNESCAPED_UNICODE).";\n";
?>
