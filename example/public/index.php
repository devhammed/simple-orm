<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Devhammed\SimpleOrm\Model;
use Devhammed\SimpleOrmExample\Models\User;
use Devhammed\SimpleOrmExample\Models\PersonalAccessToken;

Model::addConnection('default', [
    'dsn'      => 'mysql:host=localhost;dbname=gochap',
    'username' => 'root',
    'password' => 'root',
]);

$user = User::findOne(1);

$token = PersonalAccessToken::findOne(1);

var_dump($user, $token);
