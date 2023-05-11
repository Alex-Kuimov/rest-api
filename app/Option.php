<?php
namespace App;

class Option
{
    private string $table;
    private ?int $id;
    private ?string $name;
    private ?string $value;
    private object $query;

    public function __construct($data)
    {
        $this->table = 'options';

        $this->id = $data['content']['id'] ?? null;
        $this->name = $data['content']['name'] ?? null;
        $this->value = $data['content']['value'] ?? null;
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
     * @param $type
     * @return array
     */
    private function getAll(): ?array
    {
        $options = $this->query->get($this->table, 'all', null);

        if (empty($options)) {
            return null;
        }

        $result = [];

        foreach ($options as $option) {
            $id = $option['id'];
            $result[$id] = $option;
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
        $option = $this->query->get($this->table, 'all', ['id' => $id]);

        if (empty($option)) {
            return null;
        }

        return $option;
    }

    /**
     * Create
     *
     * @return int
     */
    public function create(): int
    {
        return $this->query->insert($this->table, [
            'name' => $this->name,
            'value' => $this->value,
        ]);
    }

    /**
     * Update
     *
     * @return bool
     */
    public function update(): bool
    {
        $this->query->update($this->table, ['name' => $this->name, 'value' => $this->value ], ['id' => $this->id]);
        return true;
    }

    /**
     * Delete
     *
     * @return bool
     */
    public function delete(): bool
    {
        $this->query->delete($this->table, ['id' => $this->id]);
        return true;
    }
}
