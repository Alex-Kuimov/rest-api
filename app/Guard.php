<?php
namespace App;

class Guard
{
    private object $query;
    private string $authTable;
    private string $usersTable;
    private string $postsTable;
    private string $accessTable;
    public ?int $userID;

    public function __construct()
    {
        $this->authTable = 'auth';
        $this->usersTable = 'users';
        $this->postsTable = 'posts';
        $this->accessTable = 'access';
        $this->query = new Query;
    }

    public function auth():bool
    {
        //return $this->isAuth();

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

    public function access($route, $method, $postID): bool
    {
        $permissions = $this->getPermission();
        $actionAccess = $permissions[$method.'_action'];
        $systemAccess = $permissions[$route.'_action'] ?? null;
        $allAccess = $permissions['all_items'];

        if (!$actionAccess) {
            return false;
        }

        if (isset($systemAccess) && !$systemAccess) {
            return false;
        }

        if (!$allAccess && ($method === 'update' || $method === 'delete') && !$this->isOwner($postID)) {
            return false;
        }

        return $actionAccess;
    }

    private function getPermission()
    {
        $user = $this->query->get($this->usersTable, 'all', ['id' => $this->userID]);
        $role = $user[0]['role'];

        $access = $this->query->get($this->accessTable, 'all', ['role' => $role]);

        return $access[0] ?? null;
    }

    private function isOwner($postID): bool
    {
        $posts = $this->query->get($this->postsTable, 'all', ['id' => $postID]);
        $userID = $posts[0]['user_id'] ?? null;

        return $this->userID === $userID;
    }
}
