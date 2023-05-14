<?php
namespace App;


class Model
{
    public object $query;
    public ?array $props;
    public ?int $id;

    public function initDefault($data)
    {
        $this->query = new Query;
        $this->props = $data['content']['meta'] ?? null;
        $this->id = $data['content']['id'] ?? null;
    }

    public function withMeta($posts, $props, $key): array
    {
        $result = [];
        foreach ($posts as $post) {
            $id = $post['id'];
            $result[$id] = $post;
            $meta = [];

            foreach ($props as $prop) {
                if ($prop[$key] === $id) {
                    $meta[$prop['key']] = $prop['value'];
                }
            }

            $result[$id]['meta'] = $meta;
        }

        return $result;
    }
}