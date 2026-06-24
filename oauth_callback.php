<?php
// ============================================
// JK Game Match - Callback OAuth
// ============================================

include 'config.php';

$provider = strtolower(trim($_GET['provider'] ?? ''));
$mock = isset($_GET['mock']) ? true : false;

if ($mock) {
    // Simulação de login social sem credenciais reais
    $oauthUser = [
        'id' => 1000 + rand(1, 999),
        'nome' => ucfirst($provider) . ' User',
        'email' => "{$provider}_user@example.com",
        'nickname' => strtolower($provider) . rand(1000,9999),
        'foto_perfil' => "https://via.placeholder.com/150?text=" . urlencode(ucfirst($provider)),
        'rank' => 'Avançado',
        'plataforma' => 'PC',
        'ultimo_acesso' => date('Y-m-d H:i:s'),
    ];
    $_SESSION['oauth_user'] = $oauthUser;
    $_SESSION['usuario_id'] = $oauthUser['id'];
    $_SESSION['usuario_nome'] = $oauthUser['nome'];
    $_SESSION['usuario_nickname'] = $oauthUser['nickname'];
    $_SESSION['usuario_email'] = $oauthUser['email'];
    header('Location: dashboard.php');
    exit();
}

if (empty($provider)) {
    header('Location: index.html');
    exit();
}

// Segurança básica contra CSRF
if (!isset($_GET['state']) || !isset($_SESSION['oauth_state']) || $_GET['state'] !== $_SESSION['oauth_state']) {
    die('Estado OAuth inválido.');
}

$code = $_GET['code'] ?? null;
if (!$code) {
    die('Código OAuth não fornecido.');
}

$redirectUri = APP_URL . '/oauth_callback.php';

switch ($provider) {
    case 'google':
        $tokenUrl = 'https://oauth2.googleapis.com/token';
        $tokenParams = [
            'code' => $code,
            'client_id' => GOOGLE_CLIENT_ID,
            'client_secret' => GOOGLE_CLIENT_SECRET,
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code',
        ];
        break;

    case 'discord':
        $tokenUrl = 'https://discord.com/api/oauth2/token';
        $tokenParams = [
            'code' => $code,
            'client_id' => DISCORD_CLIENT_ID,
            'client_secret' => DISCORD_CLIENT_SECRET,
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code',
        ];
        break;

    case 'twitch':
        $tokenUrl = 'https://id.twitch.tv/oauth2/token';
        $tokenParams = [
            'code' => $code,
            'client_id' => TWITCH_CLIENT_ID,
            'client_secret' => TWITCH_CLIENT_SECRET,
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code',
        ];
        break;

    default:
        die('Provedor OAuth inválido.');
}

$ch = curl_init($tokenUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($tokenParams));
$tokenResponse = curl_exec($ch);
if ($tokenResponse === false) {
    die('Falha ao obter token OAuth: ' . curl_error($ch));
}
$tokenData = json_decode($tokenResponse, true);
curl_close($ch);

if (!$tokenData || !isset($tokenData['access_token'])) {
    die('Token OAuth inválido.');
}

$accessToken = $tokenData['access_token'];

switch ($provider) {
    case 'google':
        $userInfoUrl = 'https://www.googleapis.com/oauth2/v2/userinfo';
        $headers = ["Authorization: Bearer $accessToken"];
        break;

    case 'discord':
        $userInfoUrl = 'https://discord.com/api/users/@me';
        $headers = ["Authorization: Bearer $accessToken"];
        break;

    case 'twitch':
        $userInfoUrl = 'https://api.twitch.tv/helix/users';
        $headers = [
            "Authorization: Bearer $accessToken",
            "Client-Id: " . TWITCH_CLIENT_ID
        ];
        break;
}

$ch = curl_init($userInfoUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$userInfoResponse = curl_exec($ch);
if ($userInfoResponse === false) {
    die('Falha ao obter dados do usuário: ' . curl_error($ch));
}
$userInfo = json_decode($userInfoResponse, true);
curl_close($ch);

if (!$userInfo) {
    die('Dados de usuário inválidos.');
}

switch ($provider) {
    case 'google':
        $oauthUser = [
            'id' => $userInfo['id'] ?? uniqid('google_'),
            'nome' => $userInfo['name'] ?? 'Usuário Google',
            'email' => $userInfo['email'] ?? '',
            'nickname' => $userInfo['given_name'] ?? 'google_user',
            'foto_perfil' => $userInfo['picture'] ?? '',
            'rank' => 'Intermediário',
            'plataforma' => 'PC',
        ];
        break;

    case 'discord':
        $oauthUser = [
            'id' => $userInfo['id'] ?? uniqid('discord_'),
            'nome' => $userInfo['username'] ?? 'Usuário Discord',
            'email' => $userInfo['email'] ?? '',
            'nickname' => $userInfo['username'] ?? 'discord_user',
            'foto_perfil' => isset($userInfo['avatar']) ? "https://cdn.discordapp.com/avatars/{$userInfo['id']}/{$userInfo['avatar']}.png" : '',
            'rank' => 'Intermediário',
            'plataforma' => 'PC',
        ];
        break;

    case 'twitch':
        $userData = $userInfo['data'][0] ?? [];
        $oauthUser = [
            'id' => $userData['id'] ?? uniqid('twitch_'),
            'nome' => $userData['display_name'] ?? 'Usuário Twitch',
            'email' => $userData['email'] ?? '',
            'nickname' => $userData['login'] ?? 'twitch_user',
            'foto_perfil' => $userData['profile_image_url'] ?? '',
            'rank' => 'Intermediário',
            'plataforma' => 'PC',
        ];
        break;
}

$_SESSION['oauth_user'] = $oauthUser;
$_SESSION['usuario_id'] = $oauthUser['id'];
$_SESSION['usuario_nome'] = $oauthUser['nome'];
$_SESSION['usuario_nickname'] = $oauthUser['nickname'];
$_SESSION['usuario_email'] = $oauthUser['email'];

header('Location: dashboard.html');
exit();
