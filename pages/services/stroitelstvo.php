<?php
// Страница «Строительство» (/services/stroitelstvo).
// Посадочная для Директа + SEO по каркасным домам.

// Синглтон страницы: hero-картинка и плитки преимуществ
$stroitelstvo = fetchItems('stroitelstvo', [
    'fields' => 'hero_image,hero_description,hero_form.bitrix_code,hero_link,trust,payments,tiles.tiles_id.icon,tiles.tiles_id.header,tiles.tiles_id.text,tiles.tiles_id.sort',
    'deep[tiles][_sort]' => 'tiles_id.sort'
]);
if (is_array($stroitelstvo) && isset($stroitelstvo[0])) $stroitelstvo = $stroitelstvo[0];
$stroitelstvo = $stroitelstvo ?: [];
$advantages = array_map(fn($t) => $t['tiles_id'], $stroitelstvo['tiles'] ?? []);
// пункты доверия под подзаголовком hero (repeater trust: text)
$trust = array_filter(array_map(fn($t) => trim($t['text'] ?? ''), $stroitelstvo['trust'] ?? []));

// Популярные проекты
$projects = fetchItems('projects', [
    'filter[status][_eq]' => 'published',
    'filter[show_on_main][_eq]' => true,
    'fields' => 'id,name,slug,main_image,main_plan,floors,square,length,width,bedrooms,wc,price_tk',
    'limit' => 6
]) ?? [];

// Минимальная цена теплого контура по каталогу — для «от ... ₽» в hero
$cheapest = fetchItems('projects', [
    'filter[status][_eq]' => 'published',
    'filter[price_tk][_nnull]' => 'true', // строкой: http_build_query превращает true в 1, а Directus ждёт boolean
    'fields' => 'price_tk',
    'sort' => 'price_tk',
    'limit' => 1
]);
$minPrice = (int) ($cheapest[0]['price_tk'] ?? 0);

// Ипотечные программы — та же коллекция, что на /ipoteka
$mortgagePrograms = fetchItems('ipoteka_programs', [
    'filter[status][_eq]' => 'published',
    'sort' => 'sort',
    'fields' => 'name,rate,pv,srok,is_highlighted',
]) ?? [];
// в блок «Способы оплаты» — выделенная (льготная) программа, иначе первая
$mainProgram = array_values(array_filter($mortgagePrograms, fn($p) => !empty($p['is_highlighted'])))[0] ?? ($mortgagePrograms[0] ?? null);

// Комплектация — та же коллекция, что на странице проекта
$complectation = fetchItems('project_complectation', [
    'filter[status][_eq]' => 'published',
    'sort' => 'sort',
    'fields' => 'label,value,stage'
]) ?? [];
// Вкладки: ключ вкладки => заголовок и группы (stage из Directus => подзаголовок группы).
// У фундамента две группы на выбор, у остальных вкладок одна группа без подзаголовка
$tabs = [
    'fundament'  => ['title' => 'Фундамент', 'groups' => ['svai' => 'ЖБ сваи (на выбор)', 'plita' => 'Монолитная плита (на выбор)']],
    'tk'         => ['title' => 'Тёплый контур', 'groups' => ['tk' => '']],
    'inzhenerka' => ['title' => 'Инженерные системы', 'groups' => ['inzhenerka' => '']],
    'otdelka'    => ['title' => 'Отделка', 'groups' => ['otdelka' => '']],
];
$itemsByStage = [];
foreach ($complectation as $item) {
    $itemsByStage[$item['stage']][] = $item;
}
// в группы раскладываем пункты; пустые группы и вкладки не выводятся
foreach ($tabs as $key => &$tab) {
    $groups = [];
    foreach ($tab['groups'] as $stage => $subtitle) {
        if (!empty($itemsByStage[$stage])) $groups[] = ['subtitle' => $subtitle, 'items' => $itemsByStage[$stage]];
    }
    $tab['groups'] = $groups;
}
unset($tab);
$tabs = array_filter($tabs, fn($t) => !empty($t['groups']));

// Построенные дома
$finished = fetchItems('finished', [
    'fields' => 'id,name,slug,main_image,plan_1,floors,square,length,width,bedrooms,wc,finish_date,location,construction_period',
    'filter[show_on_main][_eq]' => 'true',
    'sort' => 'sort',
    'limit' => 3
]) ?? [];

// Отзывы
$feedback = fetchItems('feedback', [
    'fields' => 'id,client_name,date,content,link,images.directus_files_id',
    'filter[show_on_main][_eq]' => 'true',
    'sort' => '-date',
    'limit' => 2
]) ?? [];

// FAQ: из коллекции faq (show_on содержит stroitelstvo)
$faqs = fetchItems('faq', [
    'filter[status][_eq]' => 'published',
    'filter[show_on][_contains]' => 'stroitelstvo',
    'sort' => 'sort',
    'fields' => 'question,answer'
]) ?? [];

// Формы Bitrix: 3 — получить расчёт, 1 — проверить участок, 7 — сравнить расчёт (6 — экскурсия, берёт блок tour)
$formRows = fetchItems('forms', [
    'filter[id][_in]' => '1,3,7',
    'fields' => 'id,bitrix_code'
]) ?? [];
$forms = array_column($formRows, 'bitrix_code', 'id');
// Скрипт click-формы Bitrix вешает открытие только на элемент сразу после себя.
// Поэтому формы 3 и 1 выводятся один раз со скрытыми кнопками в конце страницы, а видимые кнопки кликают по ним
$openCalc = "document.getElementById('stroy_form_3_trigger').click()";
$openLand = "document.getElementById('stroy_form_1_trigger').click()";

// H1 и SEO-текст — из pages_seo ($seo задаётся в index.php)
$heroTitle = trim($seo['h1'] ?? '') ?: 'Строительство каркасных домов под ключ в СПб и Ленобласти';

$heroSubtitle = trim($stroitelstvo['hero_description'] ?? '');

// Этапы «Как мы строим» — статичные, живут только на этой странице
$steps = [
    ['header' => 'Консультация', 'text' => 'Обсуждаем задачу, бюджет и участок, подбираем проект.'],
    ['header' => 'Расчёт сметы', 'text' => 'Считаем стоимость по выбранной комплектации, без «от».'],
    ['header' => 'Участок', 'text' => 'Пробное бурение и геодезия, подбираем фундамент.'],
    ['header' => 'Договор', 'text' => 'Фиксируем цену, сроки, материалы и график оплаты.'],
    ['header' => 'Фундамент', 'text' => 'Сваи или плита — по результатам бурения.'],
    ['header' => 'Коробка и инженерка', 'text' => 'Каркас, кровля, окна, фасад, коммуникации.'],
    ['header' => 'Сдача дома', 'text' => 'Подписываем акт, начинает действовать гарантия.'],
];

// Этапы оплаты — repeater `payments` в синглтоне stroitelstvo (percent, description), порядок — как в админке.
// Номер этапа — по порядку (1, 2, 3…), ширина сегмента полосы = percent.
$payments = array_values(array_filter($stroitelstvo['payments'] ?? [], fn($p) => (float) ($p['percent'] ?? 0) > 0));
$fmtPercent = fn($x) => str_replace('.', ',', (string) (float) $x);
// по умолчанию открыт самый крупный платёж
$paymentsActive = $payments ? (int) array_search(max(array_column($payments, 'percent')), array_column($payments, 'percent')) : 0;

$ldFaq = [
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => array_map(fn($faq) => [
        '@type' => 'Question',
        'name' => $faq['question'],
        'acceptedAnswer' => ['@type' => 'Answer', 'text' => trim(strip_tags($faq['answer'] ?? ''))],
    ], $faqs),
];
?>

<div class="stroy">

<!-- 1. Hero -->
<section class="ipoteka__hero stroy__hero"<?php if (!empty($stroitelstvo['hero_image'])): ?> style="--hero-image: url('<?= getAssetUrl($stroitelstvo['hero_image']) ?>?width=1920')"<?php endif; ?>>
    <div class="ipoteka__hero_inner">
        <h1><?= htmlspecialchars($heroTitle) ?></h1>
        <?php if ($heroSubtitle): ?>
        <p class="ipoteka__hero_subtitle"><?= htmlspecialchars($heroSubtitle) ?></p>
        <?php endif; ?>
        <?php if ($trust): ?>
        <ul class="ipoteka__trust">
            <?php foreach ($trust as $t): ?>
            <li><?= htmlspecialchars($t) ?></li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
        <?php if ($minPrice): ?>
        <p class="stroy__hero_price">Тёплый контур — от <b><?= number_format($minPrice, 0, '', ' ') ?> ₽</b></p>
        <?php endif; ?>
        <div class="stroy__hero_buttons">
            <?php
            // Кнопка hero — как на /ipoteka: форма из Directus важнее ссылки; нет ни того, ни другого — кнопки нет
            $heroFormCode = $stroitelstvo['hero_form']['bitrix_code'] ?? '';
            $heroLink = trim($stroitelstvo['hero_link'] ?? '');
            ?>
            <?php if ($heroFormCode): ?>
            <?= $heroFormCode ?>
            <button type="button" class="button big">Рассчитать стоимость</button>
            <?php elseif ($heroLink): ?>
            <a class="button big" href="<?= htmlspecialchars($heroLink) ?>">Рассчитать стоимость</a>
            <?php endif; ?>
            <a class="button big stroy__button_ghost" href="/projects">Выбрать проект</a>
        </div>
    </div>
</section>

<!-- 2. Преимущества (плитки из синглтона stroitelstvo) -->
<?php if (!empty($advantages)): ?>
<section class="ipoteka__adv_v2">
    <?php foreach ($advantages as $adv): ?>
    <div class="ipoteka__adv_v2_card">
        <div class="ipoteka__advantage_header">
            <?php if (!empty($adv['icon'])): ?>
            <img src="<?= getAssetUrl($adv['icon']) ?>" alt="" loading="lazy">
            <?php endif; ?>
            <p><?= htmlspecialchars($adv['header']) ?></p>
        </div>
        <p><?= htmlspecialchars($adv['text']) ?></p>
    </div>
    <?php endforeach; ?>
</section>
<?php endif; ?>

<!-- 3. Популярные проекты -->
<?php if (!empty($projects)): ?>
<section class="stroy__band">
    <h2>Популярные проекты каркасных домов</h2>
    <div class="projects__collection">
        <?php foreach ($projects as $item): ?>
            <?php include 'includes/cards/project_card.php'; ?>
        <?php endforeach; ?>
    </div>
    <div class="stroy__more">
        <a class="button" href="/projects">Смотреть весь каталог</a>
    </div>
</section>
<?php endif; ?>

<!-- 4. Что входит в стоимость (вкладки Alpine, весь текст в HTML — индексируется) -->
<?php if (!empty($tabs)): ?>
<section class="stroy__band stroy__band--white" x-data="{ tab: '<?= array_key_first($tabs) ?>' }">
    <h2>Что входит в стоимость дома</h2>
    <p class="ipoteka__lead">Материалы и работы фиксируем в спецификации к договору. Ниже — наша базовая комплектация, её можно менять под ваш бюджет.</p>
    <div class="ipoteka__switch stroy__tabs">
        <?php foreach ($tabs as $key => $tab): ?>
        <button type="button" :class="{ 'active': tab === '<?= $key ?>' }" @click="tab = '<?= $key ?>'"><?= htmlspecialchars($tab['title']) ?></button>
        <?php endforeach; ?>
    </div>
    <?php $first = true; foreach ($tabs as $key => $tab): ?>
    <div class="ipoteka__example_card" x-show="tab === '<?= $key ?>'"<?= $first ? '' : ' style="display: none"' ?>>
        <h3 class="stroy__card_title"><?= htmlspecialchars($tab['title']) ?></h3>
        <?php foreach ($tab['groups'] as $group): ?>
        <?php if ($group['subtitle']): ?>
        <h4 class="stroy__group_title"><?= htmlspecialchars($group['subtitle']) ?></h4>
        <?php endif; ?>
        <?php foreach ($group['items'] as $item): ?>
        <div class="ipoteka__example_row"><span class="stroy__row_label"><?= htmlspecialchars($item['label']) ?></span><span class="stroy__row_leader" aria-hidden="true"></span><span class="stroy__row_value"><?= htmlspecialchars($item['value']) ?></span></div>
        <?php endforeach; ?>
        <?php endforeach; ?>
        <?php if ($key === 'fundament'): ?>
        <p class="ipoteka__program_note stroy__card_note">Окончательно тип фундамента определяем после пробного бурения. <a href="/services/fundament">Подробнее о фундаментах</a></p>
        <?php elseif ($key === 'inzhenerka'): ?>
        <p class="ipoteka__program_note stroy__card_note"><a href="/services/inzhenerka">Подробнее об инженерных системах</a></p>
        <?php endif; ?>
    </div>
    <?php $first = false; endforeach; ?>
    <div class="ipoteka__inline_cta">
        <p>Пришлём подробный расчёт по вашему проекту</p>
        <button type="button" class="button" onclick="<?= $openCalc ?>">Получить расчёт</button>
    </div>
</section>
<?php endif; ?>

<!-- 5. Способы оплаты: ипотека с эскроу или свои средства (ставка и взнос — из ipoteka_programs) -->
<section class="stroy__band">
    <h2>Строим в ипотеку или на свои средства</h2>
    <p class="ipoteka__lead">Выберите удобный способ — дом и гарантия одинаковые, отличается только порядок расчётов.</p>
    <div class="stroy__pay_options">
        <div class="stroy__pay_option stroy__pay_option--mortgage">
            <span class="stroy__pay_option_label">В ипотеку</span>
            <h3>Ипотека с эскроу-счётом</h3>
            <?php if ($mainProgram && (!empty($mainProgram['rate']) || !empty($mainProgram['pv']))): ?>
            <div class="stroy__pay_option_figures">
                <?php if (!empty($mainProgram['rate'])): ?>
                <div><b><?= $fmtPercent($mainProgram['rate']) ?>%</b><?= htmlspecialchars(mb_strtolower(trim($mainProgram['name']))) ?></div>
                <?php endif; ?>
                <?php if (!empty($mainProgram['pv'])): ?>
                <div><b>от <?= $fmtPercent($mainProgram['pv']) ?>%</b>первый взнос</div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <ul class="ipoteka__final_cta_list">
                <li>Деньги хранятся на эскроу-счёте в банке и переходят к нам только после сдачи дома</li>
                <li>Ипотечный брокер в команде поможет получить одобрение</li>
                <li>Сами согласуем проект, смету и участок с банком</li>
                <li>Материнский капитал — в первоначальный взнос</li>
            </ul>
            <a class="button stroy__pay_option_button" href="/ipoteka">Условия ипотеки</a>
        </div>
        <div class="stroy__pay_option">
            <span class="stroy__pay_option_label">Свои средства</span>
            <h3>Поэтапная оплата по договору</h3>
            <div class="stroy__pay_option_figures">
                <div><b>Лучшая цена</b>без расходов на банковское сопровождение</div>
            </div>
            <ul class="ipoteka__final_cta_list">
                <li>Платите по этапам — по факту выполненных работ</li>
                <li>Основная часть — когда материалы уже на вашем участке</li>
                <li>Быстрый старт: не нужно ждать одобрения банка</li>
            </ul>
            <a class="button stroy__pay_option_button" href="#payments">Как устроена оплата</a>
        </div>
    </div>
</section>

<!-- 6. Поэтапная оплата: полоса из этапов (ширина = процент), по клику — описание этапа -->
<?php if ($payments): ?>
<section id="payments" class="stroy__band stroy__band--dark" x-data="{ active: <?= $paymentsActive ?> }">
    <h2>Никаких скрытых платежей</h2>
    <p class="ipoteka__lead">Оплата поэтапная. Основную часть вы платите, когда строительные материалы уже разгружают на вашем участке. Нажмите на этап, чтобы узнать подробности.</p>
    <div class="stroy__pay">
        <div class="stroy__pay_track" role="tablist">
            <?php foreach ($payments as $i => $p): ?>
            <button type="button" class="stroy__pay_seg" role="tab" style="flex-grow: <?= (float) $p['percent'] ?>"
                :class="{ 'active': active === <?= $i ?> }" :aria-selected="active === <?= $i ?>" @click="active = <?= $i ?>">
                <span class="stroy__pay_seg_step">Этап <?= $i + 1 ?></span>
                <span class="stroy__pay_seg_bar"></span>
                <b class="stroy__pay_seg_percent"><?= $fmtPercent($p['percent']) ?>%</b>
            </button>
            <?php endforeach; ?>
        </div>
        <?php foreach ($payments as $i => $p): ?>
        <div class="stroy__pay_detail" role="tabpanel" x-show="active === <?= $i ?>"<?= $i === $paymentsActive ? '' : ' style="display: none"' ?>>
            <div class="stroy__pay_detail_head">
                <span>Этап <?= $i + 1 ?> из <?= count($payments) ?></span>
                <b><?= $fmtPercent($p['percent']) ?>% <small>от суммы договора</small></b>
            </div>
            <p><?= htmlspecialchars($p['description'] ?? '') ?></p>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- 7. Договор (статичный блок) -->
<?php $blockClass = 'stroy__band stroy__band--white'; $contractButtonJs = $openCalc; include 'includes/blocks/contract.php'; ?>

<!-- 8. Проверка участка (статичный блок) -->
<?php $blockClass = 'stroy__band stroy__band--white'; $landButtonJs = $openLand; include 'includes/blocks/land_check.php'; ?>

<!-- 9. Индивидуальный проект -->
<section class="stroy__band">
    <h2>Строим по типовым и индивидуальным проектам</h2>
    <div class="ipoteka__split">
        <div>
            <p class="ipoteka__lead">Возьмите готовый проект из каталога или сделаем свой. Меняем размеры дома, окна, террасу, добавляем комнаты. Есть только эскиз от руки — сделаем по нему объёмную модель до начала строительства.</p>
            <div class="ipoteka__tiles ipoteka__tiles--stack">
                <div class="ipoteka__tile stroy__tile_row"><img class="stroy__tile_icon" src="<?= asset('/assets/icons/house.webp') ?>" alt="" loading="lazy"><div><h3>Создадим дом с нуля</h3><p>По вашим требованиям и сценарию жизни.</p></div></div>
                <div class="ipoteka__tile stroy__tile_row"><img class="stroy__tile_icon" src="<?= asset('/assets/icons/pen.webp') ?>" alt="" loading="lazy"><div><h3>Доработаем планировку</h3><p>Возьмём проект из каталога и адаптируем.</p></div></div>
                <div class="ipoteka__tile stroy__tile_row"><img class="stroy__tile_icon" src="<?= asset('/assets/icons/tree.webp') ?>" alt="" loading="lazy"><div><h3>Впишем дом в участок</h3><p>С учётом солнца, сторон света и соседей.</p></div></div>
            </div>
        </div>
        <div class="stroy__draw">
            <a href="<?= asset('/assets/images/build__draw_first.webp') ?>" data-fancybox="stroy-draw">
                <img src="<?= asset('/assets/images/build__draw_first.webp') ?>" alt="Эскиз планировки от руки" loading="lazy">
            </a>
            <div class="stroy__draw_results">
                <?php for ($i = 1; $i <= 4; $i++): ?>
                <a href="<?= asset("/assets/images/build__draw_result_$i.webp") ?>" data-fancybox="stroy-draw">
                    <img src="<?= asset("/assets/images/build__draw_result_$i.webp") ?>" alt="3D-модель дома по эскизу" loading="lazy">
                </a>
                <?php endfor; ?>
            </div>
        </div>
    </div>
</section>

<!-- 10. Сравнение расчёта другой компании -->
<?php if (!empty($forms[7])): ?>
<section class="stroy__band stroy__band--tight">
    <div class="ipoteka__inline_cta">
        <div>
            <p>Уже есть расчёт от другой компании?</p>
            <span class="stroy__cta_sub">Бесплатно сравним с нашим расчётом и укажем на скрытые платежи и слабые места комплектации</span>
        </div>
        <?= $forms[7] ?>
        <button type="button" class="button">Сравнить расчёт</button>
    </div>
</section>
<?php endif; ?>

<!-- 11. Построенные дома -->
<?php if (!empty($finished)): ?>
<section class="stroy__band stroy__band--white">
    <h2>Построенные каркасные дома</h2>
    <div class="projects__collection">
        <?php foreach ($finished as $item): ?>
            <?php include 'includes/cards/finished_card.php'; ?>
        <?php endforeach; ?>
    </div>
    <div class="stroy__more">
        <a class="button" href="/finished">Все построенные дома</a>
    </div>
</section>
<?php endif; ?>

<!-- 12. Экскурсия (статичный блок) -->
<?php $blockClass = 'stroy__band'; include 'includes/blocks/tour.php'; ?>

<!-- 13. Отзывы -->
<?php if (!empty($feedback)): ?>
<section class="stroy__band stroy__band--white">
    <h2>Отзывы тех, кто построил дом у нас</h2>
    <div class="feedback_gallery">
        <?php foreach ($feedback as $item): ?>
            <?php include 'includes/cards/feedback_card.php'; ?>
        <?php endforeach; ?>
    </div>
    <div class="stroy__more">
        <a class="button" href="/feedback">Все отзывы</a>
    </div>
</section>
<?php endif; ?>

<!-- 14. Почему каркасный дом (статичный блок) -->
<?php $blockClass = 'stroy__band'; include 'includes/blocks/why_frame.php'; ?>

<!-- 15. Этапы -->
<section class="stroy__band stroy__band--white">
    <h2>Как мы строим</h2>
    <ol class="ipoteka__steps">
        <?php foreach ($steps as $i => $step): ?>
        <li class="ipoteka__step">
            <span class="ipoteka__step_num"><?= $i + 1 ?></span>
            <div>
                <h3><?= htmlspecialchars($step['header']) ?></h3>
                <p><?= htmlspecialchars($step['text']) ?></p>
            </div>
        </li>
        <?php endforeach; ?>
    </ol>
</section>

<!-- 16. FAQ -->
<?php if (!empty($faqs)): ?>
<section class="stroy__band">
    <h2>Частые вопросы о строительстве каркасного дома</h2>
    <?php foreach ($faqs as $f): ?>
        <?php include 'includes/cards/faq_card.php'; ?>
    <?php endforeach; ?>
    <script type="application/ld+json"><?= json_encode($ldFaq, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) ?></script>
</section>
<?php endif; ?>

<!-- 17. Финальный CTA -->
<section class="stroy__band stroy__final">
    <div class="ipoteka__final_cta"<?php if (!empty($stroitelstvo['hero_image'])): ?> style="--cta-image: url('<?= getAssetUrl($stroitelstvo['hero_image']) ?>?width=1600')"<?php endif; ?>>
        <div class="ipoteka__final_cta_content">
            <h2>Рассчитаем стоимость вашего дома</h2>
            <p>Оставьте заявку — специалист свяжется с вами, пришлёт подробную комплектацию и ответит на вопросы.</p>
            <ul class="ipoteka__final_cta_list">
                <li>Расчёт без «от» — по вашему проекту</li>
                <li>Подбор фундамента под участок</li>
                <li>Расчёт ипотечного платежа</li>
            </ul>
        </div>
        <div class="ipoteka__final_cta_action">
            <button type="button" class="button big" onclick="<?= $openCalc ?>">Получить расчёт</button>
        </div>
    </div>
</section>

<!-- 18. SEO-текст из pages_seo -->
<?php if (!empty($seo['seo_text'])): ?>
<section class="stroy__band stroy__band--tight">
    <?php include 'includes/seo_text.php'; ?>
</section>
<?php endif; ?>

<!-- Формы для кнопок, которых на странице несколько: видимые кнопки кликают по этим скрытым -->
<?= $forms[3] ?? '' ?>
<button type="button" id="stroy_form_3_trigger" hidden></button>
<?= $forms[1] ?? '' ?>
<button type="button" id="stroy_form_1_trigger" hidden></button>

</div>
