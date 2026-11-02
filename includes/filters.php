<?php
// Блок фильтров и сортировки для каталогов (projects, finished).
// Перед include задать:
//   $filterTotal — общее количество карточек
//   $sortOptions — варианты сортировки: ['value' => 'Подпись'], первый — по умолчанию.
//                  value: 'popular' (исходный порядок с сервера) или «атрибут_asc» / «атрибут_desc»
//   $filterExtra — (необязательно) дополнительные группы в том же формате, что $filterGroups
//   $filterHide  — (необязательно) группы, которые не показывать: ['floors', 'area', 'bedrooms', 'wc']
// Карточки оборачиваются в <div class="filter_item" data-index="1" data-...> —
// фильтр и сортировка читают data-атрибуты обёртки.
// type: 'eq' — значение атрибута входит в выбранные, 'range' — число в диапазоне «min-max»
// wide: true — группа на всю ширину блока
$filterHide = $filterHide ?? [];

$filterGroups = array_merge([
    'floors' => [
        'title' => 'Этажность',
        'attr' => 'floors',
        'type' => 'eq',
        'options' => [1 => '1 этаж', 2 => '2 этажа'],
    ],
    'area' => [
        'title' => 'Площадь',
        'attr' => 'area',
        'type' => 'range',
        'options' => [
            '50-70' => 'от 50 до 70',
            '70-100' => 'от 70 до 100',
            '100-130' => 'от 100 до 130',
            '130-170' => 'от 130 до 170',
        ],
    ],
    'bedrooms' => [
        'title' => 'Спальни',
        'attr' => 'bedrooms',
        'type' => 'eq',
        'options' => [1 => '1 спальня', 2 => '2 спальни', 3 => '3 спальни', 4 => '4 спальни'],
    ],
    'wc' => [
        'title' => 'Санузлы',
        'attr' => 'wc',
        'type' => 'eq',
        'options' => [1 => '1 санузел', 2 => '2 санузла', 3 => '3 санузла'],
    ],
], $filterExtra ?? []);
$filterGroups = array_diff_key($filterGroups, array_flip($filterHide));

$filterConfig = [];
foreach ($filterGroups as $key => $group) {
    $filterConfig[$key] = ['attr' => $group['attr'], 'type' => $group['type']];
}
?>
<div class="projects__filters_section"
     x-data='projectsFilter(<?= (int)$filterTotal ?>, <?= json_encode($filterConfig) ?>, <?= json_encode(array_key_first($sortOptions)) ?>)'>
    <div class="projects__filters">
        <?php foreach ($filterGroups as $key => $group): ?>
            <div class="projects__filter_group<?= !empty($group['wide']) ? ' projects__filter_group_wide' : '' ?>">
                <h3><?= $group['title'] ?></h3>
                <div class="projects__filter_inputs_wrap">
                    <?php foreach ($group['options'] as $value => $label): ?>
                        <?php $id = $key . '_' . preg_replace('/[^a-z0-9]/i', '', $value); ?>
                        <span>
                            <input type="checkbox" id="<?= $id ?>" value="<?= $value ?>" x-model="filters.<?= $key ?>">
                            <label class="projects__filter_label" for="<?= $id ?>"><?= $label ?></label>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="projects__filters_end">
        <div class="projects__filters_cards_amount">
            Показано <span x-text="visibleCount"><?= (int)$filterTotal ?></span> из <span><?= (int)$filterTotal ?></span>
        </div>
        <div class="projects__filters_sort" x-data="{ open: false, labels: <?= htmlspecialchars(json_encode($sortOptions, JSON_UNESCAPED_UNICODE)) ?> }"
             @click.outside="open = false" @keydown.escape="open = false">
            <span class="projects__filters_sort_title">Сортировка:</span>
            <button type="button" class="projects__filters_sort_button" :class="{ open }" @click="open = !open" :aria-expanded="open">
                <span x-text="labels[sort]"><?= reset($sortOptions) ?></span>
                <svg width="12" height="8" viewBox="0 0 12 8"><path d="M1 1l5 5 5-5" fill="none" stroke="currentColor" stroke-width="1.5"/></svg>
            </button>
            <ul class="projects__filters_sort_list" x-show="open" x-transition.opacity.duration.150ms x-cloak>
                <?php foreach ($sortOptions as $value => $label): ?>
                    <li :class="{ active: sort === '<?= $value ?>' }" @click="sort = '<?= $value ?>'; open = false"><?= $label ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="projects__filters_reset_button" @click="resetFilters()">Сбросить фильтры</div>
    </div>
</div>
<script src="<?= asset('/js/filters.js') ?>"></script>
