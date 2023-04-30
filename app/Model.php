<?php
namespace App;

class Model
{
    private string $table;
    private string $metaTable;
    private ?int $id;
    private ?string $type;
    private ?string $name;
    private ?array $props;
    private object $db;

    public function __construct($table, $data)
    {
        $this->table = $table;
        $this->metaTable = $table . 'meta';
        $this->id = $data['content']['id'] ?? null;
        $this->type = $data['type'] ?? null;
        $this->name = $data['content']['name'] ?? null;
        $this->props = $data['content']['meta'] ?? null;
        $this->db = Db::getInstance();
    }

    /**
     * Get
     *
     * @return array|null
     */
    public function get(): ?array
    {
        $data = $this->id ? $this->getOne($this->db, $this->id) : $this->getAll($this->db, $this->type);

        if (!$data) {
            return null;
        }

        return $data;
    }

    /**
     * Get list
     *
     * @param $db
     * @param $type
     * @return array
     */
    private function getAll($db, $type): array
    {
        $result = [];

        $select = "SELECT * FROM $this->table WHERE `type` = '$type'";
        $posts = $db->fetchAll($select);

        $ids = implode(",", array_map(function ($post) {
            return $post['id'];
        }, $posts));

        $select = "SELECT `post_id`, `key`, `value` FROM $this->metaTable WHERE `post_id` IN ($ids)";
        $props = $db->fetchAll($select);

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
     * @return array|null
     */
    private function getOne($db, $id): ?array
    {
        $select = "SELECT * FROM $this->table WHERE `id` = '$id'";
        $data = $db->fetchOne($select);

        if (empty($data)) {
            return null;
        }

        $select = "SELECT `key`, `value` FROM $this->metaTable WHERE `post_id` = '$id'";
        $props = $db->fetchAll($select);

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
     * @return bool
     */
    public function create(): bool
    {
        $insert = "INSERT INTO $this->table (`name`, `type`) VALUES (:name, :type)";

        $this->db->exec($insert, [
            ':name' => $this->name,
            ':type' => $this->type,
        ]);

        $id = intval($this->db->lastInsertId());

        // created without meta
        if (is_null($this->props)) {
            return true;
        }

        foreach ($this->props as $key => $value) {
            $insert = "INSERT INTO $this->metaTable (`post_id`, `key`, `value`) VALUES (:post_id, :key, :value)";

            $this->db->exec($insert, [
                ':post_id' => $id ,
                ':key' => $key,
                ':value' => $value,
            ]);
        }

        // created with meta
        return true;
    }

    /**
     * Update
     *
     * @return string
     */
    public function update(): string
    {
        $update = "UPDATE $this->table SET `name` = (:name) WHERE `id` = '$this->id'";

        $this->db->exec($update, [
            ':name' => $this->name,
        ]);

        // updated without meta
        if (is_null($this->props)) {
            return true;
        }

        foreach ($this->props as $key => $value) {
            $select = "SELECT `key`, `value` FROM $this->metaTable WHERE `post_id` = '$this->id' AND `key` = '$key'";
            $meta = $this->db->fetchOne($select);

            if ($meta) {
                $update = "UPDATE $this->metaTable SET `value` = (:value) WHERE `post_id` = '$this->id' AND `key` = '$key'";

                $this->db->exec($update, [
                    ':value' => $value,
                ]);
            } else {
                $insert = "INSERT INTO $this->metaTable (`post_id`, `key`, `value`) VALUES (:post_id, :key, :value)";

                $this->db->exec($insert, [
                    ':post_id' => $this->id ,
                    ':key' => $key,
                    ':value' => $value,
                ]);
            }
        }

        // updated with meta
        return true;
    }

    /**
     * Delete
     *
     * @return bool
     */
    public function delete(): bool
    {
        $query = "DELETE FROM $this->metaTable WHERE `post_id` = '$this->id'";
        $this->db->exec($query);

        $query = "DELETE FROM $this->table WHERE `id` = '$this->id'";
        $this->db->exec($query);

        return true;
    }
}
