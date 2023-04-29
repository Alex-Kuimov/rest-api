<?php
namespace App;

class Validator
{
    /**
     * Available methods array
     *
     * @return array
     */
    public function availableMethods(): array
    {
        return ['get', 'update', 'create', 'delete'];
    }
}
