<?php
// Блок «Лучше один раз увидеть» (экскурсия) — статичный, в Directus не выносится.
// Форма записи — Bitrix-форма 6 из коллекции forms.
// Необязательно перед include: $blockClass — доп. классы секции (например, полоса фона страницы)
$tourForm = fetchItems('forms', ['filter[id][_eq]' => 6, 'fields' => 'bitrix_code'])[0]['bitrix_code'] ?? '';
// Экскурсия: фото из assets/images, выводятся только существующие файлы
$tourPhotos = array_filter([
    '/assets/images/build__tour.webp',
    '/assets/images/build__tour_2.webp',
    '/assets/images/build__tour_3.webp',
], fn($src) => is_file($_SERVER['DOCUMENT_ROOT'] . $src));
$tourItems = [
    'Оцените планировку и высоту потолков — на фото и в 3D это сложно почувствовать',
    'Посмотрите каркас и утепление до обшивки — то, что потом скрыто в стенах',
    'Задайте вопросы прорабу прямо на объекте: про технологию, сроки и материалы',
    'Сравните готовый дом со строящимся и решите, какая комплектация нужна вам',
];
?>
<section class="stroy__tour <?= $blockClass ?? '' ?>">
    <div class="ipoteka__split">
        <div>
            <h2>Лучше один раз увидеть</h2>
            <p class="ipoteka__lead">Приглашаем на экскурсию: вы можете посмотреть вживую на построенный или строящийся дом, чтобы оценить планировку и высоту потолков.</p>
            <ul class="ipoteka__final_cta_list stroy__tour_list">
                <?php foreach ($tourItems as $t): ?>
                <li><?= htmlspecialchars($t) ?></li>
                <?php endforeach; ?>
            </ul>
            <?= $tourForm ?>
            <button type="button" class="button big">Записаться на экскурсию</button>
        </div>
        <?php if ($tourPhotos): ?>
        <div class="stroy__tour_photos stroy__tour_photos--<?= count($tourPhotos) ?>">
            <?php foreach ($tourPhotos as $src): ?>
            <a href="<?= $src ?>" data-fancybox="stroy-tour">
                <img src="<?= $src ?>" alt="Каркасный дом на экскурсии" loading="lazy">
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php unset($blockClass);
