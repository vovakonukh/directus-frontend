<?php
// Избранное /favorites: ID берём из cookie (их пишет js/favorites.js).
// Запросы к Directus те же, что в каталогах, — ответы берутся из общего кэша,
// а не отдельным запросом под каждый набор ID.
require_once 'includes/landings.php';

$favIds = fn($name) => array_values(array_unique(array_filter(array_map('intval', explode('.', $_COOKIE[$name] ?? '')))));
// Последние добавленные — первыми
$pickFav = function ($items, $ids) {
    $byId = array_column($items ?? [], null, 'id');
    return array_values(array_filter(array_map(fn($id) => $byId[$id] ?? null, array_reverse($ids))));
};

$favProjects = [];
if ($ids = $favIds('fav_projects')) {
    $favProjects = $pickFav(fetchItems('projects', [
        'filter[status][_eq]' => 'published',
        'sort' => '-popular,square',
        'fields' => 'id,name,slug,main_image,main_plan,floors,length,width,bedrooms,wc,' . LANDING_PROJECT_FIELDS
    ]), $ids);
}

$favFinished = [];
if ($ids = $favIds('fav_finished')) {
    $favFinished = $pickFav(fetchItems('finished', [
        'filter[status][_eq]' => 'published',
        'sort' => '-popular,-sort',
        'fields' => 'id,name,slug,main_image,plan_1,floors,square,length,width,bedrooms,wc,finish_date,construction_period,location,otdelka'
    ]), $ids);
}

$breadcrumbs = [['title' => 'Избранное']];
include 'includes/breadcrumbs.php';
?>

<section class="page__wrap favorites" x-data>
    <h1>Избранное</h1>
    <p class="favorites__note">Избранное сохраняется только в этом браузере и только на этом устройстве.</p>

    <?php if ($favProjects): ?>
    <div x-show="$store.fav.projects.length">
        <h2 class="favorites__title">Проекты</h2>
        <div class="projects__collection">
            <?php foreach ($favProjects as $item): ?>
                <div class="filter_item" x-show="$store.fav.has('projects', <?= (int) $item['id'] ?>)">
                    <?php include 'includes/cards/project_card.php'; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($favFinished): ?>
    <div x-show="$store.fav.finished.length">
        <h2 class="favorites__title">Построенные дома</h2>
        <div class="projects__collection">
            <?php foreach ($favFinished as $item): ?>
                <div class="filter_item" x-show="$store.fav.has('finished', <?= (int) $item['id'] ?>)">
                    <?php include 'includes/cards/finished_card.php'; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php // без карточек показываем всегда: в cookie могли остаться ID снятых с публикации ?>
    <div class="favorites__empty"<?= ($favProjects || $favFinished) ? ' x-cloak x-show="!$store.fav.projects.length && !$store.fav.finished.length"' : '' ?>>
        <p>Здесь пока пусто. Нажмите ♡ на карточке проекта или построенного дома, чтобы сохранить его.</p>
        <div class="favorites__links">
            <a class="button" href="/projects">Каталог проектов</a>
            <a class="button" href="/finished">Построенные дома</a>
        </div>
    </div>
</section>
