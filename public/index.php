<?php
namespace App;

require_once '../config.php';
require_once '../vendor/autoload.php';

$wizard = new Main();
$wizard->makeMagic();
