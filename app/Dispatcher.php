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
        $reserved = ['users' , 'groups', 'auth'];

        $method = $data['method'];
        $route = $data['type'];

        if (empty($route)) {
            $this->responce->JSON(APP_NAME . ' app. Author: ' . APP_AUTHOR . '. Ver: ' . APP_VER);
        }

        if (empty($method)) {
            $this->responce->JSON('method err');
        }

        if (in_array($route, $reserved, true)) {
            $class = $this->getClassName($route);
            $instance = new $class($data);
            $this->responce->JSON($instance->{$method}());
        }

        $post = new Post($data);
        $this->responce->JSON($post->{$method}());
    }

    private function getClassName($str):string
    {
        if (substr($str, -1) === 's') {
            $str = substr($str, 0, -1);
        }

        return 'App\\' . ucfirst($str);
    }
}
