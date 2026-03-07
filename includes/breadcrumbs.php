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

<!-- Добавление хлебных крошек на страницу

$breadcrumbs = [
    ['title' => 'Проекты', 'url' => '/projects'],
    ['title' => $project['name']]
];
include 'includes/breadcrumbs.php';

Все уровни с параметром url
Последний без

-->