<?php
namespace App;

class Dispatcher
{
    private object $responce;

    public function __construct()
    {
        $this->responce = new Response();
    }

    /**
     * Dispatch
     *
     * @param $data
     */
    public function dispatch($data)
    {
        $method = $data['method'];
        $route = $data['type'];

        if (empty($route)) {
            $this->responce->JSON(APP_NAME . ' app. Author: ' . APP_AUTHOR . '. Ver: ' . APP_VER);
        }

        if (empty($method)) {
            $this->responce->JSON('method err');
        }

        if ($route === 'login') {
            $authUser = new Auth($data);
            $this->responce->JSON($authUser->login());
        }

        if ($route === 'users') {
            $user = new User($data);
            $this->responce->JSON($user->{$method}());
        }

        if ($route === 'groups') {
            //
        }

        $post = new Post($data);
        $this->responce->JSON($post->{$method}());

    }
}
