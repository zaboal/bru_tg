<?php

/**
 * Логирует входящее сообщение в Tinybird API.
 *
 * @param string $incoming_message Входящее сообщение в формате JSON.
 * @return bool True, если сообщение было успешно залогировано, false в противном случае.
 */
function log_message($incoming_message): bool
{
    $data = json_decode($incoming_message, true);

    if ($data === null) {
        return false;
    }

    $json_data = json_encode(array("data" => json_encode($data)));

    if ($json_data === false) {
        return false;
    }

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.us-east.aws.tinybird.co/v0/events?name=schema_ds_7565",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $json_data,
        CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer " . $_ENV["TOKEN"],
            "Content-Type: application/json"
        ),
    ));

    curl_exec($curl);
    return true;
}

/**
 * Логирует пользователя, отправляя его Telegram ID и номер телефона в Tinybird API.
 *
 * @param string $tg Telegram ID пользователя.
 * @param int $number Номер телефона пользователя.
 * @return bool True, если пользователь был успешно залогирован, false в противном случае.
 */
function login_user($tg, $number): bool
{
    $data = [
        'num' => (int)$number,
        'tg' => $tg
    ];

    $json_data = json_encode($data);

    $curl = curl_init();

    $token = $_ENV["TOKEN2"];

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.us-east.aws.tinybird.co/v0/events?name=demo_custom_data_2453",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $json_data,
        CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer $token",
            "Content-Type: application/json"
        ),
    ));

    $response = curl_exec($curl);
    echo $response;
    curl_close($curl);
    $ppp = json_decode($response);
    return $ppp->successful_rows == 1;
}
