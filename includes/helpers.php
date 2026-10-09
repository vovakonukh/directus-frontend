<?php
function formatDimension($value) {
    $num = floatval($value);
    return ($num == intval($num))
        ? intval($num)
        : str_replace('.', ',', $num);
}

// Склонение по числу: pluralRu(2, ['спальня', 'спальни', 'спален']) → 'спальни'
function pluralRu($n, array $forms): string {
    $n = abs((int) $n) % 100;
    $n1 = $n % 10;
    if ($n > 10 && $n < 20) return $forms[2];
    if ($n1 > 1 && $n1 < 5) return $forms[1];
    if ($n1 == 1) return $forms[0];
    return $forms[2];
}

// Короткий текст для meta description: без HTML, обрезан по границе слова
function seoExcerpt($html, int $limit = 155): string {
    $text = html_entity_decode(strip_tags(str_replace('<', ' <', (string) $html)), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = trim(preg_replace('/\s+/u', ' ', $text));
    if (mb_strlen($text) <= $limit) return $text;
    $cut = mb_substr($text, 0, $limit);
    return rtrim(mb_substr($cut, 0, mb_strrpos($cut, ' ') ?: $limit), ' ,.;:—-') . '…';
}

function getShiftTelegramLink(string $fallback = 'https://t.me/alinaclasshouse'): string {
    $day = (int) date('j');
    $res = fetchItems('shifts', [
        'filter[day][_in]' => "0,$day",
        'fields' => 'day,manager.telegram_link',
    ]);
    if (!$res) return $fallback;

    $links = [];
    foreach ($res as $row) {
        $links[$row['day']] = $row['manager']['telegram_link'] ?? null;
    }
    return $links[$day] ?? $links[0] ?? $fallback;
}