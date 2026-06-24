<?php
// db_debug.php
// Use este arquivo para verificar se o .env está sendo lido corretamente e se as credenciais de DB são válidas.

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

        putenv("$name=$value");
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }
}

carregar_env(__DIR__ . '/.env');

$dbHost = getenv('DB_HOST') ?: 'localhost';

$dbUser = getenv('DB_USER');
$dbUser = ($dbUser !== false && $dbUser !== '') ? $dbUser : 'root';

$dbPass = getenv('DB_PASS');
$dbPass = ($dbPass !== false) ? $dbPass : '';

$dbName = getenv('DB_NAME') ?: 'jk_game_match';
$dbPort = getenv('DB_PORT') ?: 3306;

header('Content-Type: text/plain; charset=utf-8');
echo "Verificando conexão de banco de dados...\n";
echo "DB_HOST = $dbHost\n";
echo "DB_USER = $dbUser\n";
echo "DB_PASS = " . ($dbPass === false ? '[não definido]' : ($dbPass === '' ? '[vazio]' : '[definido]')) . "\n";
echo "DB_NAME = $dbName\n";
echo "DB_PORT = $dbPort\n\n";

$mysqli = @new mysqli($dbHost, $dbUser, $dbPass, $dbName, $dbPort);
if ($mysqli->connect_errno) {
    echo "Falha na conexão: ({$mysqli->connect_errno}) {$mysqli->connect_error}\n";
    echo "\nSugestão:\n";
    echo "- Se o MySQL root tem senha, insira o valor em DB_PASS no arquivo .env.\n";
    echo "- Se o root não tem senha, deixe DB_PASS vazio ou remova a linha do arquivo .env.\n";
} else {
    echo "Conectado com sucesso ao banco de dados.\n";
    $mysqli->close();
}
