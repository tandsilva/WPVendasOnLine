<?php
/**
 * Custom database drop-in para forçar conexão TCP
 * Railway precisa de conexão TCP, não Unix socket
 */

// Força o uso de conexão TCP definindo o host explicitamente
if (!defined('DB_HOST')) {
    return;
}

// Remove qualquer referência a socket Unix
$GLOBALS['wpdb_force_tcp'] = true;

// Carrega o wpdb padrão
require_once(ABSPATH . WPINC . '/wp-db.php');

// Sobrescreve a classe wpdb para forçar TCP
class wpdb_railway extends wpdb {
    public function db_connect($allow_bail = true) {
        $this->is_mysql = true;

        // Força flags de conexão TCP/IP
        $client_flags = MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT;
        
        // Separa host:porta
        $host = DB_HOST;
        $port = null;
        $socket = null;
        
        if (strpos($host, ':') !== false) {
            list($host, $port_or_socket) = explode(':', $host, 2);
            if (is_numeric($port_or_socket)) {
                $port = (int) $port_or_socket;
            }
        }
        
        if ($port === null) {
            $port = 3306;
        }
        
        // FORÇA socket = null para usar TCP
        $socket = null;
        
        // Tenta conectar
        $this->dbh = mysqli_init();
        
        mysqli_options($this->dbh, MYSQLI_OPT_CONNECT_TIMEOUT, 10);
        
        @mysqli_real_connect(
            $this->dbh,
            $host,
            $this->dbuser,
            $this->dbpassword,
            null,
            $port,
            $socket,
            $client_flags
        );
        
        if ($this->dbh->connect_errno) {
            $this->bail(sprintf(
                "<h1>Error establishing database connection</h1>
                <p>Error: %s</p>
                <p>Host: %s:%d</p>",
                $this->dbh->connect_error,
                $host,
                $port
            ), 'db_connect_fail');
            
            return false;
        }
        
        $this->select($this->dbname, $this->dbh);
        $this->set_charset($this->dbh);
        
        $this->ready = true;
        $this->set_sql_mode();
        $this->init_charset();
        
        return true;
    }
}

// Substitui $wpdb global
$wpdb = new wpdb_railway(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
