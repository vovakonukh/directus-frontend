<?php
// Подборки проектов (коллекция project_landings): кнопки в фильтре каталога и страницы /projects/{slug}.
// Состав подборки:
//   - если в подборке заполнено хоть одно условие (LANDING_RULES) — проект входит, когда подходит
//     под все заполненные условия; ручные отметки не учитываются;
//   - иначе — проекты, отмеченные в подборке вручную (связь projects).

// Поля-условия подборки
const LANDING_RULES = ['floors', 'bedrooms', 'wc', 'square_min', 'square_max', 'price_max', 'square_terrace_min', 'second_light'];

// Все опубликованные подборки. fields=* — чтобы новое поле-условие не ломало запрос, пока его нет в Directus
function fetchLandings() {
    static $landings = null;
    if ($landings !== null) return $landings;

    $landings = fetchItems('project_landings', [
        'filter[status][_eq]' => 'published',
        'sort' => 'sort,id',
        'fields' => '*,projects.projects_id',
        'limit' => -1,
    ]) ?? [];
    foreach ($landings as &$l) {
        $l['project_ids'] = array_column($l['projects'] ?? [], 'projects_id');
    }
    unset($l);
    return $landings = array_values(array_filter($landings, fn($l) => !empty($l['slug'])));
}

// Поля проекта, нужные для проверки условий
const LANDING_PROJECT_FIELDS = 'id,floors,bedrooms,wc,square,price_tk_auto,square_terrace,second_light';

// Заполненные условия подборки: ['floors' => 1, 'square_min' => 50, ...]
function landingRules(array $landing): array {
    $rules = [];
    foreach (LANDING_RULES as $field) {
        if (!empty($landing[$field])) $rules[$field] = $landing[$field];
    }
    return $rules;
}

function projectInLanding(array $project, array $landing): bool {
    $rules = landingRules($landing);
    if (!$rules) {
        return in_array($project['id'], $landing['project_ids']);
    }
    foreach (['floors', 'bedrooms', 'wc'] as $field) {
        if (isset($rules[$field]) && (int) ($project[$field] ?? 0) !== (int) $rules[$field]) return false;
    }
    $square = (float) ($project['square'] ?? 0);
    $price = (float) ($project['price_tk_auto'] ?? 0);
    // Диапазон «от 70 до 100» — 70 включительно, 100 нет: дом ровно на границе попадает только в один
    // диапазон (как в JS-фильтре). Одиночные пороги «до 80», «от 120» — включительно
    if (isset($rules['square_min']) && $square < $rules['square_min']) return false;
    if (isset($rules['square_max'])) {
        $isRange = isset($rules['square_min']);
        if ($isRange ? $square >= $rules['square_max'] : $square > $rules['square_max']) return false;
    }
    // проект без цены в «Недорогие» не попадает
    if (isset($rules['price_max']) && ($price <= 0 || $price > $rules['price_max'])) return false;
    if (isset($rules['square_terrace_min']) && (float) ($project['square_terrace'] ?? 0) < $rules['square_terrace_min']) return false;
    if (isset($rules['second_light']) && empty($project['second_light'])) return false;
    return true;
}

// Slug всех подборок проекта — для data-landings карточки
function projectLandingSlugs(array $project, array $landings): array {
    $slugs = [];
    foreach ($landings as $l) {
        if (projectInLanding($project, $l)) $slugs[] = $l['slug'];
    }
    return $slugs;
}

// Условия, которым соответствует кнопка фильтра: «1 этаж» → floors 1, «от 50 до 70» → square 50–70
function filterChipRules(string $group, string $value): array {
    switch ($group) {
        case 'floors':
        case 'bedrooms':
        case 'wc':
            return [$group => (int) $value];
        case 'area':
            // «до 50» (0-50) — подборка только с square_max, нулевую нижнюю границу не учитываем
            [$min, $max] = array_map('intval', explode('-', $value));
            return array_filter(['square_min' => $min, 'square_max' => $max]);
    }
    return [];
}

// Ссылки для кнопок фильтра: ['floors' => ['1' => '/projects/odnoetazhnye-doma'], ...].
// Кнопка становится ссылкой, если есть подборка ровно с её условиями
function filterChipLinks(array $groups, array $landings): array {
    $bySignature = [];
    foreach ($landings as $l) {
        $rules = array_map('intval', landingRules($l));
        ksort($rules);
        if ($rules) $bySignature[json_encode($rules)] = '/projects/' . $l['slug'];
    }
    $links = [];
    foreach ($groups as $group => $values) {
        foreach ($values as $value) {
            $rules = filterChipRules($group, (string) $value);
            ksort($rules);
            if ($rules && isset($bySignature[json_encode($rules)])) {
                $links[$group][$value] = $bySignature[json_encode($rules)];
            }
        }
    }
    return $links;
}

// Группы фильтра, совпадающие с условиями подборки, — на её странице их не показываем
function landingHiddenGroups(array $landing): array {
    $rules = landingRules($landing);
    $hide = array_values(array_intersect(['floors', 'bedrooms', 'wc'], array_keys($rules)));
    if (isset($rules['square_min']) || isset($rules['square_max'])) $hide[] = 'area';
    return $hide;
}
