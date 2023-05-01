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
        $type = $data['type'];

        if (empty($type)) {
            $this->responce->JSON(APP_NAME . ' app. Author: ' . APP_AUTHOR . '. Ver: ' . APP_VER);
            exit();
        }

        if ($type === 'login') {
            $authUser = new Auth($data);
            $this->responce->JSON($authUser->login());
            exit();
        }

        if (empty($method)) {
            $this->responce->JSON('method err');
            exit();
        }

        if ($type === 'users') {
            $user = new User($data);
            $this->responce->JSON($user->{$method}());
            exit();
        }

        if ($type === 'groups') {
            //
        }


        if ($type !== 'users') {
            $post = new Post($data);
            $this->responce->JSON($post->{$method}());
            exit();
        }
    }
}
