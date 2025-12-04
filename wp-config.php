<?php
/**
 * WordPress config para Railway (serviço dentro do mesmo projeto)
 * Usando as variáveis WORDPRESS_DB_* sugeridas pela Railway.
 */

// Função helper para ler variáveis de ambiente (Railway compatível)
function get_env($key, $default = '') {
    return $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key) ?: $default;
}

// URLs do WordPress - Usa domínio customizado
define('WP_HOME', 'https://facilcompra.online');
define('WP_SITEURL', 'https://facilcompra.online');

// Força HTTPS no Railway
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
}

// Credenciais do MySQL (Railway - host público com porta)
define('DB_NAME',     get_env('MYSQL_DATABASE', get_env('WORDPRESS_DB_NAME', 'railway_db')));
define('DB_USER',     get_env('MYSQLUSER', get_env('MYSQL_USER', get_env('WORDPRESS_DB_USER', 'root'))));
define('DB_PASSWORD', get_env('MYSQLPASSWORD', get_env('MYSQL_PASSWORD', get_env('WORDPRESS_DB_PASSWORD'))));

// Para Railway: host e porta separados
$db_host = get_env('MYSQLHOST', get_env('WORDPRESS_DB_HOST', 'localhost'));
$db_port = get_env('MYSQLPORT', '3306');

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

// Debug - ATIVADO temporariamente para ver erros
define('WP_DEBUG', true);
define('WP_DEBUG_DISPLAY', true);
define('WP_DEBUG_LOG', true);
@ini_set('display_errors', 1);

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

// AUTO-CRIAÇÃO DO BANCO: Garante que o banco existe antes de carregar o WordPress
if (!defined('WP_INSTALLING') && php_sapi_name() !== 'cli') {
    $test_host = $db_host;
    $test_port = intval($db_port);
    
    // Conecta sem especificar banco primeiro
    $test_conn = @new mysqli($test_host, DB_USER, DB_PASSWORD, '', $test_port);
    
    if ($test_conn->connect_errno) {
        die(sprintf(
            '<h1>Erro de Conexão MySQL</h1>
            <pre>Não foi possível conectar ao servidor MySQL.
Host: %s:%d
User: %s
Error: %s</pre>',
            $test_host,
            $test_port,
            DB_USER,
            $test_conn->connect_error
        ));
    }
    
    // Cria o banco se não existir
    $test_conn->query("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $test_conn->close();
}

require_once ABSPATH . 'wp-settings.php';
