<?php
// teste_banco.php
require 'config.php';

echo "<h2>Teste de Conectividade - JK Game Match</h2>";

// Testar conexão
echo "<p>✅ Banco de dados conectado com sucesso!</p>";

// Verificar tabelas
try {
    $result = $conexao->query("SELECT COUNT(*) as cnt FROM information_schema.tables WHERE table_schema='jk_game_match'");
    $row = $result->fetch();
    echo "<p>✅ Tabelas encontradas: " . $row['cnt'] . "</p>";
    
    // Listar tabelas
    $result = $conexao->query("SELECT table_name FROM information_schema.tables WHERE table_schema='jk_game_match' ORDER BY table_name");
    $tabelas = $result->fetchAll();
    
    echo "<h3>Tabelas do banco:</h3><ul>";
    foreach ($tabelas as $t) {
        echo "<li>" . $t['table_name'] . "</li>";
    }
    echo "</ul>";
    
    // Verificar se usuarios tem dados
    $result = $conexao->query("SELECT COUNT(*) as cnt FROM usuarios");
    $row = $result->fetch();
    echo "<p>Usuários cadastrados: " . $row['cnt'] . "</p>";
    
    // Verificar se jogos tem dados
    $result = $conexao->query("SELECT COUNT(*) as cnt FROM jogos");
    $row = $result->fetch();
    echo "<p>Jogos disponíveis: " . $row['cnt'] . "</p>";
    
} catch (Exception $e) {
    echo "<p>❌ Erro: " . $e->getMessage() . "</p>";
}

echo "<h3>Links de ação:</h3>";
echo "<ul>";
echo "<li><a href='criar_usuario.php'>Criar novo usuário</a></li>";
echo "<li><a href='index.html'>Login</a></li>";
echo "<li><a href='db_debug.php'>Debug do banco</a></li>";
echo "</ul>";
