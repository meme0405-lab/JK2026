<?php
require __DIR__ . '/../config.php';

$users = [
    ['nome' => 'Jogador Dois', 'email' => 'player2@example.com', 'senha' => 'password', 'nickname' => 'player2', 'foto' => '', 'bio' => 'Gosta de FPS', 'rank' => 'Platina', 'plataforma' => 'PC', 'nivel' => 20],
    ['nome' => 'Jogador Tres', 'email' => 'player3@example.com', 'senha' => 'password', 'nickname' => 'player3', 'foto' => '', 'bio' => 'Prefere battle royale', 'rank' => 'Ouro', 'plataforma' => 'Console', 'nivel' => 18],
    ['nome' => 'Jogador Quatro', 'email' => 'player4@example.com', 'senha' => 'password', 'nickname' => 'player4', 'foto' => '', 'bio' => 'Estratégia e MOBA', 'rank' => 'Prata', 'plataforma' => 'PC', 'nivel' => 12],
    ['nome' => 'Jessica Costa', 'email' => 'jessica@example.com', 'senha' => 'password', 'nickname' => 'Jess', 'foto' => '', 'bio' => 'Competitiva e dedicada', 'rank' => 'Ouro', 'plataforma' => 'PC', 'nivel' => 16],
    ['nome' => 'Pedro Alves', 'email' => 'pedro@example.com', 'senha' => 'password', 'nickname' => 'PedroA', 'foto' => '', 'bio' => 'Novo no cenário gamer', 'rank' => 'Bronze', 'plataforma' => 'Console', 'nivel' => 8]
];

try {
    foreach ($users as $u) {
        $check = $conexao->prepare('SELECT id FROM usuarios WHERE email = ? OR nickname = ? LIMIT 1');
        $check->execute([$u['email'], $u['nickname']]);
        if ($check->fetch()) {
            echo "Usuário {$u['nickname']} já existe, pulando.\n";
            continue;
        }

        $stmt = $conexao->prepare('INSERT INTO usuarios (nome, email, senha, nickname, foto_perfil, bio, rank, plataforma, nivel, data_criacao) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())');
        $stmt->execute([
            $u['nome'],
            $u['email'],
            $u['senha'],
            $u['nickname'],
            $u['foto'],
            $u['bio'],
            $u['rank'],
            $u['plataforma'],
            $u['nivel']
        ]);
        echo "Inserido usuário: {$u['nickname']}\n";
    }

    // Inserir jogos favoritos conforme nosso mapeamento
    $mappings = [
        'player2' => ['Counter-Strike 2', 'Valorant'],
        'player3' => ['Apex Legends', 'Fortnite'],
        'player4' => ['League of Legends', 'Dota 2'],
        'Jess' => ['Valorant', 'Counter-Strike 2'],
        'PedroA' => ['Fortnite', 'Minecraft']
    ];

    foreach ($mappings as $nick => $games) {
        $stmtU = $conexao->prepare('SELECT id FROM usuarios WHERE nickname = ? LIMIT 1');
        $stmtU->execute([$nick]);
        $uid = $stmtU->fetchColumn();
        if (!$uid) {
            echo "Usuário $nick não encontrado para associar jogos.\n";
            continue;
        }
        foreach ($games as $gname) {
            $stmtJ = $conexao->prepare('SELECT id FROM jogos WHERE nome = ? LIMIT 1');
            $stmtJ->execute([$gname]);
            $jid = $stmtJ->fetchColumn();
            if (!$jid) {
                echo "Jogo $gname não encontrado, pulando.\n";
                continue;
            }
            // Verifica duplicado
            $chk = $conexao->prepare('SELECT id FROM jogos_favoritos WHERE usuario_id = ? AND jogo_id = ? LIMIT 1');
            $chk->execute([$uid, $jid]);
            if ($chk->fetch()) {
                continue;
            }
            $ins = $conexao->prepare('INSERT INTO jogos_favoritos (usuario_id, jogo_id) VALUES (?, ?)');
            $ins->execute([$uid, $jid]);
            echo "Associado $gname a $nick\n";
        }
    }

    echo "Concluído.\n";
    exit(0);
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage() . PHP_EOL;
    exit(1);
}
