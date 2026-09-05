<?php
function formatDimension($value) {
    $num = floatval($value);
    return ($num == intval($num))
        ? intval($num)
        : str_replace('.', ',', $num);
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