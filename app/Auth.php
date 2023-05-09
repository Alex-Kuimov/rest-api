<?php
namespace App;

class Auth
{
    private ?string $login;
    private ?string $pass;
    private string $table;
    private object $user;
    private object $query;

    public function __construct($data)
    {
        $this->table = 'auth';
        $this->login = $data['content']['login'] ?? null;
        $this->pass = $data['content']['pass'] ?? null;
        $this->user = new User($data);

        $this->query = new Query;
    }

    public function login(): ?string
    {
        $user = $this->user->findByLogin($this->login);

        if (!$user) {
            return null;
        }

        if ($user->pass === md5($this->pass)) {
            return $this->createToken($user->id);
        }

        return null;
    }

    private function createToken($id): string
    {
        $token = $this->generateAuthToken();

        $auth = $this->query->get($this->table, 'all', ['user_id' => $id]);
        $now = date('Y-m-d H:i:s');

        if (!empty($auth)) {
            $created = $auth[0]['created_at'];

            if (strtotime($now) > strtotime($created)) {
                $this->query->update($this->table, ['token' => $token], ['user_id' => $id]);
                return $token;
            }
        } else {
            $this->query->insert($this->table, [
                'user_id' => $id,
                'token' => $token,
                'created_at' => $now,
            ]);
            return $token;
        }
        return $auth[0]['token'];
    }

    private function generateAuthToken($length = 20): string
    {
        $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $charactersLength = strlen($characters);
        $token = "";
        for ($i = 0; $i < $length; $i++) {
            $token .= $characters[rand(0, $charactersLength - 1)];
        }
        return $token;
    }
}
