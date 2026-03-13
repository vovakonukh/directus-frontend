<?php
$videos = fetchItems('video', [
    'fields' => 'id,title,slug,cover,date,playlist',
    'filter[status][_eq]' => 'published',
    'sort' => '-date'
]);
?>

<?php 
$breadcrumbs = [
    ['title' => 'Видео']
];
include __DIR__ . '/../includes/breadcrumbs.php';
?>

<section>
    <h1>Видео</h1>
    <div class="video_gallery">
        <?php if (!empty($videos)): ?>
            <?php foreach ($videos as $video): ?>
                <a href="/video/<?= $video['slug'] ?>" class="video_card">
                    <?php if (!empty($video['cover'])): ?>
                        <img src="<?= getAssetUrl($video['cover']) ?>?width=400&format=webp" alt="<?= htmlspecialchars($video['title']) ?>">
                    <?php endif; ?>
                    <span><?= htmlspecialchars(mb_strimwidth($video['title'], 0, 55, '...')) ?></span>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>