<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cadastro.html');
    exit();
}

include 'config.php';

$nome = trim(sanitizar($_POST['nome'] ?? ''));
$email = trim(sanitizar($_POST['email'] ?? ''));
$nickname = trim(sanitizar($_POST['nickname'] ?? ''));
$senha = $_POST['senha'] ?? '';
$confirmSenha = $_POST['confirmSenha'] ?? '';
$plataforma = trim(sanitizar($_POST['plataforma'] ?? ''));
$rank = trim(sanitizar($_POST['rank'] ?? ''));
$bio = trim(sanitizar($_POST['bio'] ?? ''));

$fotoPerfil = '';
$erros = [];

if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
        if ($_FILES['foto_perfil']['size'] > MAX_UPLOAD_SIZE) {
            $erros[] = 'O avatar deve ter no máximo 5MB.';
        } else {
            $ext = strtolower(pathinfo($_FILES['foto_perfil']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ALLOWED_EXTENSIONS, true)) {
                $erros[] = 'Formato de avatar inválido. Use JPG, PNG ou GIF.';
            } else {
                $uploadDir = __DIR__ . '/' . rtrim(UPLOAD_DIR, '/') . '/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $novoNome = uniqid('avatar_', true) . '.' . $ext;
                $caminhoDestino = $uploadDir . $novoNome;
                if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $caminhoDestino)) {
                    $fotoPerfil = rtrim(UPLOAD_DIR, '/') . '/' . $novoNome;
                } else {
                    $erros[] = 'Erro ao enviar o avatar. Tente novamente.';
                }
            }
        }
    } else {
        $erros[] = 'Erro ao enviar o avatar. Tente novamente.';
    }
}

if ($nome === '') {
    $erros[] = 'Nome é obrigatório.';
}
if ($email === '' || !validar_email($email)) {
    $erros[] = 'Informe um email válido.';
}
if ($nickname === '' || strlen($nickname) < 3) {
    $erros[] = 'O nickname deve ter no mínimo 3 caracteres.';
}
if ($senha === '' || strlen($senha) < 6) {
    $erros[] = 'A senha deve ter no mínimo 6 caracteres.';
}
if ($senha !== $confirmSenha) {
    $erros[] = 'As senhas não coincidem.';
}
if ($plataforma === '' || $rank === '') {
    $erros[] = 'Selecione plataforma e rank.';
}

if (!empty($erros)) {
    $mensagem = implode(' ', $erros);
    header('Location: cadastro.html?status=error&message=' . rawurlencode($mensagem));
    exit();
}

try {
    $stmtExiste = $conexao->prepare('SELECT id FROM usuarios WHERE email = ? OR nickname = ? LIMIT 1');
    $stmtExiste->execute([$email, $nickname]);
    if ($stmtExiste->fetch()) {
        header('Location: cadastro.html?status=error&message=' . rawurlencode('Email ou nickname já estão em uso.'));
        exit();
    }

    $senhaHash = password_hash($senha, PASSWORD_BCRYPT);
    $stmt = $conexao->prepare('INSERT INTO usuarios (nome, email, senha, nickname, foto_perfil, rank, plataforma, bio) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$nome, $email, $senhaHash, $nickname, $fotoPerfil, $rank, $plataforma, $bio]);

    $usuarioId = (int) $conexao->lastInsertId();

    $_SESSION['usuario_id'] = $usuarioId;
    $_SESSION['usuario_nome'] = $nome;
    $_SESSION['usuario_nickname'] = $nickname;
    $_SESSION['usuario_email'] = $email;
    $_SESSION['usuario_rank'] = $rank;
    $_SESSION['usuario_plataforma'] = $plataforma;
    $_SESSION['usuario_bio'] = $bio;
    $_SESSION['usuario_foto'] = $fotoPerfil;
    $_SESSION['usuario_nivel'] = 1;
    $_SESSION['usuario_matches'] = 0;
    $_SESSION['usuario_jogos'] = 0;
    $_SESSION['usuario_data_criacao'] = date('Y-m-d H:i:s');

    fazer_log('CADASTRO', 'Novo usuário criado: ' . $email, $usuarioId);

    header('Location: perfil.php');
    exit();
} catch (PDOException $e) {
    fazer_log('ERRO_CADASTRO', $e->getMessage());
    header('Location: cadastro.html?status=error&message=' . rawurlencode('Erro ao criar conta. Tente novamente.'));
    exit();
}
?>
