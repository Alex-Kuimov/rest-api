<?php
namespace App;

class Auth
{
    private ?string $login;
    private ?string $pass;
    private ?string $table;
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

        if ($user->pass === md5($this->pass)) {
            return $this->createToken($user->id);
        }

        return null;
    }

    private function createToken($id): string
    {
        $token = $this->generateAuthToken();

        $this->query->insert($this->table, [
            'user_id' => $id,
            'token' => $token,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $token;
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
