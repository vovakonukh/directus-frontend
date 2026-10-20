<?php
$items = fetchItems('finished', [
    'filter[status][_eq]' => 'published',
    'sort' => '-popular,-sort',
    'fields' => 'id,name,slug,main_image,plan_1,floors,square,length,width,bedrooms,wc,finish_date,construction_period,location'
]);

$breadcrumbs = [
    ['title' => 'Построенные дома']
];
include 'includes/breadcrumbs.php';
?>

<section class="page__wrap">
    <h1><?= htmlspecialchars(!empty($seo['h1']) ? $seo['h1'] : 'Построенные дома') ?></h1>

    <?php
    $filterTotal = count($items);
    include 'includes/filters.php';
    ?>

    <div class="projects__collection">
        <?php foreach ($items as $item): ?>
            <?php include 'includes/cards/finished_card.php'; ?>
        <?php endforeach; ?>
    </div>

    <div id="no-results" class="hidden">Ничего не найдено</div>
    <?php include 'includes/seo_text.php'; ?>
</section>
