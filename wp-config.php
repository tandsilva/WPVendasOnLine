<?php
/**
 * WordPress configuration for Railway
 */

define('DB_NAME', getenv('MYSQL_DATABASE'));          // railway
define('DB_USER', getenv('MYSQLUSER'));               // root
define('DB_PASSWORD', getenv('MYSQLPASSWORD'));       // senha do banco
define('DB_HOST', getenv('MYSQLHOST') . ':' . getenv('MYSQLPORT')); // mysql.railway.internal:3306

define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', '');

// Chaves de autenticação e salts
define('AUTH_KEY',         'put-your-unique-phrase-here');
define('SECURE_AUTH_KEY',  'put-your-unique-phrase-here');
define('LOGGED_IN_KEY',    'put-your-unique-phrase-here');
define('NONCE_KEY',        'put-your-unique-phrase-here');
define('AUTH_SALT',        'put-your-unique-phrase-here');
define('SECURE_AUTH_SALT', 'put-your-unique-phrase-here');
define('LOGGED_IN_SALT',   'put-your-unique-phrase-here');
define('NONCE_SALT',       'put-your-unique-phrase-here');

$table_prefix = 'wp_';
define('WP_DEBUG', false);

/* Repair */
define('WP_ALLOW_REPAIR', true);

if ( ! defined('ABSPATH') ) {
    define('ABSPATH', __DIR__ . '/');
}

require_once ABSPATH . 'wp-settings.php';
