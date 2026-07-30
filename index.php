<?php
require_once 'includes/api.php';
require_once 'includes/helpers.php';



// Получаем путь из URL
$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

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
    default:
        $page = '404';
        $title = 'Страница не найдена';
        break;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <link rel="icon" href="/assets/icons/favicon.ico">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/carousel/carousel.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css">
    <link rel="stylesheet" href="/css/main_styles.css">
    <link rel="stylesheet" href="/css/style.css">
</head>

<?php
$contacts = fetchItems('contacts', ['fields' => '*']);
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
        default:
            echo '<section class="page__wrap"><h1>404 — Страница не найдена</h1></section>';
    }
    ?>

    <?php include 'includes/footer.php'; ?>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/carousel/carousel.umd.js"></script>
    <script>Fancybox.bind('[data-fancybox]');</script>
</body>
</html>