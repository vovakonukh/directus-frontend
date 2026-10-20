<?php
// Тестовая версия страницы «Строительство» (/services/stroitelstvo-test, noindex).
// Посадочная для Директа + SEO по каркасным домам.

// Синглтон страницы: hero-картинка и плитки преимуществ
$stroitelstvo = fetchItems('stroitelstvo', [
    'fields' => 'hero_image,tiles.tiles_id.icon,tiles.tiles_id.header,tiles.tiles_id.text,tiles.tiles_id.sort',
    'deep[tiles][_sort]' => 'tiles_id.sort'
]);
if (is_array($stroitelstvo) && isset($stroitelstvo[0])) $stroitelstvo = $stroitelstvo[0];
$stroitelstvo = $stroitelstvo ?: [];
$advantages = array_map(fn($t) => $t['tiles_id'], $stroitelstvo['tiles'] ?? []);

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

// Комплектация — та же коллекция, что на странице проекта
$complectation = fetchItems('project_complectation', [
    'filter[status][_eq]' => 'published',
    'sort' => 'sort',
    'fields' => 'label,value,stage'
]) ?? [];
$stages = [
    'tk'         => 'Тёплый контур',
    'svai'       => 'Фундамент: сваи',
    'plita'      => 'Фундамент: плита',
    'inzhenerka' => 'Инженерные системы',
    'otdelka'    => 'Отделка',
];
$complectationByStage = [];
foreach ($complectation as $item) {
    if (isset($stages[$item['stage']])) $complectationByStage[$item['stage']][] = $item;
}
// порядок вкладок = порядок $stages, пустые этапы не выводятся
$complectationByStage = array_filter(array_replace(array_fill_keys(array_keys($stages), []), $complectationByStage));

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

// FAQ: из коллекции faq (show_on содержит stroitelstvo), пока там пусто — запасной хардкод ниже
$faqs = fetchItems('faq', [
    'filter[status][_eq]' => 'published',
    'filter[show_on][_contains]' => 'stroitelstvo',
    'sort' => 'sort',
    'fields' => 'question,answer'
]) ?? [];

// Формы Bitrix: 7 — проект на расчёт, 3 — свой проект на расчёт, 6 — экскурсия
$formRows = fetchItems('forms', [
    'filter[id][_in]' => '3,6,7',
    'fields' => 'id,bitrix_code'
]) ?? [];
$forms = array_column($formRows, 'bitrix_code', 'id');
// Скрипт click-формы Bitrix вешает открытие только на элемент сразу после себя.
// Поэтому каждый скрипт выводится один раз, а остальные кнопки кликают по его кнопке
$openCalc = "document.getElementById('stroy_calc_trigger').click()";

// SEO-текст: пока тестовая страница, берём запись реальной страницы
if (empty($seo)) {
    $seo = fetchItems('pages_seo', [
        'filter[route][_eq]' => 'services/stroitelstvo',
        'fields' => 'h1,seo_text',
        'limit' => 1
    ])[0] ?? null;
}
$heroTitle = trim($seo['h1'] ?? '') ?: 'Строительство каркасных домов под ключ в СПб и Ленобласти';

// ===== Хардкод (перенести в Directus после утверждения макета) =====
$heroSubtitle = 'Проект, фундамент, коробка, инженерные системы и отделка — одним договором. Фиксированная цена и поэтапная оплата по факту работ.';
$trust = [
    '5,0 на Яндекс Картах — 75 отзывов',
    'Гарантия 5 лет по договору',
    '37 сделок по эскроу на строим.дом.рф',
];

$whyFrame = [
    ['header' => 'Быстро', 'text' => 'Коробку под ключ собираем за 1–3 месяца: на наших сданных объектах — от 35 дней. Без «мокрых» процессов и перерывов на усадку.'],
    ['header' => 'Тепло и экономно', 'text' => '150 мм каменной ваты в стенах и 200 мм в перекрытиях. Такой дом быстро прогревается и дёшев в отоплении.'],
    ['header' => 'Без усадки', 'text' => 'Каркас из доски камерной сушки не «ведёт», отделку можно делать сразу после монтажа.'],
    ['header' => 'Лёгкий фундамент', 'text' => 'Дом весит в разы меньше каменного — подходит свайный фундамент, это экономия на нулевом цикле.'],
    ['header' => 'Строим круглый год', 'text' => 'Каркасная технология не боится мороза: заезжаем на участок в любой сезон.'],
    ['header' => 'Подходит для ипотеки', 'text' => 'Строим по семейной ипотеке с эскроу-счётом — банк перечисляет деньги после сдачи дома.'],
];

$steps = [
    ['header' => 'Консультация', 'text' => 'Обсуждаем задачу, бюджет и участок, подбираем проект.'],
    ['header' => 'Расчёт сметы', 'text' => 'Считаем стоимость по выбранной комплектации, без «от».'],
    ['header' => 'Участок', 'text' => 'Пробное бурение и геодезия, подбираем фундамент.'],
    ['header' => 'Договор', 'text' => 'Фиксируем цену, сроки, материалы и график оплаты.'],
    ['header' => 'Фундамент', 'text' => 'Сваи или плита — по результатам бурения.'],
    ['header' => 'Коробка и инженерка', 'text' => 'Каркас, кровля, окна, фасад, коммуникации.'],
    ['header' => 'Сдача дома', 'text' => 'Подписываем акт, начинает действовать гарантия.'],
];

$payments = [
    ['sum' => '100 000 ₽', 'text' => 'После подписания договора — на проектную документацию и заказ материалов.'],
    ['sum' => '60%', 'text' => 'При заезде на участок: привозим пиломатериал, приезжают прораб и бригада.'],
    ['sum' => '15%', 'text' => 'После возведения силового каркаса и укладки кровли.'],
    ['sum' => '15%', 'text' => 'После окончания наружной отделки.'],
    ['sum' => '10%', 'text' => 'После окончания всех работ.'],
];

$contractItems = [
    ['icon' => 'service__money.webp', 'text' => 'Стоимость работ'],
    ['icon' => 'service__payments.webp', 'text' => 'Порядок оплаты'],
    ['icon' => 'service__period.webp', 'text' => 'Сроки работы'],
    ['icon' => 'service__warranty.webp', 'text' => 'Гарантия 5 лет'],
    ['icon' => 'service__smeta.webp', 'text' => 'Спецификация используемых материалов'],
];

$faqFallback = [
    ['question' => 'Сколько стоит построить каркасный дом под ключ?', 'answer' => 'Цена зависит от площади, проекта, фундамента и комплектации. Тёплый контур по нашему каталогу начинается от ' . ($minPrice ? number_format($minPrice, 0, '', ' ') . ' ₽' : 'суммы, указанной в карточке проекта') . '. Точную смету считаем бесплатно по вашему проекту или эскизу.'],
    ['question' => 'Сколько времени занимает строительство?', 'answer' => 'Коробка дома площадью около 100 м² — в среднем 1,5–3 месяца. Срок фиксируется в договоре. Фактические сроки по каждому дому смотрите в разделе «Построенные дома».'],
    ['question' => 'Какой фундамент нужен для каркасного дома?', 'answer' => 'Чаще всего — железобетонные сваи, при сложных грунтах или для дома с тёплыми полами по грунту — монолитная плита. Окончательно тип фундамента выбираем после пробного бурения на участке.'],
    ['question' => 'Можно ли изменить типовой проект?', 'answer' => 'Да. Каждый дом строим по индивидуальному проекту: меняем размеры, планировку, окна, террасу. Можем сделать проект с нуля по вашему эскизу.'],
    ['question' => 'Как происходит оплата?', 'answer' => 'Поэтапно: 100 000 ₽ при подписании договора, основная часть — когда материалы привезены на участок, остальное — по факту выполненных этапов. Последние 10% — после окончания всех работ.'],
    ['question' => 'Можно ли построить дом в ипотеку?', 'answer' => 'Да, работаем с семейной и другими программами ипотеки на строительство с эскроу-счётом. Ипотечный брокер в нашей команде поможет получить одобрение.'],
    ['question' => 'Какая гарантия на дом?', 'answer' => 'Гарантия 5 лет прописана в договоре. Если в этот период возникает проблема, устраняем её за свой счёт.'],
    ['question' => 'Можно ли посмотреть ваши дома вживую?', 'answer' => 'Да, проводим экскурсии по строящимся и готовым домам — можно оценить качество работ и пообщаться с прорабом.'],
];
// ===== Конец хардкода =====

if (empty($faqs)) $faqs = $faqFallback;

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

<!-- 1. Hero -->
<section class="ipoteka__hero stroy__hero"<?php if (!empty($stroitelstvo['hero_image'])): ?> style="--hero-image: url('<?= getAssetUrl($stroitelstvo['hero_image']) ?>?width=1920')"<?php endif; ?>>
    <div class="ipoteka__hero_inner">
        <h1><?= htmlspecialchars($heroTitle) ?></h1>
        <p class="ipoteka__hero_subtitle"><?= htmlspecialchars($heroSubtitle) ?></p>
        <ul class="ipoteka__trust">
            <?php foreach ($trust as $t): ?>
            <li><?= htmlspecialchars($t) ?></li>
            <?php endforeach; ?>
        </ul>
        <?php if ($minPrice): ?>
        <p class="stroy__hero_price">Тёплый контур — от <b><?= number_format($minPrice, 0, '', ' ') ?> ₽</b></p>
        <?php endif; ?>
        <div class="stroy__hero_buttons">
            <?= $forms[7] ?? '' ?>
            <button type="button" class="button big" id="stroy_calc_trigger">Рассчитать стоимость</button>
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
<section>
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
<?php if (!empty($complectationByStage)): ?>
<section x-data="{ tab: '<?= array_key_first($complectationByStage) ?>' }">
    <h2>Что входит в стоимость дома</h2>
    <p class="ipoteka__lead">Материалы и работы фиксируем в спецификации к договору. Ниже — наша базовая комплектация, её можно менять под ваш бюджет.</p>
    <div class="ipoteka__switch stroy__tabs">
        <?php foreach ($complectationByStage as $stage => $items): ?>
        <button type="button" :class="{ 'active': tab === '<?= $stage ?>' }" @click="tab = '<?= $stage ?>'"><?= htmlspecialchars($stages[$stage]) ?></button>
        <?php endforeach; ?>
    </div>
    <?php $first = true; foreach ($complectationByStage as $stage => $items): ?>
    <div class="ipoteka__example_card" x-show="tab === '<?= $stage ?>'"<?= $first ? '' : ' style="display: none"' ?>>
        <h3 class="stroy__card_title"><?= htmlspecialchars($stages[$stage]) ?></h3>
        <?php foreach ($items as $item): ?>
        <div class="ipoteka__example_row"><?= htmlspecialchars($item['label']) ?> <span class="stroy__row_value"><?= htmlspecialchars($item['value']) ?></span></div>
        <?php endforeach; ?>
        <?php if ($stage === 'svai' || $stage === 'plita'): ?>
        <p class="ipoteka__program_note stroy__card_note">Окончательно тип фундамента определяем после пробного бурения. <a href="/services/fundament">Подробнее о фундаментах</a></p>
        <?php elseif ($stage === 'inzhenerka'): ?>
        <p class="ipoteka__program_note stroy__card_note"><a href="/services/inzhenerka">Подробнее об инженерных системах</a></p>
        <?php endif; ?>
    </div>
    <?php $first = false; endforeach; ?>
    <div class="ipoteka__inline_cta">
        <p>Пришлём подробную смету по вашему проекту</p>
        <button type="button" class="button" onclick="<?= $openCalc ?>">Получить смету</button>
    </div>
</section>
<?php endif; ?>

<!-- 5. Почему каркасный дом -->
<section>
    <h2>Почему каркасный дом</h2>
    <div class="ipoteka__tiles">
        <?php foreach ($whyFrame as $tile): ?>
        <div class="ipoteka__tile">
            <h3><?= htmlspecialchars($tile['header']) ?></h3>
            <p><?= htmlspecialchars($tile['text']) ?></p>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- 6. Этапы -->
<section>
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

<!-- 7. Поэтапная оплата -->
<section>
    <h2>Никаких скрытых платежей</h2>
    <p class="ipoteka__lead">Основную часть вы платите, когда строительные материалы уже разгружают на вашем участке.</p>
    <div class="build__payments_inner">
        <div class="build__payments_list">
            <?php foreach ($payments as $i => $p): ?>
            <div class="build__payments_list_item">
                <span><?= $i + 1 ?></span>
                <div class="build__payments_list_item_text">
                    <strong><?= htmlspecialchars($p['sum']) ?></strong>
                    <p><?= htmlspecialchars($p['text']) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="build__payments_video_wrap">
            <iframe src="https://vkvideo.ru/video_ext.php?oid=-195326596&id=456239196&hd=3" width="300" height="535" allow="autoplay; encrypted-media; fullscreen; picture-in-picture; screen-wake-lock;" frameborder="0" allowfullscreen loading="lazy"></iframe>
        </div>
    </div>
</section>

<!-- 8. Индивидуальный проект -->
<section>
    <h2>Строим по индивидуальному проекту</h2>
    <div class="ipoteka__split">
        <div>
            <p class="ipoteka__lead">Идеального типового проекта не существует. Меняем размеры дома, окна, террасу, добавляем комнаты. Есть только эскиз от руки — сделаем по нему объёмную модель до начала строительства.</p>
            <div class="ipoteka__tiles ipoteka__tiles--stack">
                <div class="ipoteka__tile"><h3>Создадим дом с нуля</h3><p>По вашим требованиям и сценарию жизни.</p></div>
                <div class="ipoteka__tile"><h3>Доработаем планировку</h3><p>Возьмём проект из каталога и адаптируем.</p></div>
                <div class="ipoteka__tile"><h3>Впишем дом в участок</h3><p>С учётом солнца, сторон света и соседей.</p></div>
            </div>
        </div>
        <div class="stroy__draw">
            <img src="/assets/images/build__draw_first.webp" alt="Эскиз планировки от руки" loading="lazy">
            <div class="stroy__draw_results">
                <?php for ($i = 1; $i <= 4; $i++): ?>
                <a href="/assets/images/build__draw_result_<?= $i ?>.webp" data-fancybox="stroy-draw">
                    <img src="/assets/images/build__draw_result_<?= $i ?>.webp" alt="3D-модель дома по эскизу" loading="lazy">
                </a>
                <?php endfor; ?>
            </div>
        </div>
    </div>
</section>

<!-- 9. Сравнение сметы -->
<?php if (!empty($forms[3])): ?>
<section>
    <div class="ipoteka__inline_cta">
        <div>
            <p>Уже есть смета другой компании?</p>
            <span class="stroy__cta_sub">Бесплатно сравним с нашим расчётом и укажем на скрытые платежи и слабые места комплектации</span>
        </div>
        <?= $forms[3] ?>
        <button type="button" class="button">Сравнить смету</button>
    </div>
</section>
<?php endif; ?>

<!-- 10. Построенные дома -->
<?php if (!empty($finished)): ?>
<section>
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

<!-- 11. Отзывы -->
<?php if (!empty($feedback)): ?>
<section>
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

<!-- 12. Экскурсия -->
<section>
    <div class="ipoteka__split stroy__tour">
        <div>
            <h2>Лучше один раз увидеть</h2>
            <p class="ipoteka__lead">Отвезём вас на наши строящиеся объекты: оцените качество работ и пообщайтесь с прорабом.</p>
            <?= $forms[6] ?? '' ?>
            <button type="button" class="button">Записаться на экскурсию</button>
        </div>
        <img class="ipoteka__land_image" src="/assets/images/build__tour.webp" alt="Строящийся каркасный дом" loading="lazy">
    </div>
</section>

<!-- 13. Участок и ипотека -->
<section>
    <div class="ipoteka__tiles stroy__tiles--2">
        <div class="ipoteka__tile">
            <h3>Поможем с участком</h3>
            <p>Подскажем, на что смотреть при покупке и чем отличаются ИЖС и СНТ. Сделаем пробное бурение, геодезию и вынос границ.</p>
            <button type="button" class="button stroy__tile_button" onclick="<?= $openCalc ?>">Проконсультироваться</button>
        </div>
        <div class="ipoteka__tile ipoteka__tile--cta">
            <h3>Строительство в ипотеку</h3>
            <p>Семейная ипотека с эскроу-счётом. Ипотечный брокер в команде поможет получить одобрение.</p>
            <a class="button" href="/ipoteka">Условия ипотеки</a>
        </div>
    </div>
</section>

<!-- 14. Договор -->
<section class="service__contract_section">
    <div class="service__contract_inner">
        <div class="service__contract_col_1">
            <h2>Вы защищены по договору на 100%</h2>
            <p class="service__description">Перед началом работ заключаем договор, поэтому вы защищены законодательством РФ. Персональный руководитель проекта на связи весь срок строительства.</p>
        </div>
        <div class="service__contract_col_2">
            <h3>В договоре зафиксированы:</h3>
            <?php foreach ($contractItems as $c): ?>
            <div class="service__contract_list">
                <img src="/assets/icons/<?= $c['icon'] ?>" alt="" loading="lazy" />
                <p><?= htmlspecialchars($c['text']) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 15. FAQ -->
<section>
    <h2>Частые вопросы о строительстве каркасного дома</h2>
    <?php foreach ($faqs as $f): ?>
        <?php include 'includes/cards/faq_card.php'; ?>
    <?php endforeach; ?>
</section>
<script type="application/ld+json"><?= json_encode($ldFaq, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) ?></script>

<!-- 16. Финальный CTA -->
<section class="stroy__final">
    <div class="ipoteka__final_cta"<?php if (!empty($stroitelstvo['hero_image'])): ?> style="--cta-image: url('<?= getAssetUrl($stroitelstvo['hero_image']) ?>?width=1600')"<?php endif; ?>>
        <div class="ipoteka__final_cta_content">
            <h2>Рассчитаем стоимость вашего дома</h2>
            <p>Оставьте заявку — специалист свяжется с вами, пришлёт подробную комплектацию и ответит на вопросы.</p>
            <ul class="ipoteka__final_cta_list">
                <li>Смета без «от» — по вашему проекту</li>
                <li>Подбор фундамента под участок</li>
                <li>Расчёт ипотечного платежа</li>
            </ul>
        </div>
        <div class="ipoteka__final_cta_action">
            <button type="button" class="button big" onclick="<?= $openCalc ?>">Получить расчёт</button>
        </div>
    </div>
</section>

<!-- 17. SEO-текст из pages_seo -->
<?php if (!empty($seo['seo_text'])): ?>
<section>
    <?php include 'includes/seo_text.php'; ?>
</section>
<?php endif; ?>
