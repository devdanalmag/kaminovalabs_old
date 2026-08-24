<?php
/**
 * Database Configuration
 * Kaminova Labs Admin Panel
 */

require_once __DIR__ . '/env.php';

class Database
{
    private $host;
    private $db_name;
    private $username;
    private $password;
    private static $conn = null;

    public function getConnection()
    {
        if (self::$conn !== null) {
            return self::$conn;
        }

        try {
            // SECURITY: Read from env with NO defaults - credentials must come from .env only
            $this->host = env('DB_HOST');
            $this->db_name = env('DB_NAME');
            $this->username = env('DB_USER');
            $this->password = env('DB_PASS');

            // Validate all required connection parameters are set
            if (!$this->host || !$this->db_name || !$this->username || $this->password === null) {
                throw new Exception('Missing required database configuration. Please check your .env file.');
            }
            self::$conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                ]
            );
        } catch (PDOException $exception) {
            error_log("Database connection error: " . $exception->getMessage());
            throw new Exception("Database connection failed: " . $exception->getMessage());
        }

        return self::$conn;
    }
}

// Helper functions for database operations
function getDB()
{
    $database = new Database();
    return $database->getConnection();
}

function executeQuery($sql, $params = [])
{
    try {
        $db = getDB();
        if (!$db) {
            throw new Exception("Database connection failed");
        }
        $stmt = $db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . implode(", ", $db->errorInfo()));
        }
        $result = $stmt->execute($params);
        if (!$result) {
            throw new Exception("Failed to execute query: " . implode(", ", $stmt->errorInfo()));
        }
        return $stmt;
    } catch (PDOException $e) {
        throw new Exception("Database error: " . $e->getMessage());
    }
}

function fetchOne($sql, $params = [])
{
    $stmt = executeQuery($sql, $params);
    return $stmt->fetch();
}

function fetchAll($sql, $params = [])
{
    $stmt = executeQuery($sql, $params);
    return $stmt->fetchAll();
}

function insertRecord($table, $data)
{
    $columns = implode(',', array_keys($data));
    $placeholders = ':' . implode(', :', array_keys($data));

    $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
    $stmt = executeQuery($sql, $data);

    return getDB()->lastInsertId();
}

function updateRecord($table, $data, $where, $whereParams = [])
{
    $setParts = [];
    foreach (array_keys($data) as $key) {
        $setParts[] = "{$key} = :{$key}";
    }
    $setClause = implode(', ', $setParts);

    $sql = "UPDATE {$table} SET {$setClause} WHERE {$where}";
    $params = array_merge($data, $whereParams);

    $stmt = executeQuery($sql, $params);
    return $stmt->rowCount(); // Return number of affected rows
}

function deleteRecord($table, $where, $params = [])
{
    $sql = "DELETE FROM {$table} WHERE {$where}";
    return executeQuery($sql, $params);
}
?>