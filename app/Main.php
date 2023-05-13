<?php
namespace App;

use JetBrains\PhpStorm\NoReturn;

class Main
{
    private object $route;
    private object $dispatcher;
    private object $plugins;

    public function __construct()
    {
        $this->route = new Route();
        $this->dispatcher = new Dispatcher();
        $this->plugins =  new Plugins();
    }

    /**
     * Run app
     */
    #[NoReturn] public function makeMagic()
    {
        //require_once '../plugins/telegram/index.php';
        $this->plugins->init();
        $this->route->routing();
        $data = $this->route->getMeta();

        $this->dispatcher->dispatch($data);
    }
}
