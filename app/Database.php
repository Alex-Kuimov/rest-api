<?php
namespace App;

use Illuminate\Database\Capsule\Manager as Capsule;

class Database
{
    public function init()
    {
        $capsule = new Capsule;

        $capsule->addConnection([
            'driver' => 'mysql',
            'host' => DB_HOST,
            'database' => DB_NAME,
            'username' => DB_USER,
            'password' => DB_PASSWORD,
            'charset' => 'utf8',
            'collation' => 'utf8_general_ci',
            'prefix' => '',
        ]);

        $capsule->bootEloquent();
    }
}
