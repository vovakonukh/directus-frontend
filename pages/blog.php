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
            <?php include 'includes/cards/blog_card.php'; ?>
        <?php endforeach; ?>
    </div>
</section>