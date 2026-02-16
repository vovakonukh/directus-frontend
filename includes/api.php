<?php
define('API_URL', 'http://176.53.162.57:8055');

function fetchItems($collection, $params = []) {
    $query = http_build_query($params);
    $url = API_URL . '/items/' . $collection . ($query ? '?' . $query : '');
    
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    return $data['data'] ?? null;
}

function getAssetUrl($fileId) {
    return API_URL . '/assets/' . $fileId;
}