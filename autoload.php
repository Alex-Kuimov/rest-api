<?php
spl_autoload_register(function ($class) {
    $name = str_replace('App\\', '', $class);
    require_once '../app/' . $name . '.php';
});
