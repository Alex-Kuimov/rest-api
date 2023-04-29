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
     * Get data
     *
     * @return array|null
     */
    public function get(): ?array
    {
        $type = $this->data['type'];
        $id = $this->data['content']['id'] ?? null;

        $db = Db::getInstance();

        $data = $id ? $this->getOne($db, $id) : $this->getAll($db, $type);

        if (!$data) {
            return null;
        }

        return $data;
    }

    /**
     * Get all
     *
     * @param $db
     * @param $type
     * @return mixed
     */
    private function getAll($db, $type): mixed
    {
        $select = "SELECT * FROM $this->table WHERE `type` = '$type'";
        return $db->fetchAll($select, __METHOD__);
    }

    /**
     * Get one
     *
     * @param $db
     * @param $id
     * @return mixed
     */
    private function getOne($db, $id): mixed
    {
        $select = "SELECT * FROM $this->table WHERE `id` = '$id'";
        return $db->fetchOne($select, __METHOD__);
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
