<?php
$projects = fetchItems('projects', [
    'filter[status][_eq]' => 'published',
    'sort' => '-popular,square',
    'fields' => 'id,name,slug,main_image,main_plan,floors,square,length,width,bedrooms,wc,price_tk'
]);

$breadcrumbs = [
    ['title' => 'Каталог проектов']
];
include 'includes/breadcrumbs.php';
?>

<section class="page__wrap">
    <h1><?= htmlspecialchars(!empty($seo['h1']) ? $seo['h1'] : 'Проекты каркасных домов') ?></h1>

    <?php
    $filterTotal = count($projects);
    include 'includes/filters.php';
    ?>

    <div class="projects__collection">
        <?php foreach ($projects as $item): ?>
            <?php include 'includes/cards/project_card.php'; ?>
        <?php endforeach; ?>
    </div>

    <div id="no-results" class="hidden">Ничего не найдено</div>

    <?php include 'includes/seo_text.php'; ?>
</section>
