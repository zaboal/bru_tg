<?php

/**
 * Отправляет сообщение в указанный чат Telegram.
 *
 * @param int $chat Идентификатор чата, в который нужно отправить сообщение.
 * @param string $text Текст сообщения для отправки.
 */
function sendMessage($chat, $text)
{
    $data = [
        'chat_id' => $chat,
        'text' => $text
    ];
    $api = $_ENV['API_KEY'];
    $url = "https://api.telegram.org/bot$api/sendMessage";
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_exec($ch);
    curl_close($ch);
}

/**
 * Получает идентификатор чата Telegram, связанный с указанным номером телефона.
 *
 * @param string $number Номер телефона для поиска.
 * @return int|false Идентификатор чата Telegram, если найден, иначе false.
 */
function getChat($number)
{
    $url = 'https://api.us-east.aws.tinybird.co/v0/pipes/untitled_pipe_7865.json';
    $params = ['num' => $number];
    $token = $_ENV['TOKEN'];
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url . '?' . http_build_query($params),
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $token,
            'Accept-Encoding: gzip'
        ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 30
    ]);

    $response = curl_exec($ch);

    $response = json_decode(gzdecode($response));
    curl_close($ch);
    if (! $response->data[0]->tg) {
        return false;
    }
    return (int)$response->data[0]->tg;
}

/**
 * Обрабатывает входящие события и отправляет сообщение пользователю.
 *
 * @param array $event Данные события.
 * @param array $context Данные контекста (не используются).
 * @throws Exception Если тело события отсутствует.
 */
function handler($event, $context)
{
    // context - бесполезен, $event - закодированные данные
    if (! $event['body']) {
        throw new Exception("No body in message");
    }

    $text = base64_decode($event['body'], true);
    parse_str($text, $params);
    $changes = json_decode($params['changes'], true);
    $data = json_decode($params['data']);
    $new = $changes[1]['data']['bonus_sum'];
    $delta = $new - $changes[0]['data']['bonus_sum'];
    $message = "Благодарим за покупку!\nНачислено " . $delta . " баллов, теперь у Вас " . $new . " баллов";
    $id = getChat($data->num);
    sendMessage($id, $message);
}