<?php
include __DIR__ . '/../config.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $sql = "SELECT u.id, u.nome, u.nickname, u.foto_perfil AS foto, u.rank, u.plataforma, u.nivel, u.bio, u.ultimo_acesso,
            GROUP_CONCAT(j.nome SEPARATOR '||') AS jogos
            FROM usuarios u
            LEFT JOIN jogos_favoritos jf ON u.id = jf.usuario_id
            LEFT JOIN jogos j ON jf.jogo_id = j.id
            GROUP BY u.id
            ORDER BY u.id DESC
            LIMIT 200";

    $stmt = $conexao->query($sql);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $result = array_map(function($r) {
        $r['foto'] = $r['foto'] ?: '';
        $r['jogos'] = [];
        if (!empty($r['jogos'])) {
            $r['jogos'] = array_filter(array_map('trim', explode('||', $r['jogos'])));
        }
        unset($r['ultimo_acesso']);
        return $r;
    }, $rows);

    echo json_encode(['status' => 'ok', 'dados' => $result], JSON_UNESCAPED_UNICODE);
    exit();
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'mensagem' => 'Erro ao buscar jogadores']);
    exit();
}
