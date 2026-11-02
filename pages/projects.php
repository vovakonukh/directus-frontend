<?php
$projects = fetchItems('projects', [
    'filter[status][_eq]' => 'published',
    'sort' => '-popular,square',
    'fields' => 'id,name,slug,main_image,main_plan,floors,square,length,width,bedrooms,wc,price_tk'
]);

// Подборки из project_landings — пока скрыты. Чтобы включить: раскомментировать блок,
// связать проекты с project_landings в Directus и вывести slug подборок в data-landings карточки
/*
$filterExtra = [
    'landings' => [
        'title' => 'Подборки',
        'attr' => 'landings',
        'type' => 'eq',
        'wide' => true,
        'options' => [
            'pmzh' => 'Для постоянного проживания',
            'second_light' => 'Со вторым светом',
            'dacha' => 'Дачные дома',
            'terrace' => 'С большой террасой',
            'big' => 'Большие дома',
            'small' => 'Маленькие дома',
            'cheap' => 'Недорогие дома',
            'sauna' => 'С сауной',
            'shed_roof' => 'С односкатной кровлей',
            'multilevel_roof' => 'С разноуровневой кровлей',
            'panoramic' => 'С панорамными окнами',
        ],
    ],
];
*/
$sortOptions = [
    'popular' => 'По популярности',
    'price_asc' => 'Сначала дешевле',
    'price_desc' => 'Сначала дороже',
    'area_asc' => 'Площадь сначала маленькие',
    'area_desc' => 'Площадь сначала большие',
];

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
        <?php foreach ($projects as $i => $item): ?>
            <div class="filter_item"
                 data-index="<?= $i + 1 ?>"
                 data-floors="<?= $item['floors'] ?>"
                 data-area="<?= $item['square'] ?>"
                 data-bedrooms="<?= $item['bedrooms'] ?>"
                 data-wc="<?= $item['wc'] ?>"
                 data-price="<?= $item['price_tk'] ?>">
                <?php include 'includes/cards/project_card.php'; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div id="no-results" class="hidden">Ничего не найдено</div>

    <?php include 'includes/seo_text.php'; ?>
</section>
