<?php

require_once(__DIR__ . '/config.php');

function post($url, $params=[], $headers=[], $timeout = 5) {
    $curl = curl_init( $url );
    $params = json_encode($params);
    $headers[] = 'Content-Type: application/json;charset=utf-8';
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt( $curl, CURLOPT_POSTFIELDS, $params );
    curl_setopt( $curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt( $curl, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
    $result = curl_exec($curl);
    curl_close($curl);
    return json_decode($result, true);
}

$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['HTTP_X_TELEGRAM_BOT_API_SECRET_TOKEN'] !== TG_SECRET || !$data['message']['text'] || !$data['message']['chat']['id'])
    exit;

$page = post('https://api.notion.com/v1/pages', [
    'parent' => [
        "type" => "database_id",
        "database_id" =>  NOTION_DATABASE
    ],
    "properties" => [
        "Name" => [
            "title" => [
                [
                    "text" => ["content" => $data['message']['text']]
                ]
            ]
        ]
    ]
], ["Authorization: Bearer ".NOTION_TOKEN, "Notion-Version: 2022-06-28"]);

post('https://api.telegram.org/bot' . TG_TOKEN . '/sendMessage',
    [
        'chat_id' => $data['message']['chat']['id'],
       'text' => $page['id']?"✅ Сохранено":"❌ Ошибка"
    ]
);