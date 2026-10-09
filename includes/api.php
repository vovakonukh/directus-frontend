<?php
define('API_URL', 'https://api.class-house.ru');
define('API_CACHE_DIR', __DIR__ . '/../cache/api');
// на localhost кэш не используется, чтобы правки из Directus были видны сразу
define('API_CACHE_TTL', strpos($_SERVER['HTTP_HOST'] ?? '', 'localhost') === 0 ? 0 : 86400); // секунд (сутки); при правках в Directus кэш сбрасывает Flow через /cache-clear

function fetchItems($collection, $params = []) {
    static $ch = null; // одно соединение на все запросы страницы (keep-alive)

    $query = http_build_query($params);
    $url = API_URL . '/items/' . $collection . ($query ? '?' . $query : '');

    $cacheFile = API_CACHE_DIR . '/' . md5($url) . '.json';
    if (is_file($cacheFile) && time() - filemtime($cacheFile) < API_CACHE_TTL) {
        return json_decode(file_get_contents($cacheFile), true);
    }

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
        // API недоступен — отдаём последнюю сохранённую копию, даже устаревшую
        return is_file($cacheFile) ? json_decode(file_get_contents($cacheFile), true) : null;
    }

    $data = json_decode($response, true)['data'] ?? null;

    // пустые ответы не сохраняем: иначе каждый несуществующий адрес создаёт файл
    if (!empty($data)) {
        if (!is_dir(API_CACHE_DIR)) {
            mkdir(API_CACHE_DIR, 0755, true);
        }
        // запись через временный файл, чтобы параллельный запрос не прочитал половину
        $tmp = $cacheFile . '.' . uniqid('', true) . '.tmp';
        if (file_put_contents($tmp, json_encode($data, JSON_UNESCAPED_UNICODE)) !== false) {
            rename($tmp, $cacheFile);
        }
    }

    return $data;
}

function getAssetUrl($fileId) {
    return API_URL . '/assets/' . $fileId;
}