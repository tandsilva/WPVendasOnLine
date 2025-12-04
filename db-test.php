
<?php
$host = getenv('MYSQLHOST');
$port = intval(getenv('MYSQLPORT'));
$user = getenv('MYSQLUSER');
$pass = getenv('MYSQLPASSWORD');
$db   = getenv('MYSQL_DATABASE');

echo "Tentando conectar a $host:$port com $user no DB $db...\n";

$mysqli = new mysqli($host, $user, $pass, $db, $port);
if ($mysqli->connect_errno) {
  die("Falha: ({$mysqli->connect_errno}) {$mysqli->connect_error}\n");
}
echo "Conectou!\n";
$mysqli->close();
