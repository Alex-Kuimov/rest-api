<?php
namespace App;

class Model
{
    public string $table;
    public string $metaTable;
    public ?int $id;
    public ?string $type;
    public ?string $name;
    public ?array $props;

    public function __construct($table, $data)
    {
        $this->table = $table;
        $this->metaTable = $table . 'meta';
        $this->id = $data['content']['id'] ?? null;
        $this->type = $data['type'] ?? null;
        $this->name = $data['content']['name'] ?? null;
        $this->props = $data['content']['meta'] ?? null;
    }

    /**
     * Get data
     *
     * @return array|null
     */
    public function get(): ?array
    {
        $db = Db::getInstance();

        $data = $this->id ? $this->getOne($db, $this->id) : $this->getAll($db, $this->type);

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

        $select = "SELECT `post_id`, `key`, `value` FROM $this->metaTable WHERE `post_id` IN ($ids)";
        $props = $db->fetchAll($select, __METHOD__);

        foreach ($posts as $post) {
            $id = $post['id'];
            $result[$id] = $post;
            $meta = [];

            foreach ($props as $prop) {
                if ($prop['post_id'] === $id) {
                    $meta[$prop['key']] = $prop['value'];
                }
            }

            $result[$id]['meta'] = $meta;
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
        $props = $db->fetchAll($select, __METHOD__);

        $meta = [];

        if (empty($props)) {
            return [...$data, 'meta' => $meta];
        }
        
        foreach ($props as $prop) {
            $meta[$prop['key']] = $prop['value'];
        }

        return [...$data, 'meta' => $meta];
    }

    /**
     * Create
     *
     * @return string
     */
    public function create(): string
    {
        $db = Db::getInstance();
        $insert = "INSERT INTO $this->table (`name`, `type`) VALUES (:name, :type)";

        $db->exec($insert, __METHOD__, [
            ':name' => $this->name,
            ':type' => $this->type,
        ]);

        $id = intval($db->lastInsertId());

        if (is_null($this->props)) {
            return 'created without meta';
        }

        foreach ($this->props as $key => $value) {
            $insert = "INSERT INTO $this->metaTable (`post_id`, `key`, `value`) VALUES (:post_id, :key, :value)";

            $db->exec($insert, __METHOD__, [
                ':post_id' => $id ,
                ':key' => $key,
                ':value' => $value,
            ]);
        }

        return 'created with meta';
    }

    /**
     * Update
     *
     * @return string
     */
    public function update(): string
    {
        $db = Db::getInstance();

        $update = "UPDATE $this->table SET `name` = (:name) WHERE `id` = '$this->id'";

        $db->exec($update, __METHOD__, [
            ':name' => $this->name,
        ]);

        if (is_null($this->props)) {
            return 'updated without meta';
        }

        foreach ($this->props as $key => $value) {
            $select = "SELECT `key`, `value` FROM $this->metaTable WHERE `post_id` = '$this->id' AND `key` = '$key'";
            $meta = $db->fetchOne($select, __METHOD__);

            if ($meta) {
                $update = "UPDATE $this->metaTable SET `value` = (:value) WHERE `post_id` = '$this->id' AND `key` = '$key'";

                $db->exec($update, __METHOD__, [
                    ':value' => $value,
                ]);
            } else {
                $insert = "INSERT INTO $this->metaTable (`post_id`, `key`, `value`) VALUES (:post_id, :key, :value)";

                $db->exec($insert, __METHOD__, [
                    ':post_id' => $this->id ,
                    ':key' => $key,
                    ':value' => $value,
                ]);
            }
        }

        return 'updated with meta';
    }

    /**
     * Delete
     *
     * @return int
     */
    public function delete(): int
    {
        $db = Db::getInstance();
        $query = "DELETE FROM $this->table WHERE `id` = '$this->id'";

        return $db->exec($query, __METHOD__);
    }
}
