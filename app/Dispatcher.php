<?php
namespace App;

use JetBrains\PhpStorm\NoReturn;
use App\Controller\PostController as Post;

class Dispatcher
{
    private object $guard;

    public function __construct()
    {
        $this->guard = new Guard();
    }

    /**
     * Dispatch logic
     *
     * @param $data
     */
    #[NoReturn] public function dispatch($data)
    {
        $auth = ['auth', 'reg'];
        $system = ['users', 'options'];

        $method = $data['method'] ?? null;
        $route = $data['type'] ?? null;
        $itemID = $data['content']['id'] ?? null;

        //home page
        if (empty($route)) {
            Response::JSON(APP_NAME . ' app. Author: ' . APP_AUTHOR . '. Ver: ' . APP_VER);
        }

        //if method empty
        if (empty($method)) {
            Response::JSONError('Method error!');
        }

        //auth reserved routes
        $this->reservedRoutes($route, $method, $auth, $data);

        //run guard. Also guard set auth user id
        if (!$this->guard->auth()) {
            Response::JSONError('Guard error');
        }

        //check access
        if (!$this->guard->access($route, $method, $itemID)) {
            Response::JSONError('Access denied');
        }

        //system reserved routes
        $this->reservedRoutes($route, $method, $system, $data);

        //if we use abstract name in route. Example: tasks or articles
        $post = new Post([...$data, 'user_id' => $this->guard->userID]);
        Response::JSON($post->{$method}());
    }

    private function getClassName($str):string
    {
        if (substr($str, -1) === 's') {
            $str = substr($str, 0, -1);
        }

        return 'App\\' . ucfirst($str);
    }

    private function reservedRoutes($route, $method, $reserved, $data)
    {
        if (in_array($route, $reserved, true)) {
            $class = $this->getClassName($route);
            $instance = new $class($data);

            $result = $instance->{$method}();
            Response::JSON($result);
        }
    }
}
