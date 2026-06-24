<?php
// ============================================
// JK Game Match - Redirecionador OAuth
// ============================================

include 'config.php';

$provider = strtolower(trim($_GET['provider'] ?? ''));
$allowedProviders = ['google', 'discord', 'twitch'];

if (!in_array($provider, $allowedProviders, true)) {
    header('Location: index.html');
    exit();
}

$state = bin2hex(random_bytes(16));
$_SESSION['oauth_state'] = $state;

$redirectUri = APP_URL . '/oauth_callback.php';

switch ($provider) {
    case 'google':
        $clientId = GOOGLE_CLIENT_ID;
        $scope = 'openid email profile';
        if (empty($clientId) || empty(GOOGLE_CLIENT_SECRET)) {
            header("Location: oauth_callback.php?provider=google&mock=1");
            exit();
        }
        $params = [
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => $scope,
            'access_type' => 'online',
            'prompt' => 'select_account',
            'state' => $state,
        ];
        $authUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
        break;

    case 'discord':
        $clientId = DISCORD_CLIENT_ID;
        $scope = 'identify email';
        if (empty($clientId) || empty(DISCORD_CLIENT_SECRET)) {
            header("Location: oauth_callback.php?provider=discord&mock=1");
            exit();
        }
        $params = [
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => $scope,
            'prompt' => 'consent',
            'state' => $state,
        ];
        $authUrl = 'https://discord.com/api/oauth2/authorize?' . http_build_query($params);
        break;

    case 'twitch':
        $clientId = TWITCH_CLIENT_ID;
        $scope = 'user:read:email';
        if (empty($clientId) || empty(TWITCH_CLIENT_SECRET)) {
            header("Location: oauth_callback.php?provider=twitch&mock=1");
            exit();
        }
        $params = [
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => $scope,
            'state' => $state,
        ];
        $authUrl = 'https://id.twitch.tv/oauth2/authorize?' . http_build_query($params);
        break;
}

header('Location: ' . $authUrl);
exit();
