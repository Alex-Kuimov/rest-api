<?php
namespace App;

class Post extends Model
{
    public function __construct($data)
    {
        parent::__construct('posts', $data);
    }
}
