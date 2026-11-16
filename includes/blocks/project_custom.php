<?php
// Блок «Бесплатно доработаем любой проект» — статичный, в Directus не выносится.
// Форма — Bitrix-форма 5 из коллекции forms (click-форма: вешается на кнопку сразу после кода формы).
// Необязательно перед include:
//   $blockClass  — доп. классы (например, отступы под конкретную страницу)
//   $customTitle — свой заголовок (например, на странице проекта «этот» вместо «любой»)
// В каталоге стоит внутри .projects__collection: data-insert-after — после какой видимой карточки
// держать блок при фильтре/сортировке (js/filters.js). Вне каталога атрибут ни на что не влияет.
$customForm = fetchItems('forms', ['filter[id][_eq]' => 5, 'fields' => 'bitrix_code'])[0]['bitrix_code'] ?? '';
?>
<div class="project_custom <?= $blockClass ?? '' ?>" data-insert-after="4">
    <img class="project_custom__image" src="<?= asset('/assets/images/project_custom.webp') ?>" alt="" loading="lazy" width="954" height="512">
    <div class="project_custom__body">
        <p class="project_custom__title"><?= htmlspecialchars($customTitle ?? 'Бесплатно доработаем любой проект под вашу семью') ?></p>
        <p class="project_custom__text">Нравится дом, но хочется кабинет, спальню побольше или окно в другом месте? Архитектор внесёт изменения и сразу пересчитает смету — для наших клиентов это бесплатно.</p>
        <?php if ($customForm): ?>
            <?= $customForm ?>
            <button type="button" class="button big project_custom__button">Записаться к архитектору</button>
        <?php endif; ?>
    </div>
</div>
<?php unset($blockClass, $customTitle);
