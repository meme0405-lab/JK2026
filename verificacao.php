<?php
// ============================================
// JK Game Match - Script de Verificação
// Acesse em: http://localhost/site_JK/verificacao.php
// ============================================

echo "<!DOCTYPE html>";
echo "<html lang='pt-BR'>";
echo "<head>";
echo "    <meta charset='UTF-8'>";
echo "    <meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "    <title>JK Game Match - Verificação de Instalação</title>";
echo "    <style>";
echo "        * { margin: 0; padding: 0; box-sizing: border-box; }";
echo "        body {";
echo "            background: linear-gradient(135deg, #0a0e27 0%, #1a1f3a 100%);";
echo "            color: #e0e0e0;";
echo "            font-family: 'Segoe UI', Roboto, sans-serif;";
echo "            padding: 40px 20px;";
echo "            min-height: 100vh;";
echo "        }";
echo "        .container {";
echo "            max-width: 800px;";
echo "            margin: 0 auto;";
echo "        }";
echo "        .header {";
echo "            text-align: center;";
echo "            margin-bottom: 40px;";
echo "            animation: slideDown 0.5s ease;";
echo "        }";
echo "        .header h1 {";
echo "            font-size: 2.5em;";
echo "            background: linear-gradient(135deg, #9d4edd, #00d9ff);";
echo "            -webkit-background-clip: text;";
echo "            -webkit-text-fill-color: transparent;";
echo "            margin-bottom: 10px;";
echo "        }";
echo "        .header p {";
echo "            color: #a0a0a0;";
echo "            font-size: 0.95em;";
echo "        }";
echo "        .check-group {";
echo "            background: rgba(26, 31, 58, 0.8);";
echo "            border: 1px solid #3a3f5a;";
echo "            border-radius: 12px;";
echo "            padding: 20px;";
echo "            margin-bottom: 20px;";
echo "            backdrop-filter: blur(10px);";
echo "            animation: slideIn 0.5s ease 0.1s both;";
echo "        }";
echo "        .check-group h2 {";
echo "            color: #00d9ff;";
echo "            font-size: 1.2em;";
echo "            margin-bottom: 15px;";
echo "            border-bottom: 2px solid #9d4edd;";
echo "            padding-bottom: 10px;";
echo "        }";
echo "        .check-item {";
echo "            display: flex;";
echo "            align-items: center;";
echo "            padding: 10px 0;";
echo "            border-bottom: 1px solid rgba(58, 63, 90, 0.5);";
echo "        }";
echo "        .check-item:last-child {";
echo "            border-bottom: none;";
echo "        }";
echo "        .status-icon {";
echo "            display: flex;";
echo "            align-items: center;";
echo "            justify-content: center;";
echo "            width: 30px;";
echo "            height: 30px;";
echo "            border-radius: 50%;";
echo "            margin-right: 15px;";
echo "            font-weight: bold;";
echo "            font-size: 0.9em;";
echo "        }";
echo "        .status-icon.ok {";
echo "            background: rgba(0, 255, 136, 0.2);";
echo "            color: #00ff88;";
echo "            border: 2px solid #00ff88;";
echo "        }";
echo "        .status-icon.warning {";
echo "            background: rgba(255, 165, 0, 0.2);";
echo "            color: #ffa500;";
echo "            border: 2px solid #ffa500;";
echo "        }";
echo "        .status-icon.error {";
echo "            background: rgba(255, 0, 110, 0.2);";
echo "            color: #ff006e;";
echo "            border: 2px solid #ff006e;";
echo "        }";
echo "        .check-text {";
echo "            flex: 1;";
echo "        }";
echo "        .check-text strong {";
echo "            color: #e0e0e0;";
echo "        }";
echo "        .check-text span {";
echo "            color: #a0a0a0;";
echo "            font-size: 0.9em;";
echo "        }";
echo "        .summary {";
echo "            background: rgba(26, 31, 58, 0.8);";
echo "            border: 2px solid #00d9ff;";
echo "            border-radius: 12px;";
echo "            padding: 20px;";
echo "            text-align: center;";
echo "            animation: slideIn 0.5s ease 0.3s both;";
echo "        }";
echo "        .summary.success {";
echo "            border-color: #00ff88;";
echo "        }";
echo "        .summary.warning {";
echo "            border-color: #ffa500;";
echo "        }";
echo "        .summary.error {";
echo "            border-color: #ff006e;";
echo "        }";
echo "        .summary h3 {";
echo "            font-size: 1.5em;";
echo "            margin-bottom: 10px;";
echo "        }";
echo "        .summary p {";
echo "            color: #a0a0a0;";
echo "            margin-bottom: 15px;";
echo "        }";
echo "        .action-button {";
echo "            display: inline-block;";
echo "            padding: 10px 30px;";
echo "            background: linear-gradient(135deg, #9d4edd, #00d9ff);";
echo "            color: #0a0e27;";
echo "            border: none;";
echo "            border-radius: 8px;";
echo "            font-weight: bold;";
echo "            cursor: pointer;";
echo "            text-decoration: none;";
echo "            transition: all 0.3s ease;";
echo "        }";
echo "        .action-button:hover {";
echo "            transform: translateY(-2px);";
echo "            box-shadow: 0 10px 25px rgba(157, 78, 221, 0.4);";
echo "        }";
echo "        @keyframes slideDown {";
echo "            from { opacity: 0; transform: translateY(-20px); }";
echo "            to { opacity: 1; transform: translateY(0); }";
echo "        }";
echo "        @keyframes slideIn {";
echo "            from { opacity: 0; transform: translateX(-20px); }";
echo "            to { opacity: 1; transform: translateX(0); }";
echo "        }";
echo "    </style>";
echo "</head>";
echo "<body>";
echo "    <div class='container'>";
echo "        <div class='header'>";
echo "            <h1>🎮 JK Game Match</h1>";
echo "            <p>Verificação de Instalação e Ambiente</p>";
echo "        </div>";

$checks = [];
$total_ok = 0;
$total_warning = 0;
$total_error = 0;

// 1. Verificar PHP
$check = [
    'name' => 'PHP Version',
    'status' => 'ok',
    'message' => 'PHP ' . PHP_VERSION
];
if (version_compare(PHP_VERSION, '7.4.0', '<')) {
    $check['status'] = 'warning';
    $check['message'] = 'PHP ' . PHP_VERSION . ' (Recomendado 7.4+)';
    $total_warning++;
} else {
    $total_ok++;
}
$checks['php'] = $check;

// 2. Verificar PDO
$check = [
    'name' => 'PDO Extension',
    'status' => 'ok',
    'message' => 'PDO MySQL disponível'
];
if (!extension_loaded('pdo') || !extension_loaded('pdo_mysql')) {
    $check['status'] = 'error';
    $check['message'] = 'PDO MySQL não encontrado';
    $total_error++;
} else {
    $total_ok++;
}
$checks['pdo'] = $check;

// 3. Verificar Session
$check = [
    'name' => 'Session Support',
    'status' => 'ok',
    'message' => 'Sessions ativas'
];
if (!extension_loaded('session')) {
    $check['status'] = 'error';
    $check['message'] = 'Sessions não disponível';
    $total_error++;
} else {
    $total_ok++;
}
$checks['session'] = $check;

// 4. Verificar Arquivos HTML
    $html_files = ['index.html', 'cadastro.html', 'dashboard.html', 'jogadores.html', 'matches.html', 'perfil.php', 'jogos_favoritos.html', 'GUIA_RAPIDO.html'];
$missing_html = [];
foreach ($html_files as $file) {
    if (!file_exists($file)) {
        $missing_html[] = $file;
    }
}
$check = [
    'name' => 'Arquivos HTML',
    'status' => count($missing_html) === 0 ? 'ok' : 'error',
    'message' => count($missing_html) === 0 ? 'Todos os 8 arquivos HTML encontrados' : 'Faltam: ' . implode(', ', $missing_html)
];
if ($check['status'] === 'ok') {
    $total_ok++;
} else {
    $total_error++;
}
$checks['html'] = $check;

// 5. Verificar CSS
$check = [
    'name' => 'Arquivo CSS',
    'status' => file_exists('css/style.css') ? 'ok' : 'error',
    'message' => file_exists('css/style.css') ? 'css/style.css encontrado' : 'css/style.css não encontrado'
];
if ($check['status'] === 'ok') {
    $total_ok++;
} else {
    $total_error++;
}
$checks['css'] = $check;

// 6. Verificar JavaScript
$js_files = ['script.js', 'dashboard.js', 'players.js', 'matches.js', 'profile.js', 'games.js'];
$missing_js = [];
foreach ($js_files as $file) {
    if (!file_exists('js/' . $file)) {
        $missing_js[] = $file;
    }
}
$check = [
    'name' => 'Arquivos JavaScript',
    'status' => count($missing_js) === 0 ? 'ok' : 'error',
    'message' => count($missing_js) === 0 ? 'Todos os 6 arquivos JS encontrados' : 'Faltam: ' . implode(', ', $missing_js)
];
if ($check['status'] === 'ok') {
    $total_ok++;
} else {
    $total_error++;
}
$checks['js'] = $check;

// 7. Verificar PHP Backend
$php_files = ['config.php', 'login.php', 'cadastro.php', 'logout.php'];
$missing_php = [];
foreach ($php_files as $file) {
    if (!file_exists($file)) {
        $missing_php[] = $file;
    }
}
$check = [
    'name' => 'Arquivos PHP Backend',
    'status' => count($missing_php) === 0 ? 'ok' : 'error',
    'message' => count($missing_php) === 0 ? 'Todos os 4 arquivos PHP encontrados' : 'Faltam: ' . implode(', ', $missing_php)
];
if ($check['status'] === 'ok') {
    $total_ok++;
} else {
    $total_error++;
}
$checks['php_backend'] = $check;

// 8. Verificar Database SQL
$check = [
    'name' => 'Script de Banco de Dados',
    'status' => file_exists('database.sql') ? 'ok' : 'warning',
    'message' => file_exists('database.sql') ? 'database.sql encontrado' : 'database.sql não encontrado (será necessário importar)'
];
if ($check['status'] === 'ok') {
    $total_ok++;
} else {
    $total_warning++;
}
$checks['db'] = $check;

// 9. Verificar config.php
$check = [
    'name' => 'Arquivo de Configuração',
    'status' => file_exists('config.php') && is_readable('config.php') ? 'ok' : 'error',
    'message' => file_exists('config.php') && is_readable('config.php') ? 'config.php encontrado e legível' : 'config.php não encontrado ou não legível'
];
if ($check['status'] === 'ok') {
    $total_ok++;
} else {
    $total_error++;
}
$checks['config'] = $check;

// 10. Verificar .htaccess
$check = [
    'name' => 'Apache Configuration',
    'status' => file_exists('.htaccess') ? 'ok' : 'warning',
    'message' => file_exists('.htaccess') ? '.htaccess configurado' : '.htaccess não encontrado (URL rewriting desabilitado)'
];
if ($check['status'] === 'ok') {
    $total_ok++;
} else {
    $total_warning++;
}
$checks['htaccess'] = $check;

// 11. Verificar escrita de arquivos
$check = [
    'name' => 'Permissões de Escrita',
    'status' => 'warning',
    'message' => 'Crie as pastas: logs/, uploads/, cache/'
];
$total_warning++;
$checks['permissions'] = $check;

// 12. Verificar extensões opcionais
$check = [
    'name' => 'OpenSSL (Segurança)',
    'status' => extension_loaded('openssl') ? 'ok' : 'warning',
    'message' => extension_loaded('openssl') ? 'OpenSSL disponível' : 'OpenSSL não disponível (HTTPS pode ter problemas)'
];
if ($check['status'] === 'ok') {
    $total_ok++;
} else {
    $total_warning++;
}
$checks['openssl'] = $check;

// Renderizar resultados
echo "        <div class='check-group'>";
echo "            <h2>🖥️ Ambiente do Servidor</h2>";
foreach (['php', 'pdo', 'session', 'openssl'] as $key) {
    $c = $checks[$key];
    echo "            <div class='check-item'>";
    echo "                <div class='status-icon " . $c['status'] . "'>" . ($c['status'] === 'ok' ? '✓' : ($c['status'] === 'warning' ? '⚠' : '✕')) . "</div>";
    echo "                <div class='check-text'>";
    echo "                    <strong>" . $c['name'] . "</strong><br>";
    echo "                    <span>" . $c['message'] . "</span>";
    echo "                </div>";
    echo "            </div>";
}
echo "        </div>";

echo "        <div class='check-group'>";
echo "            <h2>📁 Arquivos do Projeto</h2>";
foreach (['html', 'css', 'js', 'php_backend', 'config'] as $key) {
    $c = $checks[$key];
    echo "            <div class='check-item'>";
    echo "                <div class='status-icon " . $c['status'] . "'>" . ($c['status'] === 'ok' ? '✓' : ($c['status'] === 'warning' ? '⚠' : '✕')) . "</div>";
    echo "                <div class='check-text'>";
    echo "                    <strong>" . $c['name'] . "</strong><br>";
    echo "                    <span>" . $c['message'] . "</span>";
    echo "                </div>";
    echo "            </div>";
}
echo "        </div>";

echo "        <div class='check-group'>";
echo "            <h2>⚙️ Configuração</h2>";
foreach (['db', 'htaccess', 'permissions'] as $key) {
    $c = $checks[$key];
    echo "            <div class='check-item'>";
    echo "                <div class='status-icon " . $c['status'] . "'>" . ($c['status'] === 'ok' ? '✓' : ($c['status'] === 'warning' ? '⚠' : '✕')) . "</div>";
    echo "                <div class='check-text'>";
    echo "                    <strong>" . $c['name'] . "</strong><br>";
    echo "                    <span>" . $c['message'] . "</span>";
    echo "                </div>";
    echo "            </div>";
}
echo "        </div>";

// Renderizar resumo
$summary_status = $total_error > 0 ? 'error' : ($total_warning > 0 ? 'warning' : 'success');
$summary_message = $total_error > 0 ? '❌ Erros encontrados! Corrija os itens acima.' : ($total_warning > 0 ? '⚠️ Atenção: Algumas configurações precisam de ajustes.' : '✅ Tudo OK! Pronto para usar.');

echo "        <div class='summary " . $summary_status . "'>";
echo "            <h3>" . $summary_message . "</h3>";
echo "            <p>OK: " . $total_ok . " | Avisos: " . $total_warning . " | Erros: " . $total_error . "</p>";
echo "            <a href='index.html' class='action-button'>Ir para Login 🎮</a>";
echo "        </div>";

echo "    </div>";
echo "</body>";
echo "</html>";

?>
