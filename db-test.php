
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

echo "Tentando conectar...\n";

// Força conexão TCP especificando porta e socket=null
set_error_handler(function($errno, $errstr) {
    echo "PHP Error: $errstr\n";
});

$start = microtime(true);
$mysqli = new mysqli($host, $user, $pass, $db, $port);
$duration = round(microtime(true) - $start, 2);

restore_error_handler();

echo "Tempo de tentativa: {$duration}s\n\n";

if ($mysqli->connect_errno) {
  echo "❌ FALHA: ({$mysqli->connect_errno}) {$mysqli->connect_error}\n";
  die("</pre>");
}

echo "✅ CONECTOU COM SUCESSO!\n";
echo "Versão MySQL: " . $mysqli->server_info . "\n";
$mysqli->close();
echo "</pre>";
