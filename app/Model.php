<?php
namespace App;

class Model
{
    public String $table;
    public String $metaTable;
    public array $data;

    public function __construct($table, $data)
    {
        $this->table = $table;
        $this->metaTable = $table . 'meta';
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
        $result = [];

        $select = "SELECT * FROM $this->table WHERE `type` = '$type'";
        $posts = $db->fetchAll($select, __METHOD__);

        $ids = implode(",", array_map(function ($post) {
            return $post['id'];
        }, $posts));

        $select = "SELECT * FROM $this->metaTable WHERE `post_id` IN ('$ids')";
        $meta = $db->fetchAll($select, __METHOD__);

        foreach ($posts as $post) {
            $id = $post['id'];
            $result[$id] = $post;
            $result[$id]['meta'] = array_filter($meta, function ($prop) use ($id) {
                return $prop['post_id'] === $id ? $prop : null;
            });
        }

        return $result;
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
        $data = $db->fetchOne($select, __METHOD__);

        $select = "SELECT `key`, `value` FROM $this->metaTable WHERE `post_id` = '$id'";
        $meta = $db->fetchAll($select, __METHOD__);

        return [...$data, 'meta' => $meta];
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
