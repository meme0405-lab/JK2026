<?php
// ============================================
// JK Game Match - Configurações
// ============================================

// Session settings are configured before starting the session.

/**
 * Carrega variáveis de ambiente de um arquivo .env local.
 */
function carregar_env($path) {
    if (!file_exists($path) || !is_readable($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        if (!str_contains($line, '=')) {
            continue;
        }

        [$name, $value] = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        $value = trim($value, "'\"");

        if ($name === '') {
            continue;
        }

        putenv("$name=$value");
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }
}

carregar_env(__DIR__ . '/.env');

// Configuração do banco de dados
define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_NAME', getenv('DB_NAME') ?: 'jk_game_match');
define('DB_USER', getenv('DB_USER') ?: 'root');

$envDbPass = getenv('DB_PASS');
define('DB_PASS', $envDbPass !== false ? $envDbPass : '');

define('DB_PORT', getenv('DB_PORT') ?: 3307);

// Configurações da aplicação
define('APP_NAME', getenv('APP_NAME') ?: 'JK Game Match');
define('APP_ENV', getenv('APP_ENV') ?: 'development'); // development ou production

$envAppUrl = getenv('APP_URL');
if ($envAppUrl !== false && $envAppUrl !== '') {
    define('APP_URL', $envAppUrl);
} else {
    function detectar_app_url() {
        if (!empty($_SERVER['HTTP_HOST'])) {
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'];
            $scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
            $scriptDir = str_replace('\\', '/', $scriptDir);
            $scriptDir = rtrim($scriptDir, '/');
            return rtrim($scheme . '://' . $host . $scriptDir, '/');
        }
        return 'http://localhost/site_JK';
    }

    define('APP_URL', detectar_app_url());
}

define('GOOGLE_CLIENT_ID', getenv('GOOGLE_CLIENT_ID') ?: '');
define('GOOGLE_CLIENT_SECRET', getenv('GOOGLE_CLIENT_SECRET') ?: '');
define('DISCORD_CLIENT_ID', getenv('DISCORD_CLIENT_ID') ?: '');
define('DISCORD_CLIENT_SECRET', getenv('DISCORD_CLIENT_SECRET') ?: '');
define('TWITCH_CLIENT_ID', getenv('TWITCH_CLIENT_ID') ?: '');
define('TWITCH_CLIENT_SECRET', getenv('TWITCH_CLIENT_SECRET') ?: '');

// Configurações de segurança
define('SESSION_TIMEOUT', 3600); // 1 hora
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_TIME', 900); // 15 minutos

// Configurações de upload
define('MAX_UPLOAD_SIZE', 5242880); // 5MB
define('UPLOAD_DIR', 'uploads/');
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

// Configurações de paginação
define('ITEMS_PER_PAGE', 12);
define('MATCHES_PER_PAGE', 10);

// Conexão com banco de dados
$conexao = null;

try {
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $conexao = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    if (APP_ENV === 'production') {
        die("Erro ao conectar ao banco de dados. Por favor, tente novamente mais tarde.");
    }

    $mensagem = "Erro de conexão PDO: " . $e->getMessage();
    if ($e->getCode() === '1045') {
        $mensagem .= "\nVerifique DB_USER e DB_PASS no arquivo .env. Para esta instalação, o usuário root está sendo usado sem senha.\nSe o seu MySQL tiver senha, coloque-a em DB_PASS.";
    }

    die($mensagem);
}

// ============================================
// FUNÇÕES DE SEGURANÇA
// ============================================

/**
 * Sanitiza uma string removendo caracteres especiais
 */
function sanitizar($string) {
    return htmlspecialchars(strip_tags(trim($string)), ENT_QUOTES, 'UTF-8');
}

/**
 * Valida um email
 */
function validar_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Valida um URL
 */
function validar_url($url) {
    return filter_var($url, FILTER_VALIDATE_URL);
}

/**
 * Verifica se o usuário está autenticado
 */
function verificar_autenticacao() {
    if (!isset($_SESSION['usuario_id']) || empty($_SESSION['usuario_id'])) {
        header("Location: index.html");
        exit();
    }
}

/**
 * Verifica se o usuário está autenticado para APIs JSON
 */
function verificar_autenticacao_json() {
    if (!isset($_SESSION['usuario_id']) || empty($_SESSION['usuario_id'])) {
        http_response_code(401);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'error', 'mensagem' => 'Usuário não autenticado']);
        exit();
    }
}

/**
 * Obtém o ID do usuário atual
 */
function get_usuario_id() {
    return $_SESSION['usuario_id'] ?? null;
}

/**
 * Faz log de ações importantes
 */
function fazer_log($tipo, $mensagem, $usuario_id = null) {
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'desconhecido';
    $log_message = "[$timestamp] [$tipo] IP: $ip | Usuário: $usuario_id | $mensagem\n";
    
    // Salvar em arquivo de log (criar diretório logs/ se não existir)
    if (!is_dir('logs')) {
        mkdir('logs', 0755, true);
    }
    
    file_put_contents('logs/app.log', $log_message, FILE_APPEND);
}

/**
 * Retorna JSON com status
 */
function responder_json($status, $mensagem, $dados = null) {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => $status,
        'mensagem' => $mensagem,
        'dados' => $dados
    ]);
    exit();
}

/**
 * Gera um token CSRF
 */
function gerar_token_csrf() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verifica um token CSRF
 */
function verificar_token_csrf($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Redireciona com segurança
 */
function redirecionar($url) {
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        header("Location: " . $url);
        exit();
    } else {
        header("Location: index.html");
        exit();
    }
}

/**
 * Formata data para exibição
 */
function formatar_data($data, $formato = 'd/m/Y H:i') {
    return date($formato, strtotime($data));
}

/**
 * Calcula tempo desde uma data
 */
function tempo_decorrido($data) {
    $data = strtotime($data);
    $agora = time();
    $diff = $agora - $data;
    
    if ($diff < 60) {
        return "há alguns segundos";
    } elseif ($diff < 3600) {
        $minutos = floor($diff / 60);
        return "há " . $minutos . " minuto" . ($minutos > 1 ? "s" : "");
    } elseif ($diff < 86400) {
        $horas = floor($diff / 3600);
        return "há " . $horas . " hora" . ($horas > 1 ? "s" : "");
    } elseif ($diff < 604800) {
        $dias = floor($diff / 86400);
        return "há " . $dias . " dia" . ($dias > 1 ? "s" : "");
    } else {
        return formatar_data(date('Y-m-d H:i:s', $data));
    }
}

// ============================================
// HEADER DE SEGURANÇA
// ============================================

// Configurar headers de segurança
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// Configurar cookie de sessão apenas se a sessão ainda não foi iniciada
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', APP_ENV === 'production' ? 1 : 0);
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.use_strict_mode', 1);
    session_start();
}

?>

