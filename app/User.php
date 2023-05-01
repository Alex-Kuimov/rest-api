<?php
namespace App;

class User
{
    private string $table;
    private string $metaTable;
    private ?int $id;
    private ?string $login;
    private ?string $pass;
    private ?array $props;
    private object $db;

    public function __construct($data)
    {
        $this->table = 'users';
        $this->metaTable = 'usersmeta';
        $this->id = $data['content']['id'] ?? null;
        $this->login = $data['content']['login'] ?? null;
        $this->pass = $data['content']['pass'] ?? null;
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
        $data = $this->id ? $this->getOne($this->db, $this->id) : $this->getAll($this->db);

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
    private function getAll($db): array
    {
        $result = [];

        $users = $this->query->get($this->table, ['id', 'login'], null);

        $ids = implode(",", array_map(function ($user) {
            return $user['id'];
        }, $users));

        $props = $this->query->get($this->metaTable, ['user_id', 'key', 'value'], ['user_id' => $ids], true);

        foreach ($users as $post) {
            $id = $post['id'];
            $result[$id] = $post;
            $meta = [];

            foreach ($props as $prop) {
                if ($prop['user_id'] === $id) {
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
        $user = $this->query->get($this->table, ['id', 'login'], ['id' => $id]);

        if (empty($user)) {
            return null;
        }

        $select = "SELECT `key`, `value` FROM $this->metaTable WHERE `user_id` = '$id'";
        $props = $db->fetchAll($select);

        $meta = [];

        if (empty($props)) {
            return [...$user, 'meta' => $meta];
        }

        foreach ($props as $prop) {
            $meta[$prop['key']] = $prop['value'];
        }

        return [...$user, 'meta' => $meta];
    }

    /**
     * Create
     *
     * @return bool
     */
    public function create(): bool
    {
        $insert = "INSERT INTO $this->table (`login`, `pass`) VALUES (:login, :pass)";

        $this->db->exec($insert, [
            ':login' => $this->login,
            ':pass' => md5($this->pass),
        ]);

        $id = intval($this->db->lastInsertId());

        // created without meta
        if (is_null($this->props)) {
            return true;
        }

        foreach ($this->props as $key => $value) {
            $insert = "INSERT INTO $this->metaTable (`user_id`, `key`, `value`) VALUES (:user_id, :key, :value)";

            $this->db->exec($insert, [
                ':user_id' => $id ,
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
        $update = "UPDATE $this->table SET `login` = (:login), `pass` = (:pass) WHERE `id` = '$this->id'";

        $this->db->exec($update, [
            ':login' => $this->login,
            ':pass' => md5($this->pass),
        ]);

        // updated without meta
        if (is_null($this->props)) {
            return true;
        }

        foreach ($this->props as $key => $value) {
            $select = "SELECT `key`, `value` FROM $this->metaTable WHERE `user_id` = '$this->id' AND `key` = '$key'";
            $meta = $this->db->fetchOne($select);

            if ($meta) {
                $update = "UPDATE $this->metaTable SET `value` = (:value) WHERE `user_id` = '$this->id' AND `key` = '$key'";

                $this->db->exec($update, [
                    ':value' => $value,
                ]);
            } else {
                $insert = "INSERT INTO $this->metaTable (`user_id`, `key`, `value`) VALUES (:user_id, :key, :value)";

                $this->db->exec($insert, [
                    ':user_id' => $this->id ,
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
        $query = "DELETE FROM $this->metaTable WHERE `user_id` = '$this->id'";
        $this->db->exec($query);

        $query = "DELETE FROM $this->table WHERE `id` = '$this->id'";
        $this->db->exec($query);

        return true;
    }
}
