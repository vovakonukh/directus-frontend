<?php
require_once 'includes/api.php';
require_once 'includes/helpers.php';



// Получаем путь из URL
$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

// sitemap.xml генерируется на лету, без HTML-обёртки
if ($uri === 'sitemap.xml') {
    require 'includes/sitemap.php';
    exit;
}

// robots.txt тоже динамический — чтобы домен в ссылке на sitemap подставлялся сам
if ($uri === 'robots.txt') {
    $siteUrl = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
    header('Content-Type: text/plain; charset=utf-8');
    echo "User-agent: *\n";
    echo "Disallow: /thank-you\n";
    echo "Disallow: /links\n";
    echo "\n";
    echo "Sitemap: $siteUrl/sitemap.xml\n";
    exit;
}

// Сброс кэша API — вызывается из Directus Flow при изменении записей.
// Ключ лежит в includes/cache_key.php (не в git); без файла адрес не работает.
if ($uri === 'cache-clear') {
    $keyFile = __DIR__ . '/includes/cache_key.php';
    $key = is_file($keyFile) ? require $keyFile : '';
    header('Content-Type: text/plain; charset=utf-8');
    header('Cache-Control: no-store');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !is_string($key) || $key === ''
        || !hash_equals($key, $_SERVER['HTTP_X_CACHE_KEY'] ?? '')) {
        http_response_code(403);
        echo "forbidden\n";
        exit;
    }
    $files = glob(API_CACHE_DIR . '/*.json') ?: [];
    array_map('unlink', $files);
    echo 'cleared: ' . count($files) . "\n";
    exit;
}

// Роутинг
switch (true) {
    case $uri === '':
        $page = 'home';
        $title = 'Строительная компания Класс Хаус';
        break;
    case $uri === 'contacts':
        $page = 'contacts';
        $title = 'Контакты | Строительная компания Класс Хаус';
        break;
    case $uri === 'links':
        $page = 'links';
        $title = 'Ссылки | Строительная компания Класс Хаус';
        break;
    case $uri === 'projects':
        $page = 'projects';
        $title = 'Каталог проектов | Строительная компания Класс Хаус';
        break;
    case $uri === 'finished':
        $page = 'finished';
        $title = 'Построенные дома | Строительная компания Класс Хаус';
        break;
    case preg_match('#^finished/([a-z0-9_-]+)$#', $uri, $m) === 1:
        $page = 'finished_item';
        $slug = $m[1];
        $title = 'Построенный дом | Строительная компания Класс Хаус';
        break;
    case preg_match('#^projects/([a-z0-9_-]+)$#', $uri, $m) === 1:
        $page = 'projects_item';
        $slug = $m[1];
        $title = 'Проект | Строительная компания Класс Хаус';
        break;
    case $uri === 'video':
        $page = 'video';
        $title = 'Видео | Строительная компания Класс Хаус';
        break;
    case preg_match('#^video/([a-z0-9_-]+)$#', $uri, $m) === 1:
        $page = 'video_item';
        $slug = $m[1];
        $title = 'Видео | Строительная компания Класс Хаус';
        break;
    case $uri === 'ipoteka':
        $page = 'ipoteka';
        $title = 'Ипотека на строительство дома | Строительная компания Класс Хаус';
        break;
    case $uri === 'services/stroitelstvo':
            $page = 'services/stroitelstvo';
            $title = 'Строительство домов | Строительная компания Класс Хаус';
            $bodyClass = 'page__stroitelstvo';
            break;
    case $uri === 'services/inzhenerka':
        $page = 'services/inzhenerka';
        $title = 'Инженерные сети | Строительная компания Класс Хаус';
        $bodyClass = 'page__stroitelstvo';
        break;
    case $uri === 'feedback':
        $page = 'feedback';
        $title = 'Отзывы | Строительная компания Класс Хаус';
        $bodyClass = 'feedback-page';
        break;
    case $uri === 'blog':
        $page = 'blog';
        $title = 'Блог | Строительная компания Класс Хаус';
        break;
    case preg_match('#^blog/([a-z0-9_-]+)$#', $uri, $m) === 1:
        $page = 'blog_item';
        $slug = $m[1];
        $title = 'Статья | Строительная компания Класс Хаус';
        $bodyClass = 'page__blog_item';
        break;
    case $uri === 'services':
        $page = 'services';
        $title = 'Услуги | Строительная компания Класс Хаус';
        break;
    case $uri === 'services/fundament':
        $page = 'services/fundament';
        $title = 'Фундаменты | Строительная компания Класс Хаус';
        $bodyClass = 'page__service';
        break;
    case $uri === 'postavshhikam':
        $page = 'postavshhikam';
        $title = 'Поставщикам | Строительная компания Класс Хаус';
        break;
    case $uri === 'policy':
        $page = 'policy';
        $title = 'Политика обработки персональных данных';
        break;
    case $uri === 'thank-you':
        $page = 'thank-you';
        $title = 'Спасибо за заявку | Строительная компания Класс Хаус';
        break;
    default:
        $page = '404';
        $title = 'Страница не найдена';
        break;
}

// Страницы элементов: существование записи проверяем до вывода HTML,
// иначе код ответа 404 уже не отправить
$itemCollections = [
    'projects_item' => 'projects',
    'finished_item' => 'finished',
    'blog_item'     => 'blog',
    'video_item'    => 'video',
];
if (isset($itemCollections[$page])) {
    $exists = fetchItems($itemCollections[$page], [
        'filter[slug][_eq]' => $slug,
        'filter[status][_eq]' => 'published',
        'fields' => 'id',
        'limit' => 1
    ]);
    if ($exists === null) {
        // API недоступен и копии в кэше нет — временная ошибка, а не «страницы не существует»
        http_response_code(503);
        header('Retry-After: 3600');
    } elseif (empty($exists)) {
        $page = '404';
        $title = 'Страница не найдена';
    }
}
if ($page === '404') {
    http_response_code(404);
}

// SEO для страниц-списков из коллекции pages_seo (route = $uri)
$seo = null;
// у главной route пустой — фильтр по пустой строке Directus отклоняет с ошибкой 400
if (!isset($slug) && $page !== '404' && $uri !== '') {
    $seo = fetchItems('pages_seo', [
        'filter[route][_eq]' => $uri,
        'fields' => 'h1,seo_title,seo_description,seo_image,seo_noindex,seo_text',
        'limit' => 1
    ])[0] ?? null;
}
if (!empty($seo['seo_title'])) $title = $seo['seo_title'];
$description = $seo['seo_description'] ?? '';
$ogImage = !empty($seo['seo_image']) ? getAssetUrl($seo['seo_image']) . '?width=1200&height=630&fit=cover' : '';
$siteUrl = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
$canonical = $siteUrl . '/' . $uri;

// Браузерный кэш HTML: 60 с без запроса к серверу, затем до 10 минут — сохранённая копия
// с обновлением в фоне. На localhost выключен (как и кэш API).
if (API_CACHE_TTL > 0 && http_response_code() === 200 && $page !== 'thank-you') {
    header('Cache-Control: max-age=60, stale-while-revalidate=600');
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?= htmlspecialchars($title) ?></title>
<?php if ($description): ?>
    <meta name="description" content="<?= htmlspecialchars($description) ?>">
<?php endif; ?>
<?php if (!empty($seo['seo_noindex'])): ?>
    <meta name="robots" content="noindex, follow">
<?php endif; ?>
    <link rel="canonical" href="<?= htmlspecialchars($canonical) ?>">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Класс Хаус">
    <meta property="og:title" content="<?= htmlspecialchars($title) ?>">
<?php if ($description): ?>
    <meta property="og:description" content="<?= htmlspecialchars($description) ?>">
<?php endif; ?>
    <meta property="og:url" content="<?= htmlspecialchars($canonical) ?>">
<?php if ($ogImage): ?>
    <meta property="og:image" content="<?= htmlspecialchars($ogImage) ?>">
<?php endif; ?>
    <link rel="icon" href="/assets/icons/favicon.ico">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0.36/dist/carousel/carousel.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0.36/dist/fancybox/fancybox.css">
    <link rel="stylesheet" href="/css/main_styles.css?v=<?= filemtime(__DIR__ . '/css/main_styles.css') ?>">
    <link rel="stylesheet" href="/css/style.css?v=<?= filemtime(__DIR__ . '/css/style.css') ?>">
</head>

<?php
$contacts = fetchItems('contacts', ['fields' => '*']);
$shiftTelegram = getShiftTelegramLink($contacts['telegram_message'] ?? 'https://t.me/classhouse');
?>

<body class="<?= $bodyClass ?? '' ?>">
    <?php include 'includes/header.php'; ?>

    <?php
    switch ($page) {
        case 'home':
            include 'pages/home.php';
            break;
        case 'contacts':
            include 'pages/contacts.php';
            break;
        case 'links':
            include 'pages/links.php';
            break;
        case 'projects':
            include 'pages/projects.php';
            break;
        case 'projects_item':
            include 'pages/projects_item.php';
            break;
        case 'finished':
            include 'pages/finished.php';
            break;
        case 'finished_item':
            include 'pages/finished_item.php';
            break;
        case 'video':
            include 'pages/video.php';
            break;
        case 'video_item':
            include 'pages/video_item.php';
            break;
        case 'ipoteka':
            include 'pages/ipoteka.php';
            break;
        case 'services/stroitelstvo':
            include 'pages/services/stroitelstvo.php';
            break;
        case 'services/inzhenerka':
            include 'pages/services/inzhenerka.php';
            break;
        case 'feedback':
            include 'pages/feedback.php';
            break;
        case 'blog':
            include 'pages/blog.php';
            break;
        case 'blog_item':
            include 'pages/blog_item.php';
            break;
        case 'services':
            include 'pages/services.php';
            break;
        case 'services/fundament':
            include 'pages/services/fundament.php';
            break;
        case 'postavshhikam':
            include 'pages/postavshhikam.php';
            break;
        case 'policy':
            include 'pages/policy.php';
            break;
        case 'thank-you':
            include 'pages/thank-you.php';
            break;
        default:
            echo '<section class="page__wrap"><h1>404 — Страница не найдена</h1></section>';
    }
    ?>

    <?php include 'includes/footer.php'; ?>
    <?php include 'includes/bottom_menu.php'; ?>

    <!-- плагин Intersect подключается до ядра Alpine -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/intersect@3.17.4/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.17.4/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0.36/dist/fancybox/fancybox.umd.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0.36/dist/carousel/carousel.umd.js"></script>
    <script>Fancybox.bind('[data-fancybox]');</script>
</body>
</html>