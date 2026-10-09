<section class="breadcrumbs">
    <a href="/">Главная</a>
    <?php if (!empty($breadcrumbs)): ?>
        <?php foreach ($breadcrumbs as $crumb): ?>
            <?php if (isset($crumb['url'])): ?>
                <a href="<?= $crumb['url'] ?>">- <?= $crumb['title'] ?></a>
            <?php else: ?>
                <a>- <?= $crumb['title'] ?></a>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</section>

<?php
// Микроразметка BreadcrumbList (JSON-LD) — строится из того же массива $breadcrumbs.
// У последней крошки url нет, для неё берётся адрес текущей страницы ($canonical из index.php).
$ldItems = [['@type' => 'ListItem', 'position' => 1, 'name' => 'Главная', 'item' => $siteUrl . '/']];
foreach ($breadcrumbs ?? [] as $i => $crumb) {
    $ldItems[] = [
        '@type' => 'ListItem',
        'position' => $i + 2,
        'name' => $crumb['title'],
        'item' => isset($crumb['url']) ? $siteUrl . $crumb['url'] : $canonical,
    ];
}
?>
<script type="application/ld+json"><?= json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => $ldItems,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) ?></script>

<!-- Добавление хлебных крошек на страницу

$breadcrumbs = [
    ['title' => 'Проекты', 'url' => '/projects'],
    ['title' => $project['name']]
];
include 'includes/breadcrumbs.php';

Все уровни с параметром url
Последний без

-->