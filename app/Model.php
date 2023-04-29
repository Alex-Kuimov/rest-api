<?php
namespace App;

class Model
{
    public String $table;

    public function __construct($table)
    {
        $this->table = $table;
    }

    /**
     * Get list
     *
     * @param $data
     * @return array|null
     */
    public function get($data): ?array
    {
        $type = $data['type'];

        $db = Db::getInstance();
        $select = "SELECT * FROM `$this->table` WHERE type = '$type'";
        $data = $db->fetchAll($select, __METHOD__);

        if (!$data) {
            return null;
        }

        return $data;
    }

    /**
     * Create
     *
     * @param $data
     * @return int
     */
    public function create($data): int
    {
        $name = $data['content']['name'];
        $type = $data['type'];

        $db = Db::getInstance();
        $insert = "INSERT INTO `$this->table` (`name`, `type`) VALUES (:name, :type)";

        $db->exec($insert, __METHOD__, [
            ':name' => $name,
            ':type' => $type,
        ]);

        return intval($db->lastInsertId());
    }

    /**
     * Update
     *
     * @param $data
     * @return int
     */
    public function update($data): int
    {
        $id = $data['content']['id'];
        $name = $data['content']['name'];

        $db = Db::getInstance();
        $update = "UPDATE `$this->table`  SET `name` = (:name) WHERE id = '$id'";

        return $db->exec($update, __METHOD__, [
            ':name' => $name,
        ]);
    }

    /**
     * Delete
     *
     * @param $data
     * @return int
     */
    public function delete($data): int
    {
        $id  = $data['content']['id'];

        $db = Db::getInstance();
        $query = "DELETE FROM `$this->table` WHERE id = '$id'";

        return $db->exec($query, __METHOD__);
    }
}
