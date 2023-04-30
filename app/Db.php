<?php
namespace App;

class Db
{
    private $pdo;
    private static $instance;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function getConnection()
    {
        $host = DB_HOST;
        $dbName = DB_NAME;
        $dbUser = DB_USER;
        $dbPassword = DB_PASSWORD;

        if (!$this->pdo) {
            $this->pdo = new \PDO(
                "mysql:host=$host;dbname=$dbName",
                $dbUser,
                $dbPassword,
                [
                    \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"
                ]
            );
        }

        return $this->pdo;
    }

    public function fetchAll(string $query, array $params = [])
    {
        $prepared = $this->getConnection()->prepare($query);
        $prepared->execute($params);
        return $prepared->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function fetchOne(string $query, $_method, array $params = [])
    {
        $prepared = $this->getConnection()->prepare($query);
        $prepared->execute($params);
        $data = $prepared->fetchAll(\PDO::FETCH_ASSOC);

        if (!$data) {
            return false;
        }

        return reset($data);
    }

    public function exec(string $query, array $params = [])
    {
        $pdo = $this->getConnection();
        $prepared = $pdo->prepare($query);
        $prepared->execute($params);

        return $prepared->rowCount();
    }

    public function lastInsertId()
    {
        return $this->getConnection()->lastInsertId();
    }
}
