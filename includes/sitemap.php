<?php
// Генерация sitemap.xml: статические страницы + опубликованные записи коллекций Directus

$siteUrl = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];

// Статические страницы (при добавлении нового роута в index.php — добавить сюда)
$staticRoutes = [
    '',
    'projects',
    'finished',
    'feedback',
    'services',
    'services/stroitelstvo',
    'services/fundament',
    'services/inzhenerka',
    'ipoteka',
    'blog',
    'video',
    'contacts',
    'postavshhikam',
    'policy',
];

// Коллекции с детальными страницами: коллекция => [префикс URL, поля с датой изменения]
$collections = [
    'projects' => ['projects', ['date_updated', 'date_created']],
    'finished' => ['finished', ['date_updated', 'date_created']],
    'blog'     => ['blog',     ['date_updated', 'date_created']],
    'video'    => ['video',    ['date_updated', 'date']],
];

// Страницы, закрытые от индексации в pages_seo, в карту не попадают
$noindex = fetchItems('pages_seo', [
    'filter[seo_noindex][_eq]' => 'true',
    'fields' => 'route',
    'limit' => -1,
]);
$noindexRoutes = array_column($noindex ?? [], 'route');

$urls = [];
foreach ($staticRoutes as $route) {
    if (in_array($route, $noindexRoutes, true)) continue;
    $urls[] = ['loc' => $siteUrl . '/' . $route];
}

foreach ($collections as $collection => [$prefix, $dateFields]) {
    $items = fetchItems($collection, [
        'filter[status][_eq]' => 'published',
        'fields' => 'slug,' . implode(',', $dateFields),
        'limit' => -1,
    ]);

    // API недоступен — отдаём 503, чтобы поисковик не принял неполную карту
    if ($items === null) {
        http_response_code(503);
        header('Retry-After: 3600');
        exit;
    }

    foreach ($items as $item) {
        if (empty($item['slug'])) continue;
        $url = ['loc' => $siteUrl . '/' . $prefix . '/' . $item['slug']];
        foreach ($dateFields as $field) {
            if (!empty($item[$field])) {
                $url['lastmod'] = substr($item[$field], 0, 10);
                break;
            }
        }
        $urls[] = $url;
    }
}

header('Content-Type: application/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($urls as $url) {
    echo '  <url><loc>' . htmlspecialchars($url['loc']) . '</loc>';
    if (!empty($url['lastmod'])) echo '<lastmod>' . $url['lastmod'] . '</lastmod>';
    echo "</url>\n";
}
echo '</urlset>' . "\n";
