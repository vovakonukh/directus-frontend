<?php
$items = fetchItems('finished', [
    'filter[status][_eq]' => 'published',
    'sort' => '-popular,-sort',
    'fields' => 'id,name,slug,main_image,plan_1,floors,square,length,width,bedrooms,wc,finish_date,construction_period,location,otdelka'
]);

// Локация и отделка — пока скрыты, данные не готовы. Чтобы включить: раскомментировать блок.
// Для локации нужно поле region в Directus (выпадающий список lo / mo) и data-region в карточке
/*
$filterExtra = [
    'region' => [
        'title' => 'Локация',
        'attr' => 'region',
        'type' => 'eq',
        'options' => ['lo' => 'Ленинградская обл.', 'mo' => 'Московская обл.'],
    ],
    'otdelka' => [
        'title' => 'Отделка',
        'attr' => 'otdelka',
        'type' => 'eq',
        'options' => ['no_finishing' => 'Без отделки', 'white_box' => 'White Box', 'furniture' => 'С мебелью'],
    ],
];
*/
$sortOptions = [
    'popular' => 'По популярности',
    'date_desc' => 'Сначала новые',
    'date_asc' => 'Сначала старые',
    'area_asc' => 'Площадь сначала маленькие',
    'area_desc' => 'Площадь сначала большие',
];

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
        <?php foreach ($items as $i => $item): ?>
            <div class="filter_item"
                 data-index="<?= $i + 1 ?>"
                 data-floors="<?= $item['floors'] ?>"
                 data-area="<?= $item['square'] ?>"
                 data-bedrooms="<?= $item['bedrooms'] ?>"
                 data-wc="<?= $item['wc'] ?>"
                 data-date="<?= finishDateSortKey($item['finish_date'] ?? '') ?>"
                 data-otdelka="<?= htmlspecialchars($item['otdelka'] ?? '') ?>">
                <?php include 'includes/cards/finished_card.php'; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div id="no-results" class="hidden">Ничего не найдено</div>
    <?php include 'includes/seo_text.php'; ?>
</section>
