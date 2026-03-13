<?php
$items = fetchItems('video', [
    'fields' => 'id,title,slug,video_code,cover,date,description,playlist',
    'filter[slug][_eq]' => $slug,
    'filter[status][_eq]' => 'published',
    'limit' => 1
]);

if (empty($items)) {
    echo '<section class="page__wrap"><h1>Видео не найдено</h1></section>';
    return;
}

$video = $items[0];

// Рекомендованные видео (последние 5, кроме текущего)
$recommended = fetchItems('video', [
    'fields' => 'id,title,slug,cover,date',
    'filter[status][_eq]' => 'published',
    'filter[id][_neq]' => $video['id'],
    'sort' => '-date',
    'limit' => 5
]);
?>

<?php
$breadcrumbs = [
    ['title' => 'Видео', 'url' => '/video'],
    ['title' => $video['title']]
];
include __DIR__ . '/../includes/breadcrumbs.php';
?>

<section>
    <div class="video__wrap">
        <div class="video__main">
            <div class="video__player">
                <?= $video['video_code'] ?>
            </div>
            <div class="video__description">
                <h1><?= htmlspecialchars($video['title']) ?></h1>

                <div class="video__channel_wrap">
                    <a href="<?= htmlspecialchars($contacts['vk_group'] ?? '#') ?>" class="video__author">
                        <img src="/assets/images/logo/logo_squre_colored.svg" alt="">
                        <div>
                            <strong>Каркасные дома Класс Хаус</strong>
                            <p><?= htmlspecialchars($contacts['subscribers_amount'] ?? '') ?> подписчиков</p>
                        </div>
                    </a>
                </div>

                <div x-data="{ open: false }" class="video__description_pane" :style="open ? 'height:auto' : 'height:100px'">
                    <?= $video['description'] ?>
                    <span x-show="!open" @click="open = true" style="cursor:pointer">Развернуть</span>
                    <span x-show="open" @click="open = false" style="cursor:pointer">Свернуть</span>
                </div>
            </div>
        </div>

        <div class="video__recommend">
            <h2>Смотрите также</h2>
            <div class="video__recommend_list">
                <?php if (!empty($recommended)): ?>
                    <?php foreach ($recommended as $rec): ?>
                        <div>
                            <?php if (!empty($rec['cover'])): ?>
                                <img src="<?= getAssetUrl($rec['cover']) ?>?width=300&format=webp" alt="">
                            <?php endif; ?>
                            <div>
                                <a href="/video/<?= $rec['slug'] ?>"><?= htmlspecialchars(mb_strimwidth($rec['title'], 0, 55, '...')) ?></a>
                                <span><?= !empty($rec['date']) ? date('d.m.Y', strtotime($rec['date'])) : '' ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>