<?php
namespace App;

class Model
{
    public String $table;
    public array $data;

    public function __construct($table, $data)
    {
        $this->table = $table;
        $this->data = $data;
    }

    /**
     * Get list
     *
     * @return array|null
     */
    public function get(): ?array
    {
        $type = $this->data['type'];

        $db = Db::getInstance();
        $select = "SELECT * FROM $this->table WHERE `type` = '$type'";
        $data = $db->fetchAll($select, __METHOD__);

        if (!$data) {
            return null;
        }

        return $data;
    }

    /**
     * Create
     *
     * @return int
     */
    public function create(): int
    {
        $name = $this->data['content']['name'];
        $type = $this->data['type'];

        $db = Db::getInstance();
        $insert = "INSERT INTO $this->table (`name`, `type`) VALUES (:name, :type)";

        $db->exec($insert, __METHOD__, [
            ':name' => $name,
            ':type' => $type,
        ]);

        return intval($db->lastInsertId());
    }

    /**
     * Update
     *
     * @return int
     */
    public function update(): int
    {
        $id = $this->data['content']['id'];
        $name = $this->data['content']['name'];

        $db = Db::getInstance();
        $update = "UPDATE $this->table SET `name` = (:name) WHERE `id` = '$id'";

        return $db->exec($update, __METHOD__, [
            ':name' => $name,
        ]);
    }

    /**
     * Delete
     *
     * @return int
     */
    public function delete(): int
    {
        $id  = $this->data['content']['id'];

        $db = Db::getInstance();
        $query = "DELETE FROM $this->table WHERE `id` = '$id'";

        return $db->exec($query, __METHOD__);
    }
}
