<?php
namespace App;

use JetBrains\PhpStorm\NoReturn;

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
     * Dispatch logic
     *
     * @param $data
     */
    #[NoReturn] public function dispatch($data)
    {
        $reserved = ['users' , 'groups', 'auth', 'reg', 'options'];

        $method = $data['method'] ?? null;
        $route = $data['type'] ?? null;

        //home page
        if (empty($route)) {
            $this->response->JSON(APP_NAME . ' app. Author: ' . APP_AUTHOR . '. Ver: ' . APP_VER);
        }

        //if method empty
        if (empty($method)) {
            $this->response->JSONError('method error');
        }

        //run guard. Also guard set auth user id
        if (!$this->guard->monitor($route, $method)) {
            $this->response->JSONError('guard error');
        }

        //if we use reserved name of routes
        if (in_array($route, $reserved, true)) {
            $class = $this->getClassName($route);
            $instance = new $class($data);

            $result = $instance->{$method}();

            if ($result) {
                $this->response->JSON($result);
            }

            $this->response->JSONError('empty result');
        }

        //if we use abstract name in route. Example: tasks or articles
        $post = new Post([...$data, 'user_id' => $this->guard->userID]);
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
