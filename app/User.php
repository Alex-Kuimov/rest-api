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
    private object $query;

    public function __construct($data)
    {
        $this->table = 'users';
        $this->metaTable = $this->table . 'meta';

        $this->id = $data['content']['id'] ?? null;
        $this->login = $data['content']['login'] ?? null;
        $this->pass = $data['content']['pass'] ?? null;
        $this->props = $data['content']['meta'] ?? null;

        $this->query = new Query;
    }

    /**
     * Get
     *
     * @return array|null
     */
    public function get(): ?array
    {
        $data = $this->id ? $this->getOne($this->id) : $this->getAll();

        if (!$data) {
            return null;
        }

        return $data;
    }

    /**
     * Get list
     *
     * @return array
     */
    private function getAll(): array
    {
        $result = [];

        $users = $this->query->get($this->table, ['id', 'login'], null);

        $ids = implode(",", array_map(function ($user) {
            return $user['id'];
        }, $users));

        $props = $this->query->get($this->metaTable, ['user_id', 'key', 'value'], ['user_id' => $ids], true);

        foreach ($users as $user) {
            $id = $user['id'];
            $result[$id] = $user;
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
     * @param $id
     * @return array|null
     */
    private function getOne($id): ?array
    {
        $user = $this->query->get($this->table, ['id', 'login'], ['id' => $id]);

        if (empty($user)) {
            return null;
        }

        $props = $this->query->get($this->metaTable, ['key', 'value'], ['user_id' => $id]);

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
     * @return int
     */
    public function create(): int
    {
        $id = $this->query->insert($this->table, [
            'login' => $this->login,
            'pass' => md5($this->pass),
        ]);

        // created without meta
        if (is_null($this->props)) {
            return $id;
        }

        foreach ($this->props as $key => $value) {
            $this->query->insert($this->metaTable, [
                'user_id' => $id ,
                'key' => $key,
                'value' => $value,
            ]);
        }

        // created with meta
        return $id;
    }

    /**
     * Update
     *
     * @return bool
     */
    public function update(): bool
    {
        if ($this->login) {
            $this->query->update($this->table, ['login' => $this->login], ['id' => $this->id]);
        }

        if ($this->pass) {
            $this->query->update($this->table, ['pass' => $this->pass], ['id' => $this->id]);
        }

        // updated without meta
        if (is_null($this->props)) {
            return true;
        }

        foreach ($this->props as $key => $value) {
            $meta = $this->query->get($this->metaTable, ['key', 'value'], ['user_id' => $this->id, 'key' => $key]);

            if ($meta) {
                $this->query->update($this->metaTable, ['value' => $value], ['user_id' => $this->id, 'key' => $key]);
            } else {
                $this->query->insert($this->metaTable, [
                    'user_id' => $this->id ,
                    'key' => $key,
                    'value' => $value,
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
        $this->query->delete($this->metaTable, ['user_id' => $this->id]);
        $this->query->delete($this->table, ['id' => $this->id]);

        return true;
    }

    public function findByLogin($login): ?object
    {
        $res = $this->query->get($this->table, 'all', ['login' => $login]);
        return !empty($res) ? (object) $res[0] : null;
    }
}
