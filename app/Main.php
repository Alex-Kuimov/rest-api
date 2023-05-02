<?php
namespace App;

use JetBrains\PhpStorm\NoReturn;

class Main
{
    private object $route;
    private object $dispatcher;

    public function __construct()
    {
        $this->route = new Route();
        $this->dispatcher = new Dispatcher();
    }

    /**
     * Run app
     */
    #[NoReturn] public function makeMagic()
    {
        $this->route->routing();
        $data = $this->route->getMeta();

        $this->dispatcher->dispatch($data);
    }
}
