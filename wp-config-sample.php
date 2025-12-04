<?php
/**
 * WordPress base configuration sample for Railway deployment.
 *
 * Copy this file to wp-config.php and fill in your environment variables.
 */

// ** MySQL settings ** //
define( 'DB_NAME', getenv('MYSQL_DATABASE') );
define( 'DB_USER', getenv('MYSQL_USER') );
define( 'DB_PASSWORD', getenv('MYSQL_PASSWORD') );
define( 'DB_HOST', getenv('MYSQL_HOST') );
define( 'DB_CHARSET', 'utf8mb4' );
define( 'DB_COLLATE', '' );

// ** Authentication Unique Keys and Salts. ** //
// Generate your own at: https://api.wordpress.org/secret-key/1.1/salt/
define( 'AUTH_KEY',         'put-your-unique-phrase-here' );
define( 'SECURE_AUTH_KEY',  'put-your-unique-phrase-here' );
define( 'LOGGED_IN_KEY',    'put-your-unique-phrase-here' );
define( 'NONCE_KEY',        'put-your-unique-phrase-here' );
define( 'AUTH_SALT',        'put-your-unique-phrase-here' );
define( 'SECURE_AUTH_SALT', 'put-your-unique-phrase-here' );
define( 'LOGGED_IN_SALT',   'put-your-unique-phrase-here' );
define( 'NONCE_SALT',       'put-your-unique-phrase-here' );

// ** Database table prefix ** //
$table_prefix = 'wp_';

// ** Debugging mode ** //
define( 'WP_DEBUG', false );

/* That’s all, stop editing! Happy publishing. */

if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', __DIR__ . '/' );
}

require_once ABSPATH . 'wp-settings.php';
