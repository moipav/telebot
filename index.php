<?php
//https://api.telegram.org/bot{token}/{method}

require_once 'BotTelegram.php';
//подключаем токен
$env=  require_once "env.php";
$token = $env['token'];
$teleBot = new BotTelegram($token, __DIR__ . '/user_state');
$teleBot->handle();