<?php
/**
 * WordPress config para Railway (serviço dentro do mesmo projeto)
 * Usando as variáveis WORDPRESS_DB_* sugeridas pela Railway.
 */

// Função helper para ler variáveis de ambiente (Railway compatível)
function get_env($key, $default = '') {
    return $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key) ?: $default;
}

// URLs do WordPress - Detecta automaticamente
if (isset($_SERVER['HTTP_HOST'])) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    define('WP_HOME', $protocol . $_SERVER['HTTP_HOST']);
    define('WP_SITEURL', $protocol . $_SERVER['HTTP_HOST']);
} else {
    define('WP_HOME', 'https://facilcompra.online');
    define('WP_SITEURL', 'https://facilcompra.online');
}

// Força HTTPS no Railway
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
}

// Credenciais do MySQL (Railway - host público com porta)
define('DB_NAME',     get_env('MYSQL_DATABASE', get_env('WORDPRESS_DB_NAME', 'railway')));
define('DB_USER',     get_env('MYSQLUSER', get_env('MYSQL_USER', get_env('WORDPRESS_DB_USER', 'root'))));
define('DB_PASSWORD', get_env('MYSQLPASSWORD', get_env('MYSQL_PASSWORD', get_env('WORDPRESS_DB_PASSWORD'))));

// Para Railway: usa conexão privada interna se disponível
$db_host = get_env('MYSQL_PRIVATE_URL') ? parse_url(get_env('MYSQL_PRIVATE_URL'), PHP_URL_HOST) : get_env('MYSQLHOST', get_env('WORDPRESS_DB_HOST', 'localhost'));
$db_port = get_env('MYSQL_PRIVATE_URL') ? parse_url(get_env('MYSQL_PRIVATE_URL'), PHP_URL_PORT) : get_env('MYSQLPORT', '3306');

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

require_once ABSPATH . 'wp-settings.php';
