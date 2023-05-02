<?php
namespace App;

class Guard
{
    private object $query;
    private string $authTable;
    public ?int $userID;

    public function __construct()
    {
        $this->authTable = 'Auth';
        $this->query = new Query;
    }

    public function monitor($route, $method):bool
    {
        $authMethod = ['login', 'logout'];
        $regMethod = ['user', ];

        //auth methods
        if ($route === 'auth' && in_array($method, $authMethod, true)) {
            return true;
        }

        if ($route === 'reg' && in_array($method, $regMethod, true)) {
            return true;
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

        //if auth was ok we set user id
        $this->userID = !empty($auth) ? $auth[0]['user_id'] : null;

        return $token === $headerToken;
    }
}
