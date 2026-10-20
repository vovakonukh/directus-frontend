<?php
// Блок фильтров для каталогов (projects, finished, SEO-страницы).
// Перед include задать:
//   $filterTotal — общее количество карточек
//   $filterHide  — (необязательно) группы, которые не показывать: ['floors', 'area', 'bedrooms', 'wc']
$filterHide = $filterHide ?? [];

$filterGroups = [
    'floors' => [
        'title' => 'Этажность',
        'prefix' => 'floor_',
        'options' => [1 => '1 этаж', 2 => '2 этажа'],
    ],
    'area' => [
        'title' => 'Площадь',
        'prefix' => 'sq_',
        'options' => [
            '50-70' => 'от 50 до 70',
            '70-100' => 'от 70 до 100',
            '100-130' => 'от 100 до 130',
            '130-170' => 'от 130 до 170',
        ],
    ],
    'bedrooms' => [
        'title' => 'Спальни',
        'prefix' => 'bed_',
        'options' => [1 => '1 спальня', 2 => '2 спальни', 3 => '3 спальни', 4 => '4 спальни'],
    ],
    'wc' => [
        'title' => 'Санузлы',
        'prefix' => 'wc_',
        'options' => [1 => '1 санузел', 2 => '2 санузла', 3 => '3 санузла'],
    ],
];
?>
<div class="projects__filters_section" x-data="projectsFilter(<?= (int)$filterTotal ?>)">
    <div class="projects__filters">
        <?php foreach ($filterGroups as $key => $group): ?>
            <?php if (in_array($key, $filterHide)) continue; ?>
            <div class="projects__filter_group">
                <h3><?= $group['title'] ?></h3>
                <div class="projects__filter_inputs_wrap">
                    <?php foreach ($group['options'] as $value => $label): ?>
                        <?php $id = $group['prefix'] . strtok($value, '-'); ?>
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
        <div class="projects__filters_reset_button" @click="resetFilters()">Сбросить фильтры</div>
    </div>
</div>
<script src="/js/filters.js"></script>
