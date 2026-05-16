<?php
$services = fetchItems('services', [
    'fields' => 'id,title,slug,image,sort',
    'filter[status][_eq]' => 'published',
    'sort' => 'sort,id',
]);
?>

<section class="page__wrap">
    <h1>Услуги</h1>

    <div class="service_gallery">
        <?php foreach ($services as $s): ?>
            <a href="/services/<?= htmlspecialchars($s['slug']) ?>" class="service_card">
                <?php if (!empty($s['image'])): ?>
                    <img src="<?= getAssetUrl($s['image']) ?>" alt="<?= htmlspecialchars($s['title']) ?>" />
                <?php endif; ?>
                <span><?= htmlspecialchars($s['title']) ?></span>
            </a>
        <?php endforeach; ?>
    </div>
</section>