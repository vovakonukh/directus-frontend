<?php
function formatDimension($value) {
    $num = floatval($value);
    return ($num == intval($num))
        ? intval($num)
        : str_replace('.', ',', $num);
}

// Склонение по числу: pluralRu(2, ['спальня', 'спальни', 'спален']) → 'спальни'
function pluralRu($n, array $forms): string {
    $n = abs((int) $n) % 100;
    $n1 = $n % 10;
    if ($n > 10 && $n < 20) return $forms[2];
    if ($n1 > 1 && $n1 < 5) return $forms[1];
    if ($n1 == 1) return $forms[0];
    return $forms[2];
}

// Короткий текст для meta description: без HTML, обрезан по границе слова
function seoExcerpt($html, int $limit = 155): string {
    $text = html_entity_decode(strip_tags(str_replace('<', ' <', (string) $html)), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = trim(preg_replace('/\s+/u', ' ', $text));
    if (mb_strlen($text) <= $limit) return $text;
    $cut = mb_substr($text, 0, $limit);
    return rtrim(mb_substr($cut, 0, mb_strrpos($cut, ' ') ?: $limit), ' ,.;:—-') . '…';
}

function getShiftTelegramLink(string $fallback = 'https://t.me/alinaclasshouse'): string {
    $day = (int) date('j');
    $res = fetchItems('shifts', [
        'filter[day][_in]' => "0,$day",
        'fields' => 'day,manager.telegram_link',
    ]);
    if (!$res) return $fallback;

    $links = [];
    foreach ($res as $row) {
        $links[$row['day']] = $row['manager']['telegram_link'] ?? null;
    }
    return $links[$day] ?? $links[0] ?? $fallback;
}

// Аннуитетный платёж в месяц: annuityPayment(6400000, 6, 30) → 38371
function annuityPayment(float $sum, float $ratePercent, int $years): int {
    $i = $ratePercent / 1200;
    $n = $years * 12;
    if ($i == 0) return (int) round($sum / $n);
    return (int) round($sum * $i / (1 - pow(1 + $i, -$n)));
}

// Адрес локального файла из /assets с версией (?v=время изменения файла).
// Заменили картинку — меняется адрес, и браузер сразу грузит новую вместо копии из кэша.
function asset(string $path): string {
    $file = __DIR__ . '/../' . ltrim($path, '/');
    return is_file($file) ? $path . '?v=' . filemtime($file) : $path;
}

// Дата сдачи из текста в число ГГГГММ для сортировки: «май 2023 года» → 202305, «2021» → 202100, без года → 0.
// Временно: finish_date в Directus — строка. Когда поле станет датой, функция не нужна.
function finishDateSortKey(string $text): int {
    $months = ['янв' => 1, 'фев' => 2, 'мар' => 3, 'апр' => 4, 'мая' => 5, 'май' => 5, 'июн' => 6,
               'июл' => 7, 'авг' => 8, 'сен' => 9, 'окт' => 10, 'ноя' => 11, 'дек' => 12];
    if (!preg_match('/\d{4}/', $text, $y)) return 0;
    $text = mb_strtolower($text);
    foreach ($months as $prefix => $m) {
        if (mb_strpos($text, $prefix) !== false) return $y[0] * 100 + $m;
    }
    return $y[0] * 100;
}

// Значения для шорткодов {{имя}}. Считаются один раз за запрос, запросы к API кэшируются fetchItems().
function shortcodeValues(): array {
    static $values = null;
    if ($values !== null) return $values;

    $count = fn($collection) => (int) (fetchItems($collection, [
        'aggregate[count]' => '*',
        'filter[status][_eq]' => 'published',
    ])[0]['count'] ?? 0);

    // те же параметры, что на главной и в шапке — берутся из того же кэша
    $cheapest = fetchItems('projects', [
        'filter[status][_eq]' => 'published',
        'filter[price_tk_auto][_nnull]' => 'true',
        'fields' => 'price_tk_auto',
        'sort' => 'price_tk_auto',
        'limit' => 1,
    ]);
    $header = fetchItems('header', ['fields' => '*,form.bitrix_code']);
    $contacts = fetchItems('contacts', ['fields' => '*']);

    return $values = [
        'projects_count'  => $count('projects'),
        'finished_count'  => $count('finished'),
        'price_from'      => (int) ($cheapest[0]['price_tk_auto'] ?? 0),
        'yandex_reviews'  => (int) ($contacts['yandex_feedback_amount'] ?? 0),
        'stroimdom_deals' => (int) ($header['domrf_deals'] ?? 0),
    ];
}

// Подставляет числа вместо {{имя}} и {{имя|форма1,форма2,форма5}}.
// Неизвестная метка или пустое значение — метка удаляется, чтобы не попасть на сайт.
function renderVars(?string $text): string {
    $text = (string) $text;
    if (strpos($text, '{{') === false) return $text;
    $values = shortcodeValues();

    return preg_replace_callback('/\{\{\s*(\w+)\s*(?:\|([^}]*))?\}\}/u', function ($m) use ($values) {
        $n = $values[$m[1]] ?? 0;
        if (!$n) {
            error_log('Shortcode: нет значения для ' . $m[0]);
            return '';
        }
        $out = number_format($n, 0, '', ' ');
        $forms = isset($m[2]) ? array_map('trim', explode(',', $m[2])) : [];
        if (count($forms) === 3) $out .= ' ' . pluralRu($n, $forms);
        return $out;
    }, $text);
}

// Блоки в статьях: [[projects popular limit=4]], [[projects slugs=a,b]], [[finished limit=4]],
// [[finished slugs=a,b]], [[feedback limit=3]], [[gallery finished=slug]], [[images]]…[[/images]] (сетка картинок).
// Обёртку <p>, которую добавляет редактор Directus, убираем: блок внутри <p> — невалидная вёрстка.
function renderBlocks(?string $html): string {
    $html = (string) $html;
    if (strpos($html, '[[') === false) return $html;

    // вокруг шорткода в редакторе бывает выделение (<strong>, <em>, <span>) — убираем и его, иначе оно ломает вёрстку блока
    $inlineOpen = '(?:<(?:strong|b|em|i|u|span)[^>]*>\s*)*';
    $inlineClose = '(?:\s*</(?:strong|b|em|i|u|span)>)*';

    // парный шорткод [[images]] ... [[/images]] обрабатываем первым, иначе [[images]] попадёт в обычные блоки
    $tag = fn($name) => '(?:<p[^>]*>\s*)?' . $inlineOpen . '\[\[\s*' . $name . '\s*\]\]' . $inlineClose . '(?:\s*(?:&nbsp;|<br\s*/?>)?\s*</p>)?';
    $html = preg_replace_callback('#' . $tag('images') . '(.*?)' . $tag('/images') . '#su', fn($m) => renderImageGrid($m[1]), $html);
    return preg_replace_callback('#(?:<p[^>]*>\s*)?' . $inlineOpen . '\[\[\s*(\w+)([^\]]*)\]\]' . $inlineClose . '(?:\s*(?:&nbsp;|<br\s*/?>)?\s*</p>)?#u', function ($m) {
        // параметры: key=value, key="текст с пробелами" (кавычки "", “”, «») или просто флаг (popular)
        $params = [];
        $raw = html_entity_decode(strip_tags($m[2]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        preg_match_all('/(\w+)(?:=(?:"([^"]*)"|“([^”]*)”|«([^»]*)»|(\S+)))?/u', $raw, $pm, PREG_SET_ORDER);
        foreach ($pm as $p) {
            $v = null;
            foreach ([2, 3, 4, 5] as $g) if (isset($p[$g]) && $p[$g] !== '') { $v = trim($p[$g]); break; }
            $params[$p[1]] = $v ?? true;
        }
        // проекты и дома выводятся по 3 в ряд — по умолчанию один полный ряд
        $limit = max(1, min(12, (int) ($params['limit'] ?? (in_array($m[1], ['projects', 'finished']) ? 3 : 4))));
        $slugs = isset($params['slugs']) ? array_filter(array_map('trim', explode(',', $params['slugs']))) : [];

        // записи в порядке, в котором slug перечислены в шорткоде
        $bySlugs = function (array $items) use ($slugs) {
            $pos = array_flip($slugs);
            usort($items, fn($a, $b) => $pos[$a['slug']] <=> $pos[$b['slug']]);
            return $items;
        };

        $file = null;
        $items = [];
        switch ($m[1]) {
            case 'projects':
                $q = [
                    'filter[status][_eq]' => 'published',
                    'fields' => 'id,name,slug,main_image,main_plan,floors,square,length,width,bedrooms,wc,price_tk_auto',
                ];
                $q += $slugs ? ['filter[slug][_in]' => implode(',', $slugs)] : ['sort' => '-popular,square', 'limit' => $limit];
                $items = fetchItems('projects', $q) ?: [];
                $file = 'article_projects.php';
                break;
            case 'finished':
                $q = [
                    'filter[status][_eq]' => 'published',
                    'fields' => 'id,slug,main_image,plan_1,floors,square,length,width,bedrooms,wc,finish_date,location,construction_period',
                ];
                $q += $slugs ? ['filter[slug][_in]' => implode(',', $slugs)] : ['sort' => '-popular,-sort', 'limit' => $limit];
                $items = fetchItems('finished', $q) ?: [];
                $file = 'article_finished.php';
                break;
            case 'feedback':
                $items = fetchItems('feedback', [
                    'filter[status][_eq]' => 'published',
                    'fields' => 'id,client_name,date,content,link,images.directus_files_id',
                    'sort' => '-date',
                    'limit' => $limit,
                ]) ?: [];
                $file = 'article_feedback.php';
                break;
            case 'gallery':
                if (!empty($params['finished']) && $params['finished'] !== true) {
                    $house = fetchItems('finished', [
                        'filter[slug][_eq]' => $params['finished'],
                        'filter[status][_eq]' => 'published',
                        'fields' => 'slug,floors,length,width,gallery.directus_files_id',
                        'limit' => 1,
                    ])[0] ?? null;
                    $items = array_column($house['gallery'] ?? [], 'directus_files_id');
                }
                $file = 'article_gallery.php';
                break;
            case 'cta':
                $formId = (int) ($params['form'] ?? 0);
                $text = is_string($params['text'] ?? null) ? $params['text'] : '';
                $button = is_string($params['button'] ?? null) ? $params['button'] : 'Оставить заявку';
                $formCode = $formId ? (fetchItems('forms', [
                    'filter[id][_eq]' => $formId,
                    'fields' => 'bitrix_code',
                ])[0]['bitrix_code'] ?? '') : '';
                if ($formCode && $text !== '') $items = [$formId];
                $file = 'article_cta.php';
                break;
        }

        if (!$file || !$items) {
            error_log('Shortcode: пустой или неизвестный блок ' . strip_tags($m[0]));
            return '';
        }
        if ($slugs && $m[1] !== 'gallery') $items = $bySlugs($items);

        ob_start();
        include __DIR__ . '/blocks/' . $file;
        return ob_get_clean();
    }, $html);
}

// Сетка картинок в статье: [[images]] <figure>…</figure> или <img> … [[/images]].
// 2 или 4 картинки — по 2 в ряд, остальное — по 3. Подписи (figcaption) сохраняются,
// по клику картинки открываются в Fancybox одной галереей.
function renderImageGrid(string $inner): string {
    static $gridNum = 0;

    // figure целиком или отдельный <img> (img внутри figure забирает первая альтернатива)
    preg_match_all('#<figure\b.*?</figure>|<img\b[^>]*>#su', $inner, $found);
    $items = [];
    foreach ($found[0] as $chunk) {
        if (!preg_match('#<img\b[^>]*>#u', $chunk, $img)) continue;
        $attr = fn($name) => preg_match('#\s' . $name . '="([^"]*)"#u', $img[0], $a) ? $a[1] : '';
        $caption = preg_match('#<figcaption[^>]*>(.*?)</figcaption>#su', $chunk, $c) ? trim($c[1]) : '';
        $items[] = ['src' => $attr('src'), 'alt' => $attr('alt'), 'caption' => $caption];
    }
    $items = array_filter($items, fn($i) => $i['src'] !== '');

    // в сетке картинка маленькая: файл из Directus без параметров берём уменьшенным, в Fancybox — оригинал
    foreach ($items as &$i) {
        $i['thumb'] = strpos($i['src'], API_URL . '/assets/') === 0 && strpos($i['src'], '?') === false ? $i['src'] . '?width=800' : $i['src'];
    }
    unset($i);

    // одна картинка или ни одной — сетка не нужна, выводим как есть
    if (count($items) < 2) return $inner;

    $gridNum++;
    $cols = in_array(count($items), [2, 4]) ? 2 : 3;
    $out = '<div class="article_block article_images article_images--cols-' . $cols . '">';
    foreach ($items as $i) {
        // src и alt уже экранированы редактором — вставляем как есть
        $out .= '<figure class="article_images__item">'
            . '<a href="' . $i['src'] . '" data-fancybox="article-images-' . $gridNum . '"'
            . ($i['caption'] !== '' ? ' data-caption="' . htmlspecialchars(strip_tags($i['caption'])) . '"' : '') . '>'
            . '<img src="' . $i['thumb'] . '" alt="' . $i['alt'] . '" loading="lazy">'
            . '</a>'
            . ($i['caption'] !== '' ? '<figcaption>' . $i['caption'] . '</figcaption>' : '')
            . '</figure>';
    }
    return $out . '</div>';
}

// Публичное название построенного дома: «Одноэтажный дом 14.1 на 9 м».
// Поле name в finished — внутреннее (там фамилия заказчика), на сайт его не выводить.
function finishedTitle(array $item): string {
    return ($item['floors'] == 1 ? 'Одноэтажный дом' : 'Двухэтажный дом') . ' ' . $item['length'] . ' на ' . $item['width'] . ' м';
}
