<?php
namespace App;

class Post extends Model
{
    private string $table;
    private string $metaTable;
    private ?string $type;
    private ?string $name;
    private ?int $userID;

    public function __construct($data)
    {
        $this->initDefault($data);

        $this->table = 'posts';
        $this->metaTable = $this->table . 'meta';

        $this->type = $data['type'] ?? null;
        $this->name = $data['content']['name'] ?? null;
        $this->userID = $data['user_id'] ?? null;
    }

    /**
     * Get
     *
     * @return array|null
     */
    public function get(): ?array
    {
        $data = $this->id ? $this->getOne($this->id) : $this->getAll($this->type);

        if (!$data) {
            return null;
        }

        return $data;
    }

    /**
     * Get list
     *
     * @param $type
     * @return array
     */
    private function getAll($type): ?array
    {
        $posts = $this->query->get($this->table, 'all', ['type' => $type]);

        if (empty($posts)) {
            return null;
        }

        $ids = implode(",", array_map(function ($post) {
            return $post['id'];
        }, $posts));


        $props = $this->query->get($this->metaTable, ['post_id', 'key', 'value'], ['post_id' => $ids], true);

        return $this->withMeta($posts, $props, 'post_id');
    }

    /**
     * Get one
     *
     * @param $id
     * @return array|null
     */
    private function getOne($id): ?array
    {
        $posts = $this->query->get($this->table, 'all', ['id' => $id]);

        if (empty($posts)) {
            return null;
        }

        $props = $this->query->get($this->metaTable, ['key', 'value'], ['id' => $id]);

        return $this->withMeta($posts, $props, 'post_id');
    }

    /**
     * Create
     *
     * @return int
     */
    public function create(): int
    {
        $id = $this->query->insert($this->table, [
            'name' => $this->name,
            'type' => $this->type,
            'user_id' => $this->userID,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // created without meta
        if (is_null($this->props)) {
            return $id;
        }

        foreach ($this->props as $key => $value) {
            $this->query->insert($this->metaTable, [
                'post_id' => $id ,
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
        $this->query->update($this->table, ['name' => $this->name, 'updated_at' => date('Y-m-d H:i:s') ], ['id' => $this->id]);

        // updated without meta
        if (is_null($this->props)) {
            return true;
        }

        foreach ($this->props as $key => $value) {
            $meta = $this->query->get($this->metaTable, ['post_id', 'key', 'value'], ['post_id' => $this->id, 'key' => $key]);

            if ($meta) {
                $this->query->update($this->metaTable, ['value' => $value], ['post_id' => $this->id, 'key' => $key]);
            } else {
                $this->query->insert($this->metaTable, [
                    'post_id' => $this->id ,
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
        $this->query->delete($this->metaTable, ['post_id' => $this->id]);
        $this->query->delete($this->table, ['id' => $this->id]);

        return true;
    }

}
