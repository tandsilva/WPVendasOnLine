<?php

define('DB_NAME', getenv('MYSQL_DATABASE'));
define('DB_USER', getenv('MYSQL_USER'));
define('DB_PASSWORD', getenv('MYSQL_PASSWORD'));
define('DB_HOST', getenv('MYSQL_HOST'));

define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', '');

define('AUTH_KEY',         'substitua-com-chaves-geradas');
define('SECURE_AUTH_KEY',  'substitua-com-chaves-geradas');
define('LOGGED_IN_KEY',    'substitua-com-chaves-geradas');
define('NONCE_KEY',        'substitua-com-chaves-geradas');
define('AUTH_SALT',        'substitua-com-chaves-geradas');
define('SECURE_AUTH_SALT', 'substitua-com-chaves-geradas');
define('LOGGED_IN_SALT',   'substitua-com-chaves-geradas');
define('NONCE_SALT',       'substitua-com-chaves-geradas');

$table_prefix = 'wp_';
define('WP_DEBUG', false);
define('WP_ALLOW_REPAIR', true);

if (!defined('ABSPATH')) define('ABSPATH', __DIR__ . '/');
require_once ABSPATH . 'wp-settings.php';
