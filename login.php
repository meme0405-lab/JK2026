<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.html');
    exit();
}

include 'config.php';

$email = trim(sanitizar($_POST['email'] ?? ''));
$senha = $_POST['senha'] ?? '';

if ($email === '' || $senha === '') {
    header('Location: index.html?status=error&message=' . rawurlencode('Email e senha são obrigatórios.'));
    exit();
}

if (!validar_email($email)) {
    header('Location: index.html?status=error&message=' . rawurlencode('Email inválido.'));
    exit();
}

try {
    $stmt = $conexao->prepare('SELECT id, nome, nickname, email, senha, rank, plataforma, foto_perfil, bio, nivel, data_criacao FROM usuarios WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    if (!$usuario) {
        header('Location: index.html?status=error&message=' . rawurlencode('Email ou senha incorretos.'));
        exit();
    }

    $senhaArmazenada = $usuario['senha'] ?? '';
    $senhaValida = false;

    if (is_string($senhaArmazenada) && $senhaArmazenada !== '') {
        if (password_verify($senha, $senhaArmazenada)) {
            $senhaValida = true;
        } elseif ($senha === $senhaArmazenada) {
            $senhaValida = true;
        }
    }

    if (!$senhaValida) {
        header('Location: index.html?status=error&message=' . rawurlencode('Email ou senha incorretos.'));
        exit();
    }

    $_SESSION['usuario_id'] = (int) $usuario['id'];
    $_SESSION['usuario_nome'] = $usuario['nome'];
    $_SESSION['usuario_nickname'] = $usuario['nickname'];
    $_SESSION['usuario_email'] = $usuario['email'];
    $_SESSION['usuario_rank'] = $usuario['rank'];
    $_SESSION['usuario_plataforma'] = $usuario['plataforma'];
    $_SESSION['usuario_foto'] = $usuario['foto_perfil'] ?? '';
    $_SESSION['usuario_bio'] = $usuario['bio'] ?? '';
    $_SESSION['usuario_nivel'] = $usuario['nivel'] ?? 1;
    $_SESSION['usuario_data_criacao'] = $usuario['data_criacao'] ?? '';

    $stmtUpdate = $conexao->prepare('UPDATE usuarios SET ultimo_acesso = NOW() WHERE id = ?');
    $stmtUpdate->execute([$usuario['id']]);

    header('Location: dashboard.html');
    exit();
} catch (PDOException $e) {
    fazer_log('ERRO_LOGIN', $e->getMessage());
    header('Location: index.html?status=error&message=' . rawurlencode('Erro ao processar login. Tente novamente.'));
    exit();
}
?>
