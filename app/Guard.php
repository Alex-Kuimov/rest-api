<?php
namespace App;

class Guard
{
    private object $query;
    private string $authTable;
    private string $usersTable;
    private string $postsTable;
    public ?int $userID;

    public function __construct()
    {
        $this->authTable = 'auth';
        $this->usersTable = 'users';
        $this->postsTable = 'posts';
        $this->query = new Query;
    }

    public function auth():bool
    {
        return $this->isAuth();
    }

    public function access($route, $method, $postID): bool
    {
        $roles = ['admin', 'editor', 'subscriber'];
        $user = $this->query->get($this->usersTable, 'all', ['id' => $this->userID]);
        $role = $user[0]['role'];

        if (!in_array($role, $roles, true)) {
            return false;
        }

        if ($role === 'subscriber' && $method !== 'get') {
            return false;
        }

        if ($this->deniedSystemEdit($role, $route)) {
            return false;
        }

        if ($this->deniedPostEdit($role, $method, $postID)) {
            return false;
        }

        return true;
    }

    private function deniedSystemEdit($role, $route): bool
    {
        $routes = ['options', 'users'];
        $roles = ['editor', 'subscriber'];

        if (in_array($role, $roles, true) && in_array($route, $routes, true)) {
            return true;
        }

        return false;
    }

    private function deniedPostEdit($role, $method, $itemID): bool
    {
        $roles = ['editor'];
        $methods = ['get', 'create', 'update', 'delete'];

        if (in_array($role, $roles, true) && in_array($method, $methods, true)) {
            if ($method === 'get' || $method === 'create') {
                return false;
            }

            if ($method === 'update' || $method === 'delete') {
                $posts = $this->query->get($this->postsTable, 'all', ['id' => $itemID]);
                $userID = $posts[0]['user_id'];

                if ($this->userID === $userID) {
                    return false;
                }
            }

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
