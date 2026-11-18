<?php
// Блок фильтров и сортировки для каталогов (projects, finished). По умолчанию свёрнут:
// видны счётчик, сортировка и кнопка «Показать фильтры».
// Перед include задать:
//   $filterTotal — общее количество карточек
//   $sortOptions — варианты сортировки: ['value' => 'Подпись'], первый — по умолчанию.
//                  value: 'popular' (исходный порядок с сервера) или «атрибут_asc» / «атрибут_desc»
//   $filterExtra — (необязательно) дополнительные группы в том же формате, что $filterGroups
//   $filterHide  — (необязательно) группы, которые не показывать: ['floors', 'area', 'bedrooms', 'wc']
//   $filterNouns — (необязательно) формы слова для счётчика, по умолчанию «проект»
//   $filterLinkLandings — (необязательно) подборки project_landings: кнопки этажности, площади, спален
//                  и санузлов, для которых есть подборка с теми же условиями, станут ссылками на неё
// Карточки оборачиваются в <div class="filter_item" data-index="1" data-...> —
// фильтр и сортировка читают data-атрибуты обёртки.
// type: 'eq' — значение атрибута входит в выбранные, 'range' — число в диапазоне «min-max»,
//       'all' — в атрибуте список через пробел, и в нём есть все выбранные значения («И»)
// wide: true — группа на всю ширину блока
// links: ['value' => '/url'] — кнопки-ссылки: поисковик переходит по адресу, а клик посетителя
//        перехватывает JS и переключает фильтр
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
            '0-50' => 'до 50',
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
        'options' => [1 => '1 спальня', 2 => '2 спальни', 3 => '3 спальни', 4 => '4 спальни', 5 => '5 спален'],
    ],
    'wc' => [
        'title' => 'Санузлы',
        'attr' => 'wc',
        'type' => 'eq',
        'options' => [1 => '1 санузел', 2 => '2 санузла', 3 => '3 санузла'],
    ],
], $filterExtra ?? []);
$filterGroups = array_diff_key($filterGroups, array_flip($filterHide));

if (!empty($filterLinkLandings)) {
    $chipLinks = filterChipLinks(array_map(fn($g) => array_keys($g['options']), $filterGroups), $filterLinkLandings);
    foreach ($chipLinks as $key => $links) {
        $filterGroups[$key]['links'] = $links + ($filterGroups[$key]['links'] ?? []);
    }
}

$filterConfig = [];
foreach ($filterGroups as $key => $group) {
    $filterConfig[$key] = ['attr' => $group['attr'], 'type' => $group['type']];
}

// формы слова: именительный (72 проекта) и родительный («из 72 проектов»)
$filterNouns = $filterNouns ?? [
    'nom' => ['проект', 'проекта', 'проектов'],
    'gen' => ['проекта', 'проектов', 'проектов'],
];
?>
<div class="projects__filters_section" :class="{ 'projects__filters_section--open': panelOpen }"
     x-data='projectsFilter(<?= (int)$filterTotal ?>, <?= json_encode($filterConfig) ?>, <?= json_encode(array_key_first($sortOptions)) ?>, <?= json_encode($filterNouns, JSON_UNESCAPED_UNICODE | JSON_HEX_APOS) ?>)'>

    <div class="projects__filters_bar">
        <div class="projects__filters_count" aria-live="polite" x-text="countText">
            Показано <?= (int)$filterTotal ?> <?= pluralRu((int)$filterTotal, $filterNouns['nom']) ?>
        </div>
        <button type="button" class="projects__filters_reset" x-show="activeCount && !panelOpen" x-cloak @click="resetFilters()">Сбросить</button>

        <span class="projects__filters_break"></span>

        <div class="projects__filters_sort" x-data="{ open: false, labels: <?= htmlspecialchars(json_encode($sortOptions, JSON_UNESCAPED_UNICODE)) ?> }"
             @click.outside="open = false" @keydown.escape="open = false">
            <span class="projects__filters_sort_title">Сортировка:</span>
            <button type="button" class="projects__filters_sort_button" :class="{ open }" @click="open = !open" :aria-expanded="open">
                <span x-text="labels[sort]"><?= reset($sortOptions) ?></span>
                <svg width="12" height="8" viewBox="0 0 12 8" aria-hidden="true"><path d="M1 1l5 5 5-5" fill="none" stroke="currentColor" stroke-width="1.5"/></svg>
            </button>
            <ul class="projects__filters_sort_list" x-show="open" x-transition.opacity.duration.150ms x-cloak>
                <?php foreach ($sortOptions as $value => $label): ?>
                    <li :class="{ active: sort === '<?= $value ?>' }" @click="sort = '<?= $value ?>'; open = false"><?= $label ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <button type="button" class="projects__filters_toggle" @click="panelOpen = !panelOpen"
                :aria-expanded="panelOpen" aria-controls="projects_filters_body">
            <svg width="18" height="18" viewBox="0 0 18 18" aria-hidden="true">
                <path d="M2 4.5h8M14 4.5h2M2 13.5h2M8 13.5h8" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                <circle cx="12" cy="4.5" r="2" fill="none" stroke="currentColor" stroke-width="1.6"/>
                <circle cx="6" cy="13.5" r="2" fill="none" stroke="currentColor" stroke-width="1.6"/>
            </svg>
            <!-- обе подписи лежат друг на друге, видна одна: ширина кнопки по длинной и не меняется -->
            <span class="projects__filters_toggle_long">
                <span class="projects__filters_label_show">Показать фильтры</span>
                <span class="projects__filters_label_hide">Скрыть фильтры</span>
            </span>
            <span class="projects__filters_toggle_short">Фильтры</span>
            <span class="projects__filters_badge" x-show="activeCount" x-text="activeCount" x-cloak></span>
        </button>
    </div>

    <div class="projects__filters_body" id="projects_filters_body" x-effect="$el.inert = !panelOpen">
        <div class="projects__filters_clip">
            <div class="projects__filters_panel">
                <div class="projects__filters">
                    <?php foreach ($filterGroups as $key => $group): ?>
                        <div class="projects__filter_group<?= !empty($group['wide']) ? ' projects__filter_group_wide' : '' ?>">
                            <h3><?= $group['title'] ?></h3>
                            <div class="projects__filter_inputs_wrap">
                                <?php foreach ($group['options'] as $value => $label): ?>
                                    <?php if (isset($group['links'][$value])): ?>
                                        <span>
                                            <a class="projects__filter_label" href="<?= htmlspecialchars($group['links'][$value]) ?>"
                                               :class="{ active: filters.<?= $key ?>.includes('<?= $value ?>') }"
                                               @click.prevent="toggle('<?= $key ?>', '<?= $value ?>')"><?= htmlspecialchars($label) ?></a>
                                        </span>
                                    <?php else: ?>
                                        <?php $id = $key . '_' . preg_replace('/[^a-z0-9]/i', '', $value); ?>
                                        <span>
                                            <input type="checkbox" id="<?= $id ?>" value="<?= $value ?>" x-model="filters.<?= $key ?>">
                                            <label class="projects__filter_label" for="<?= $id ?>"><?= $label ?></label>
                                        </span>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="projects__filters_footer">
                    <button type="button" class="projects__filters_reset" :disabled="!activeCount" @click="resetFilters()">Сбросить фильтры</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?= asset('/js/filters.js') ?>"></script>
