<?php


namespace App;


class Reg
{
    private User $user;

    public function __construct($data)
    {
        $this->user = new User($data);
    }

    public function user()
    {
        return $this->user->create();
    }

}