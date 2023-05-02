<?php
namespace App;

class Dispatcher
{
    private object $response;
    private object $guard;

    public function __construct()
    {
        $this->response = new Response();
        $this->guard = new Guard();
    }

    /**
     * Dispatch
     *
     * @param $data
     */
    public function dispatch($data)
    {
        $reserved = ['users' , 'groups', 'auth'];

        $method = $data['method'] ?? null;
        $route = $data['type'] ?? null;

        if (empty($route)) {
            $this->response->JSON(APP_NAME . ' app. Author: ' . APP_AUTHOR . '. Ver: ' . APP_VER);
        }

        if (!$this->guard->monitor($route, $method)) {
            $this->response->JSON('err');
        }

        if (in_array($route, $reserved, true)) {
            $class = $this->getClassName($route);
            $instance = new $class($data);
            $this->response->JSON($instance->{$method}());
        }

        $post = new Post($data);
        $this->response->JSON($post->{$method}());
    }

    private function getClassName($str):string
    {
        if (substr($str, -1) === 's') {
            $str = substr($str, 0, -1);
        }

        return 'App\\' . ucfirst($str);
    }
}
