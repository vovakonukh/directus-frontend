<?php
define('API_URL', 'https://api.class-house.ru');

function fetchItems($collection, $params = []) {
    static $ch = null; // одно соединение на все запросы страницы (keep-alive)

    $query = http_build_query($params);
    $url = API_URL . '/items/' . $collection . ($query ? '?' . $query : '');

    if ($ch === null) {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 3,   // секунд на установку соединения
            CURLOPT_TIMEOUT        => 5,   // секунд на весь запрос
            CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4,
        ]);
    }
    curl_setopt($ch, CURLOPT_URL, $url);

    $response = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($response === false || $code !== 200) {
        error_log("API error [$code] $url: " . curl_error($ch));
        return null;
    }

    $data = json_decode($response, true);
    return $data['data'] ?? null;
}

function getAssetUrl($fileId) {
    return API_URL . '/assets/' . $fileId;
}