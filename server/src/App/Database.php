<?php

declare(strict_types=1);

namespace App;

use PDO;
use PDOException;

class Database
{
    private static ?Database $instance = null;
    private ?PDO $conn = null;

    public function __construct(
        private string $host,
        private string $username,
        private string $password,
        private string $dbname
    ) {
    }

    public static function getInstance(
        string $host,
        string $username,
        string $password,
        string $dbname
    ): Database {
        if (self::$instance === null) {
            self::$instance = new self($host, $username, $password, $dbname);
        }

        return self::$instance;
    }

    public function getConnection(): PDO
    {
        if ($this->conn === null) {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8";
            try {
                $this->conn = new PDO($dsn, $this->username, $this->password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $ex) {
                throw new \RuntimeException("Database connection failed: " . $ex->getMessage());
            }
        }

        return $this->conn;
    }
}

?>