<?php
// Каталог проектов /projects и страницы подборок /projects/{slug}
// (для подборки index.php задаёт $landing — запись из project_landings)
require_once 'includes/landings.php';

// та же выборка, что для шорткодов {{landing_count}} и {{landing_price_from}}
$projects = (isset($landing) ? landingProjects($landing) : fetchCatalogProjects()) ?? [];

// Кнопки подборок: на странице подборки текущую не показываем — все проекты и так из неё
$landings = fetchLandings();
$filterLandings = array_filter($landings, fn($l) => !empty($l['show_in_filter']) && (!isset($landing) || $l['id'] !== $landing['id']));
if ($filterLandings) {
    $filterExtra = [
        'landings' => [
            'title' => 'Подборки',
            'attr' => 'landings',
            'type' => 'all',
            'wide' => true,
            'options' => array_column($filterLandings, 'name', 'slug'),
            'links' => array_map(fn($slug) => '/projects/' . $slug, array_column($filterLandings, 'slug', 'slug')),
        ],
    ];
}
// Кнопки этажности, площади, спален и санузлов — ссылки на подборки с теми же условиями.
// На странице подборки группы с её условиями скрываем: все проекты и так под них подходят
$filterLinkLandings = $landings;
$filterHide = isset($landing) ? landingHiddenGroups($landing) : [];
$sortOptions = [
    'popular' => 'По популярности',
    'price_asc' => 'Сначала дешевле',
    'price_desc' => 'Сначала дороже',
    'area_asc' => 'Сначала маленькие',
    'area_desc' => 'Сначала большие',
];

$breadcrumbs = isset($landing)
    ? [['title' => 'Каталог проектов', 'url' => '/projects'], ['title' => $landing['name']]]
    : [['title' => 'Каталог проектов']];
include 'includes/breadcrumbs.php';
?>

<section class="page__wrap">
    <h1><?= htmlspecialchars(!empty($seo['h1']) ? renderVars($seo['h1']) : (isset($landing) ? $landing['name'] : 'Проекты каркасных домов')) ?></h1>

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
                 data-price="<?= $item['price_tk_auto'] ?>"
                 data-landings="<?= implode(' ', projectLandingSlugs($item, $landings)) ?>">
                <?php include 'includes/cards/project_card.php'; ?>
            </div>
            <?php if ($i === 3) include 'includes/blocks/project_custom.php'; ?>
        <?php endforeach; ?>
    </div>

    <div id="no-results" class="hidden">Ничего не найдено</div>

    <?php include 'includes/seo_text.php'; ?>
</section>
