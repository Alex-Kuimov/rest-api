<?php
namespace App;

class Guard
{
    private object $query;
    private string $authTable;

    public function __construct()
    {
        $this->authTable = 'Auth';
        $this->query = new Query;
    }

    public function monitor($route, $method):bool
    {
        $authMethod = ['login', 'logout'];

        if ($route === 'auth' && in_array($method, $authMethod, true)) {
            return true;
        }

        if (empty($method)) {
            return false;
        }

        if ($this->isAuth()) {
            return true;
        }

        return false;
    }

    private function isAuth():bool
    {
        $headers = getallheaders();
        $headerToken = $headers['Auth'] ?? null;

        if (is_null($headerToken)) {
            return false;
        }

        $auth = $this->query->get($this->authTable, 'all', ['token' => $headerToken]);
        $token = !empty($auth) ? $auth[0]['token'] : null;

        return $token === $headerToken;
    }
}
