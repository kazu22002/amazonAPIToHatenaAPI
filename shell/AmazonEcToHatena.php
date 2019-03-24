<?php

require_once __DIR__."/../vendor/autoload.php";

use App\Controllers\GoodsController;

// .env
$dotenv = new Dotenv\Dotenv(__DIR__."/../config/");
$dotenv->load();

if(!isset($argv[1])) return ;

$param = $argv[1];

// 2278488051 コミックス
// 2430812051 少年漫画
// 2430869051 青年漫画
// 2430765051 少女漫画
// 2430737051 女性漫画
// 2410280051 ラノベ
// 2275256051 kindle本

$controller = new GoodsController();
$controller->post($param);
