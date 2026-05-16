<?php
$posts = fetchItems('blog', [
    'fields' => 'id,slug,pagetitle,description,blog_image,date',
    'sort' => '-date',
    'filter[status][_eq]' => 'published',
    'limit' => -1,
]);
?>

<section class="page__wrap">
    <h1>Блог</h1>

    <div class="blog_index__collection">
        <?php foreach ($posts as $post): ?>
            <a class="blog_index__card" href="/blog/<?= htmlspecialchars($post['slug']) ?>">
                <?php if (!empty($post['blog_image'])): ?>
                    <img src="<?= getAssetUrl($post['blog_image']) ?>" alt="<?= htmlspecialchars($post['pagetitle']) ?>" />
                <?php endif; ?>
                <span class="blog_index__card_header"><?= htmlspecialchars($post['pagetitle']) ?></span>
                <?php if (!empty($post['description'])): ?>
                    <span class="blog_index__card_descr"><?= htmlspecialchars($post['description']) ?></span>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </div>
</section>