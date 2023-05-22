<?php
namespace App\Controller;

use App\Model\Post as Post;
use App\Model\Meta as Meta;

class PostController
{
    public ?int $id;
    private ?string $type;
    private ?string $name;
    public ?array $props;
    private ?int $userID;

    public function __construct($data)
    {
        $this->id = $data['content']['id'] ?? null;
        $this->name = $data['content']['name'] ?? null;
        $this->type = $data['type'] ?? null;
        $this->userID = $data['user_id'] ?? null;
        $this->props = $data['content']['meta'] ?? null;
    }

    /**
     * Get
     * @return mixed
     */
    public function get(): mixed
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
     * @return mixed
     */
    private function getAll($type): mixed
    {
        return Post::with('meta')->where('type', $type)->where('user_id', $this->userID)->get();
    }

    /**
     * Get one
     *
     * @param $id
     * @return mixed
     */
    private function getOne($id): mixed
    {
        return Post::with('meta')->where('id', $id)->where('user_id', $this->userID)->first();
    }

    /**
     * Create
     *
     * @return int
     */
    public function create(): int
    {
        $post = Post::create([
            'name' => $this->name,
            'type' => $this->type,
            'user_id' => $this->userID,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        if (is_null($this->props)) {
            return $post->id;
        }

        foreach ($this->props as $key => $value) {
            Meta::create([
                'post_id' => $post->id,
                'key' => $key,
                'value' => $value,
            ]);
        }

        return $post->id;
    }

    /**
     * Update
     *
     * @return bool
     */
    public function update()
    {
        $post = Post::find($this->id);

        $post->update([
            'name' => $this->name,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        if (is_null($this->props)) {
            return true;
        }

        foreach ($this->props as $key => $value) {
            $meta = Meta::where('post_id', $post->id)->first();

            $meta->update([
                'key' => $key,
                'value' => $value,
            ]);
        }

        return true;
    }

    /**
     * Delete
     *
     * @return bool
     */
    public function delete(): bool
    {
        $post = Post::with('meta')->find($this->id);

        $post->meta->each->delete();
        $post->delete();

        return true;
    }
}
