
<?php
$host = getenv('MYSQLHOST') ?: getenv('WORDPRESS_DB_HOST');
$port = intval(getenv('MYSQLPORT')) ?: 3306;
$user = getenv('MYSQLUSER') ?: getenv('MYSQL_USER') ?: getenv('WORDPRESS_DB_USER');
$pass = getenv('MYSQLPASSWORD') ?: getenv('MYSQL_PASSWORD') ?: getenv('WORDPRESS_DB_PASSWORD');
$db   = getenv('MYSQL_DATABASE') ?: getenv('WORDPRESS_DB_NAME');

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
$mysqli = @new mysqli($host, $user, $pass, $db, $port);

if ($mysqli->connect_errno) {
  echo "❌ FALHA: ({$mysqli->connect_errno}) {$mysqli->connect_error}\n";
  die("</pre>");
}

echo "✅ CONECTOU COM SUCESSO!\n";
echo "Versão MySQL: " . $mysqli->server_info . "\n";
$mysqli->close();
echo "</pre>";
