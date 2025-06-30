<?php
// config/DatabaseConnection.php
require_once __DIR__ . '/config.php';

class database {
    private static $pdo = null;
    private static $mysqli = null;
    
    public static function getConnection() {
        // Try PDO first
        if (!self::$pdo) {
            try {
                self::$pdo = new PDO(
                    "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                    DB_USER,
                    DB_PASS,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false
                    ]
                );
                
                // Test the connection with a simple query
                self::$pdo->query('SELECT 1');
                return self::$pdo;
                
            } catch (PDOException $e) {
                // Log PDO error
                error_log("PDO connection failed: " . $e->getMessage());
                
                // Try MySQLi as fallback
                try {
                    self::$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
                    
                    // Check for MySQLi connection error
                    if (self::$mysqli->connect_errno) {
                        throw new Exception("MySQLi connection error: " . self::$mysqli->connect_error);
                    }
                    
                    // Test the connection
                    self::$mysqli->query('SELECT 1');
                    
                    // Successfully connected via MySQLi, return a PDO-like wrapper
                    return new MySQLiWrapper(self::$mysqli);
                    
                } catch (Exception $e) {
                    // Both connection methods failed
                    error_log("All database connections failed: " . $e->getMessage());
                    
                    if (defined('DEBUG_MODE') && DEBUG_MODE) {
                        echo "<div style='color:red; background:#fee; padding:10px; margin:10px; border:1px solid #f00;'>";
                        echo "<strong>Database Connection Error:</strong><br>";
                        echo "Failed to connect to MySQL: " . $e->getMessage();
                        echo "<br><br><strong>Check your database settings in config/config.php:</strong>";
                        echo "<ul>";
                        echo "<li>Host: " . DB_HOST . "</li>";
                        echo "<li>Database: " . DB_NAME . "</li>";
                        echo "<li>User: " . DB_USER . "</li>";
                        echo "</ul>";
                        echo "</div>";
                    }
                    
                    // Return null when all connection attempts fail
                    return null;
                }
            }
        }
        return self::$pdo;
    }
}

/**
 * Simple wrapper class to provide PDO-like interface for MySQLi
 */
class MySQLiWrapper {
    private $mysqli;
    
    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
    }
    
    public function prepare($query) {
        return new MySQLiStatementWrapper($this->mysqli, $query);
    }
    
    public function query($query) {
        $result = $this->mysqli->query($query);
        if ($result === false) {
            throw new Exception("Query failed: " . $this->mysqli->error);
        }
        return new MySQLiResultWrapper($result);
    }
    
    public function lastInsertId() {
        return $this->mysqli->insert_id;
    }
    
    public function beginTransaction() {
        return $this->mysqli->begin_transaction();
    }
    
    public function commit() {
        return $this->mysqli->commit();
    }
    
    public function rollBack() {
        return $this->mysqli->rollback();
    }
}

/**
 * Statement wrapper for MySQLi prepared statements
 */
class MySQLiStatementWrapper {
    private $mysqli;
    private $query;
    private $stmt;
    private $params = [];
    
    public function __construct($mysqli, $query) {
        $this->mysqli = $mysqli;
        $this->query = $query;
        $this->stmt = $mysqli->prepare($query);
        
        if ($this->stmt === false) {
            throw new Exception("Failed to prepare statement: " . $mysqli->error);
        }
    }
    
    public function bindParam($param, &$variable, $type = null) {
        $this->params[$param] = &$variable;
        return true;
    }
    
    public function bindValue($param, $value, $type = null) {
        $this->params[$param] = $value;
        return true;
    }
    
    public function execute($params = null) {
        if ($params !== null) {
            $this->params = $params;
        }
        
        // Build types and values arrays for bind_param
        $types = '';
        $values = [];
        
        foreach ($this->params as $param => $value) {
            // Determine type
            if (is_int($value)) {
                $types .= 'i';
            } elseif (is_float($value)) {
                $types .= 'd';
            } elseif (is_string($value)) {
                $types .= 's';
            } else {
                $types .= 'b'; // blob
            }
            
            $values[] = $value;
        }
        
        // Bind parameters if any
        if (!empty($values)) {
            $bindParams = [$types];
            foreach ($values as &$val) {
                $bindParams[] = &$val;
            }
            
            call_user_func_array([$this->stmt, 'bind_param'], $bindParams);
        }
        
        // Execute the statement
        $result = $this->stmt->execute();
        
        if ($result === false) {
            throw new Exception("Failed to execute statement: " . $this->stmt->error);
        }
        
        return $result;
    }
    
    public function fetch($style = null) {
        $result = $this->stmt->get_result();
        if ($result === false) {
            return false;
        }
        return $result->fetch_assoc();
    }
    
    public function fetchAll($style = null) {
        $result = $this->stmt->get_result();
        if ($result === false) {
            return [];
        }
        
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        
        return $rows;
    }
}

/**
 * Result wrapper for MySQLi results
 */
class MySQLiResultWrapper {
    private $result;
    
    public function __construct($result) {
        $this->result = $result;
    }
    
    public function fetch($style = null) {
        return $this->result->fetch_assoc();
    }
    
    public function fetchAll($style = null) {
        $rows = [];
        while ($row = $this->result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }
}