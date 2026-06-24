<?php
// setup_banco.php
// Este arquivo executa o setup completo do banco de dados

require 'config.php';

$resultado = "<h2>Setup do Banco de Dados - JK Game Match</h2>";

try {
    // Ler o arquivo SQL
    $sqlFile = file_get_contents('database.sql');
    
    // Dividir em comandos individuais
    $comandos = explode(';', $sqlFile);
    
    $totalExecutados = 0;
    $erros = [];
    
    foreach ($comandos as $comando) {
        $comando = trim($comando);
        
        // Pular linhas vazias e comentários
        if (empty($comando) || substr($comando, 0, 2) === '--') {
            continue;
        }
        
        try {
            $conexao->exec($comando);
            $totalExecutados++;
        } catch (Exception $e) {
            $erros[] = "Erro ao executar comando: " . $e->getMessage();
        }
    }
    
    $resultado .= "<p style='color: green;'>✅ Setup concluído com sucesso!</p>";
    $resultado .= "<p>Comandos executados: <strong>$totalExecutados</strong></p>";
    
    if (!empty($erros)) {
        $resultado .= "<h3>Avisos:</h3><ul>";
        foreach ($erros as $erro) {
            $resultado .= "<li style='color: orange;'>⚠️ $erro</li>";
        }
        $resultado .= "</ul>";
    }
    
    // Verificar dados
    $result = $conexao->query("SELECT COUNT(*) as cnt FROM usuarios");
    $usuarios = $result->fetch()['cnt'];
    
    $result = $conexao->query("SELECT COUNT(*) as cnt FROM jogos");
    $jogos = $result->fetch()['cnt'];
    
    $resultado .= "<h3>Status do Banco:</h3>";
    $resultado .= "<ul>";
    $resultado .= "<li>Usuários: <strong>$usuarios</strong></li>";
    $resultado .= "<li>Jogos: <strong>$jogos</strong></li>";
    $resultado .= "</ul>";
    
    $resultado .= "<h3>Próximos passos:</h3>";
    $resultado .= "<ol>";
    $resultado .= "<li><a href='criar_usuario.php'>Criar um usuário</a></li>";
    $resultado .= "<li><a href='index.html'>Fazer login</a></li>";
    $resultado .= "</ol>";
    
} catch (Exception $e) {
    $resultado .= "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup - JK Game Match</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #1a1a1a;
            color: #fff;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            background: #222;
            padding: 30px;
            border-radius: 8px;
            border: 1px solid #00d4ff;
        }
        h2 {
            color: #00d4ff;
        }
        h3 {
            color: #00d4ff;
            margin-top: 20px;
        }
        a {
            color: #00d4ff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        ol, ul {
            line-height: 1.8;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php echo $resultado; ?>
    </div>
</body>
</html>
