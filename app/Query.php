<?php
namespace App;

class Query
{
    private object $db;

    public function __construct()
    {
        $this->db = Db::getInstance();
    }

    public function get($table, $select, $where, $in = false):array
    {
        $queryString = "SELECT ";

        if ($select === 'all') {
            $queryString .= "* ";
        } else {
            foreach ($select as $item) {
                $queryString .= "`$item`, ";
            }

            $queryString = substr($queryString, 0, strlen($queryString) - 2) . " ";
        }

        $queryString .= "FROM $table ";

        if (!is_null($where)) {
            $queryString .= 'WHERE ';
            if (!$in) {
                foreach ($where as $key => $value) {
                    $queryString .= "`$key` = '$value' AND ";
                }
                $queryString = substr($queryString, 0, strlen($queryString) - 4) . " ";
            } else {
                foreach ($where as $key => $value) {
                    $queryString .= "`$key` IN ($value)";
                }
            }
        }

        return $this->db->fetchAll($queryString);
    }

    public function update($table, $props, $where): bool
    {
        $values = [];
        $queryString = "UPDATE $table SET ";

        foreach ($props as $key => $value) {
            $queryString .= "`$key` = (:$key)";
            $values[':'.$key] = $value;
        }

        $queryString .= 'WHERE ';

        foreach ($where as $key => $value) {
            $queryString .= "`$key` = '$value' AND ";
        }
        $queryString = substr($queryString, 0, strlen($queryString) - 4) . " ";

        $this->db->exec($queryString, $values);

        return true;
    }

    public function insert($table, $props): int
    {
        $values = [];
        $queryString = "INSERT INTO $table (";

        foreach ($props as $key => $value) {
            $queryString .= "`$key`, ";
        }
        $queryString = substr($queryString, 0, strlen($queryString) - 2) . ") ";

        $queryString .= "VALUES (";
        foreach ($props as $key => $value) {
            $queryString .= ":$key, ";
            $values[':'.$key] = $value;
        }
        $queryString = substr($queryString, 0, strlen($queryString) - 2) . ")";


        $this->db->exec($queryString, $values);

        return intval($this->db->lastInsertId());
    }

    public function delete($table, $where):bool
    {
        $queryString = "DELETE FROM $table ";

        $queryString .= "WHERE ";

        foreach ($where as $key => $value) {
            $queryString .= "`$key` = '$value' AND ";
        }
        $queryString = substr($queryString, 0, strlen($queryString) - 4) . " ";
        $this->db->exec($queryString);

        return true;
    }
}
