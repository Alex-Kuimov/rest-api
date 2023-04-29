<?php
namespace App;

class Main
{
    private Route $route;
    private Response $response;

    public function __construct()
    {
        $this->route = new Route();
        $this->response = new Response();
    }

    /**
     * Run app
     */
    public function makeMagic()
    {
        $this->route->routing();
        $data = $this->route->getMeta();

        $this->response->getJSON($data);
    }
}
