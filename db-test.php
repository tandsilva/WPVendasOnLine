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

echo "<h2>🔍 Diagnóstico Completo MySQL Railway</h2>";
echo "<pre>";

// ===== INFORMAÇÕES DO SISTEMA =====
echo "==========================================\n";
echo "📋 INFORMAÇÕES DO SISTEMA\n";
echo "==========================================\n";
echo "PHP Version: " . phpversion() . "\n";
echo "Sistema: " . php_uname('s') . " " . php_uname('r') . "\n";
echo "MySQLi Extension: " . (extension_loaded('mysqli') ? '✅ INSTALADO' : '❌ NÃO INSTALADO') . "\n";
echo "PDO MySQL: " . (extension_loaded('pdo_mysql') ? '✅ INSTALADO' : '❌ NÃO INSTALADO') . "\n";
echo "Timestamp: " . date('Y-m-d H:i:s') . "\n\n";

// ===== VARIÁVEIS DETECTADAS =====
echo "==========================================\n";
echo "🔐 VARIÁVEIS DE AMBIENTE DETECTADAS\n";
echo "==========================================\n";
echo "Host: " . ($host ?: '❌ VAZIO') . "\n";
echo "Port: " . ($port ?: '❌ VAZIO') . "\n";
echo "User: " . ($user ?: '❌ VAZIO') . "\n";
echo "Pass: " . (empty($pass) ? '❌ VAZIA!' : '✅ ****' . substr($pass, -4) . ' (len: ' . strlen($pass) . ')') . "\n";
echo "Database: " . ($db ?: '❌ VAZIO') . "\n\n";

// ===== TODAS AS VARS MYSQL =====
echo "==========================================\n";
echo "📦 TODAS AS VARIÁVEIS MYSQL* DISPONÍVEIS\n";
echo "==========================================\n";
$found_vars = [];
foreach (array_merge($_ENV, $_SERVER) as $key => $value) {
    if (stripos($key, 'MYSQL') !== false || stripos($key, 'WORDPRESS_DB') !== false) {
        if (!isset($found_vars[$key])) {
            $display_value = (stripos($key, 'PASS') !== false) ? '****' . substr($value, -4) : $value;
            echo "$key = $display_value\n";
            $found_vars[$key] = true;
        }
    }
}
if (empty($found_vars)) {
    echo "⚠️  NENHUMA variável MYSQL* encontrada!\n";
}
echo "\n";

// ===== TESTE DE CONECTIVIDADE =====
echo "==========================================\n";
echo "🌐 TESTE DE CONECTIVIDADE\n";
echo "==========================================\n";
echo "Testando acesso à porta $port em $host...\n";

$socket_start = microtime(true);
$socket = @fsockopen($host, $port, $errno, $errstr, 5);
$socket_time = round((microtime(true) - $socket_start) * 1000, 2);

if ($socket) {
    echo "✅ Porta ACESSÍVEL (em {$socket_time}ms)\n";
    fclose($socket);
} else {
    echo "❌ Porta INACESSÍVEL (após {$socket_time}ms)\n";
    echo "   Socket Error #$errno: $errstr\n";
    echo "\n⚠️  O servidor MySQL não está acessível neste host:porta!\n";
    echo "   Verifique se as variáveis MYSQLHOST e MYSQLPORT estão corretas.\n";
    die("</pre>");
}
echo "\n";

// ===== CONEXÃO MYSQL =====
echo "==========================================\n";
echo "🔌 TENTANDO CONECTAR AO MYSQL\n";
echo "==========================================\n";
echo "Connection string: $user@$host:$port\n";

// Configura timeouts
ini_set('default_socket_timeout', 10);
ini_set('mysql.connect_timeout', 10);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$connect_start = microtime(true);

try {
    $mysqli = @new mysqli($host, $user, $pass, '', $port);
    $connect_time = round((microtime(true) - $connect_start) * 1000, 2);
    
    if ($mysqli->connect_errno) {
        echo "❌ FALHA NA CONEXÃO (após {$connect_time}ms)\n";
        echo "   Erro #" . $mysqli->connect_errno . ": " . $mysqli->connect_error . "\n\n";
        
        // Análise do erro
        $error_code = $mysqli->connect_errno;
        echo "📖 ANÁLISE DO ERRO:\n";
        
        if ($error_code == 1045) {
            echo "   ⚠️  Usuário ou senha incorretos!\n";
            echo "   → Verifique MYSQLUSER e MYSQLPASSWORD no Railway\n";
        } elseif ($error_code == 2002) {
            echo "   ⚠️  Servidor não está respondendo!\n";
            echo "   → Verifique se o MySQL está rodando no Railway\n";
        } elseif ($error_code == 2003) {
            echo "   ⚠️  Não foi possível conectar ao servidor!\n";
            echo "   → Verifique MYSQLHOST e MYSQLPORT\n";
        } elseif ($error_code == 2006) {
            echo "   ⚠️  MySQL server has gone away!\n";
            echo "   → Servidor pode ter reiniciado ou timeout de conexão\n";
        } else {
            echo "   → Erro desconhecido. Verifique logs do MySQL no Railway\n";
        }
        
        die("</pre>");
    }
    
    echo "✅ CONECTADO COM SUCESSO! (em {$connect_time}ms)\n\n";
    
    // ===== INFO DO SERVIDOR =====
    echo "==========================================\n";
    echo "ℹ️  INFORMAÇÕES DO SERVIDOR MYSQL\n";
    echo "==========================================\n";
    echo "Server Version: " . $mysqli->server_info . "\n";
    echo "Protocol Version: " . $mysqli->protocol_version . "\n";
    echo "Host Info: " . $mysqli->host_info . "\n";
    echo "Thread ID: " . $mysqli->thread_id . "\n";
    echo "Character Set: " . $mysqli->character_set_name() . "\n";
    
    // Status
    $status = $mysqli->query("SHOW STATUS LIKE 'Uptime'");
    if ($row = $status->fetch_assoc()) {
        $uptime_seconds = $row['Value'];
        $hours = floor($uptime_seconds / 3600);
        $minutes = floor(($uptime_seconds % 3600) / 60);
        echo "Server Uptime: {$hours}h {$minutes}m\n";
    }
    echo "\n";
    
    // ===== VARIÁVEIS IMPORTANTES =====
    echo "==========================================\n";
    echo "⚙️  CONFIGURAÇÕES DO SERVIDOR\n";
    echo "==========================================\n";
    $vars = $mysqli->query("SHOW VARIABLES WHERE Variable_name IN ('max_connections', 'wait_timeout', 'interactive_timeout', 'max_allowed_packet', 'version')");
    while ($row = $vars->fetch_assoc()) {
        echo str_pad($row['Variable_name'], 25) . ": " . $row['Value'] . "\n";
    }
    echo "\n";
    
    // ===== LISTA BANCOS =====
    echo "==========================================\n";
    echo "📦 BANCOS DE DADOS DISPONÍVEIS\n";
    echo "==========================================\n";
    $result = $mysqli->query("SHOW DATABASES");
    $databases = [];
    while ($row = $result->fetch_row()) {
        echo "  • {$row[0]}\n";
        $databases[] = $row[0];
    }
    echo "\n";
    
    // ===== VERIFICA BANCO ESPECÍFICO =====
    $db_exists = in_array($db, $databases);
    
    echo "==========================================\n";
    echo "🎯 VERIFICANDO BANCO '$db'\n";
    echo "==========================================\n";
    
    if (!$db_exists) {
        echo "⚠️  Banco '$db' NÃO EXISTE!\n";
        echo "Tentando criar banco '$db'...\n";
        
        if ($mysqli->query("CREATE DATABASE `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")) {
            echo "✅ Banco '$db' criado com sucesso!\n";
            $db_exists = true;
        } else {
            echo "❌ Erro ao criar banco: {$mysqli->error}\n";
            die("</pre>");
        }
    } else {
        echo "✅ Banco '$db' existe!\n";
    }
    
    // Seleciona o banco
    if ($mysqli->select_db($db)) {
        echo "✅ Banco '$db' selecionado com sucesso!\n\n";
        
        // ===== LISTA TABELAS =====
        echo "==========================================\n";
        echo "📋 TABELAS NO BANCO '$db'\n";
        echo "==========================================\n";
        $tables = $mysqli->query("SHOW TABLES");
        $table_count = $tables->num_rows;
        
        if ($table_count > 0) {
            echo "Total de tabelas: $table_count\n\n";
            while ($row = $tables->fetch_array()) {
                echo "  • {$row[0]}\n";
            }
        } else {
            echo "📭 Banco vazio (pronto para instalação do WordPress)\n";
        }
        echo "\n";
        
        // ===== TESTE DE PERMISSÕES =====
        echo "==========================================\n";
        echo "🔐 TESTE DE PERMISSÕES\n";
        echo "==========================================\n";
        $test_table = 'wp_test_' . time();
        
        try {
            // CREATE
            if ($mysqli->query("CREATE TABLE `$test_table` (id INT AUTO_INCREMENT PRIMARY KEY, test VARCHAR(50))")) {
                echo "✅ CREATE TABLE: OK\n";
                
                // INSERT
                if ($mysqli->query("INSERT INTO `$test_table` (test) VALUES ('test123')")) {
                    echo "✅ INSERT: OK\n";
                    
                    // SELECT
                    if ($result = $mysqli->query("SELECT * FROM `$test_table`")) {
                        echo "✅ SELECT: OK (" . $result->num_rows . " row)\n";
                        $result->free();
                        
                        // UPDATE
                        if ($mysqli->query("UPDATE `$test_table` SET test='updated'")) {
                            echo "✅ UPDATE: OK\n";
                            
                            // DELETE
                            if ($mysqli->query("DELETE FROM `$test_table`")) {
                                echo "✅ DELETE: OK\n";
                            }
                        }
                    }
                }
                
                // DROP
                if ($mysqli->query("DROP TABLE `$test_table`")) {
                    echo "✅ DROP TABLE: OK\n";
                }
                
                echo "\n🎉 Todas as permissões necessárias estão OK!\n";
            }
        } catch (Exception $e) {
            echo "❌ Erro no teste de permissões: " . $e->getMessage() . "\n";
        }
        
    } else {
        echo "❌ Erro ao selecionar banco '$db': {$mysqli->error}\n";
    }
    
    $mysqli->close();
    
    echo "\n==========================================\n";
    echo "✅ DIAGNÓSTICO CONCLUÍDO!\n";
    echo "==========================================\n";
    echo "Status: Tudo pronto para o WordPress! 🚀\n";
    
} catch (mysqli_sql_exception $e) {
    echo "❌ EXCEÇÃO MYSQLI:\n";
    echo "   Código: " . $e->getCode() . "\n";
    echo "   Mensagem: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "❌ EXCEÇÃO GERAL:\n";
    echo "   Mensagem: " . $e->getMessage() . "\n";
}

echo "</pre>";
