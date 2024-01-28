<?php

require_once(__DIR__ . '/config.php');

$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['HTTP_X_TELEGRAM_BOT_API_SECRET_TOKEN'] !== TG_SECRET || !$data['message']['text'] || !$data['message']['chat']['id'])
    exit;

$text = $data['message']['text'];
$ch = curl_init('https://api.telegram.org/bot' . TG_TOKEN . '/sendMessage');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, [
    'chat_id' => $data['message']['chat']['id'],
    'text' => $text
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, false);
$res = curl_exec($ch);
curl_close($ch);