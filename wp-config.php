
<?php
/**
 * WordPress config para Railway (serviço dentro do mesmo projeto)
 * Usando as variáveis WORDPRESS_DB_* sugeridas pela Railway.
 */

// Credenciais do MySQL (Railway - host público com porta)
define('DB_NAME',     getenv('MYSQL_DATABASE') ?: getenv('WORDPRESS_DB_NAME') ?: 'railway_db');
define('DB_USER',     getenv('MYSQLUSER') ?: getenv('MYSQL_USER') ?: getenv('WORDPRESS_DB_USER') ?: 'root');
define('DB_PASSWORD', getenv('MYSQLPASSWORD') ?: getenv('MYSQL_PASSWORD') ?: getenv('WORDPRESS_DB_PASSWORD'));

// Para Railway: host e porta separados
$db_host = getenv('MYSQLHOST') ?: getenv('WORDPRESS_DB_HOST') ?: 'localhost';
$db_port = getenv('MYSQLPORT') ?: '3306';

// Se DB_HOST vier com formato "host:porta", separa
if (strpos($db_host, ':') !== false) {
    list($db_host, $db_port) = explode(':', $db_host, 2);
}

// IMPORTANTE: Força conexão TCP para banco remoto  
// Adiciona 'p:' antes do host para forçar conexão persistente TCP
define('DB_HOST', $db_host . ':' . $db_port);

// Charset/Collation
define('DB_CHARSET',  'utf8mb4');
define('DB_COLLATE',  '');

// Prefixo de tabelas
$table_prefix = 'wp_';

// Debug
define('WP_DEBUG', false);

// (opcional) Reparar DB (remova depois de resolver)
define('WP_ALLOW_REPAIR', true);

// Substitua por chaves/salts geradas:
// Gere em: https://api.wordpress.org/secret-key/1.1/salt/
define('AUTH_KEY',         'coloque-sua-chave-aqui');
define('SECURE_AUTH_KEY',  'coloque-sua-chave-aqui');
define('LOGGED_IN_KEY',    'coloque-sua-chave-aqui');
define('NONCE_KEY',        'coloque-sua-chave-aqui');
define('AUTH_SALT',        'coloque-sua-chave-aqui');
define('SECURE_AUTH_SALT', 'coloque-sua-chave-aqui');
define('LOGGED_IN_SALT',   'coloque-sua-chave-aqui');
define('NONCE_SALT',       'coloque-sua-chave-aqui');

if ( ! defined('ABSPATH') ) {
  define('ABSPATH', __DIR__ . '/');
}

// DIAGNÓSTICO: Testa conexão ANTES de carregar WordPress
if (!defined('WP_INSTALLING') && php_sapi_name() !== 'cli') {
    $test_host = $db_host;
    $test_port = intval($db_port);
    
    $test_conn = @new mysqli($test_host, DB_USER, DB_PASSWORD, DB_NAME, $test_port);
    
    if ($test_conn->connect_errno) {
        die(sprintf(
            '<h1>Erro de Conexão MySQL (Diagnóstico)</h1>
            <pre>
Host: %s
Port: %d
User: %s
DB: %s
Error (#%d): %s

VARIÁVEIS DE AMBIENTE:
MYSQLHOST: %s
MYSQLPORT: %s
MYSQLUSER: %s
MYSQL_DATABASE: %s
WORDPRESS_DB_HOST: %s

PHP Info:
- mysqli extension: %s
- Tentando conexão TCP em: %s:%d
</pre>',
            $test_host,
            $test_port,
            DB_USER,
            DB_NAME,
            $test_conn->connect_errno,
            $test_conn->connect_error,
            getenv('MYSQLHOST') ?: 'não definido',
            getenv('MYSQLPORT') ?: 'não definido',
            getenv('MYSQLUSER') ?: 'não definido',
            getenv('MYSQL_DATABASE') ?: 'não definido',
            getenv('WORDPRESS_DB_HOST') ?: 'não definido',
            extension_loaded('mysqli') ? 'INSTALADA' : 'NÃO INSTALADA',
            $test_host,
            $test_port
        ));
    }
    $test_conn->close();
}

require_once ABSPATH . 'wp-settings.php';
