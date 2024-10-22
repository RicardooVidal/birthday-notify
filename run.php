<?php

use App\Core\Core;
use App\Core\Database;
use App\Core\Telegram;
use Carbon\Carbon;

require_once 'vendor/autoload.php';

$connectionParams = array(
    'dbname' => '',
    'user' => '',
    'password' => '',
    'host' => '',
    'driver' => 'pdo_mysql',
);

$carbon = Carbon::now();

$user = isset($argv[1]) ? $argv[1] : '';
$date = isset($argv[2]) ? $argv[2] : '';

if (empty($user)) {
    throw new Exception('Parameter user must be passed');
}

if (empty($date)) {
    $date = $carbon->isoFormat('DD') . '/' . $carbon->isoFormat('MM');
}

$database = new Database($connectionParams);
$telegram = new Telegram();
$core = new Core($database, $telegram, $user, $date);
$core->initProcess();
