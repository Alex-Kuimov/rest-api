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

    public function fetchAll(string $query, $_method, array $params = [])
    {
        $prepared = $this->getConnection()->prepare($query);

        $ret = $prepared->execute($params);

        if (!$ret) {
            $errorInfo = $prepared->errorInfo();
            trigger_error("{$errorInfo[0]}#{$errorInfo[1]}: " . $errorInfo[2]);
            return [];
        }

        return $prepared->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function fetchOne(string $query, $_method, array $params = [])
    {
        $prepared = $this->getConnection()->prepare($query);

        $ret = $prepared->execute($params);

        if (!$ret) {
            $errorInfo = $prepared->errorInfo();
            trigger_error("{$errorInfo[0]}#{$errorInfo[1]}: " . $errorInfo[2]);
            return [];
        }

        $data = $prepared->fetchAll(\PDO::FETCH_ASSOC);

        if (!$data) {
            return false;
        }

        return reset($data);
    }

    public function exec(string $query, $_method, array $params = [])
    {
        $t = microtime(1);
        $pdo = $this->getConnection();
        $prepared = $pdo->prepare($query);

        $ret = $prepared->execute($params);

        if (!$ret) {
            $errorInfo = $prepared->errorInfo();
            trigger_error("{$errorInfo[0]}#{$errorInfo[1]}: " . $errorInfo[2]);
            return -1;
        }

        return $prepared->rowCount();
    }

    public function lastInsertId()
    {
        return $this->getConnection()->lastInsertId();
    }
}
