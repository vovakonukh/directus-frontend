<?php
// Подборки проектов (коллекция project_landings): кнопки в фильтре каталога и страницы /projects/{slug}.
// Состав подборки:
//   - если в подборке заполнено хоть одно условие (square_min, square_max, price_max, square_terrace_min,
//     second_light) — проект входит, когда подходит под все заполненные условия; ручные отметки не учитываются;
//   - иначе — проекты, отмеченные в подборке вручную (связь projects).

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
const LANDING_PROJECT_FIELDS = 'id,square,price_tk_auto,square_terrace,second_light';

function landingHasRules(array $landing): bool {
    foreach (['square_min', 'square_max', 'price_max', 'square_terrace_min', 'second_light'] as $field) {
        if (!empty($landing[$field])) return true;
    }
    return false;
}

function projectInLanding(array $project, array $landing): bool {
    if (!landingHasRules($landing)) {
        return in_array($project['id'], $landing['project_ids']);
    }
    $square = (float) ($project['square'] ?? 0);
    $price = (float) ($project['price_tk_auto'] ?? 0);
    if (!empty($landing['square_min']) && $square < $landing['square_min']) return false;
    if (!empty($landing['square_max']) && $square > $landing['square_max']) return false;
    // проект без цены в «Недорогие» не попадает
    if (!empty($landing['price_max']) && ($price <= 0 || $price > $landing['price_max'])) return false;
    if (!empty($landing['square_terrace_min']) && (float) ($project['square_terrace'] ?? 0) < $landing['square_terrace_min']) return false;
    if (!empty($landing['second_light']) && empty($project['second_light'])) return false;
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
