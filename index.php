<?php
//https://api.telegram.org/bot{token}/{method}

$chat_id = "-4888028242";
$token = "7970368981:AAHMcrHi4T9QT3rS5JOZMsQ6TO5jfn_P9yo";

$getQuery = [
    "chat_id" => -4888028242,
    "text" => "Текст для проверки чата 2",
    "parse_mode" => "html"
];

$editQuery = [
    "chat_id" => -4888028242,
    "parse_mode" => "html",
    "text" => "<i>edited message</i>",
    "message_id" => 10
];
//$ch = curl_init("https://api.telegram.org/bot" . $token . "/editMessageText?" . http_build_query($editQuery)); //изменить текст сообщения
//$ch = curl_init("https://api.telegram.org/bot" . $token . "/sendMessage?" . http_build_query($getQuery)); // отправить сообщение
curl_setopt_array($ch,
    [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_HEADER => false
    ]);

$resultQuery = curl_exec($ch);
curl_close($ch);

echo $resultQuery;

