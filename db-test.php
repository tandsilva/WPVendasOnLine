
<?php
// Função helper para ler env vars (Railway compatível)
function get_env($key, $default = '') {
    return $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key) ?: $default;
}

$host = get_env('MYSQLHOST', get_env('WORDPRESS_DB_HOST'));
$port = intval(get_env('MYSQLPORT', '3306'));
$user = get_env('MYSQLUSER', get_env('MYSQL_USER', get_env('WORDPRESS_DB_USER')));
$pass = get_env('MYSQLPASSWORD', get_env('MYSQL_PASSWORD', get_env('WORDPRESS_DB_PASSWORD')));
$db   = get_env('MYSQL_DATABASE', get_env('WORDPRESS_DB_NAME'));

// Separa host:porta se vier junto
if (strpos($host, ':') !== false) {
    list($host, $port) = explode(':', $host, 2);
    $port = intval($port);
}

echo "<h2>Teste de Conexão MySQL Railway</h2>";
echo "<pre>";
echo "Host: $host\n";
echo "Port: $port\n";
echo "User: $user\n";
echo "DB: $db\n";
echo "Pass: " . (empty($pass) ? 'VAZIA!' : '****') . "\n\n";

echo "Tentando conectar ao servidor (sem banco específico)...\n";

// Primeiro conecta SEM especificar banco para listar os disponíveis
$mysqli = @new mysqli($host, $user, $pass, '', $port);

if ($mysqli->connect_errno) {
  echo "❌ FALHA: ({$mysqli->connect_errno}) {$mysqli->connect_error}\n";
  die("</pre>");
}

echo "✅ CONECTOU ao servidor MySQL!\n";
echo "Versão MySQL: " . $mysqli->server_info . "\n\n";

// Lista bancos disponíveis
echo "📋 BANCOS DE DADOS DISPONÍVEIS:\n";
$result = $mysqli->query("SHOW DATABASES");
$databases = [];
while ($row = $result->fetch_row()) {
    echo "  • {$row[0]}\n";
    $databases[] = $row[0];
}

// Verifica se o banco que queremos existe
$db_exists = in_array($db, $databases);

if (!$db_exists) {
    echo "\n⚠️  Banco '$db' NÃO existe!\n";
    echo "Criando banco '$db'...\n";
    
    if ($mysqli->query("CREATE DATABASE `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")) {
        echo "✅ Banco '$db' criado com sucesso!\n";
        $db_exists = true;
    } else {
        echo "❌ Erro ao criar banco: {$mysqli->error}\n";
    }
}

// Tenta selecionar o banco
if ($db_exists && $mysqli->select_db($db)) {
    echo "\n✅ Banco '$db' selecionado e pronto para usar!\n";
} else {
    echo "\n❌ Erro ao selecionar banco '$db'\n";
}

$mysqli->close();
echo "\n🎉 Teste concluído!";
echo "</pre>";
