<?php
// Главная (/). Собрана из блоков других страниц + hero с фото построенного дома.
// Прошлая версия (слайдер homepage_slides) — pages/home_old.php, в роутинге не подключена.

// Синглтон homepage: hero, дом для фото в hero, карта. SEO-текст и запасной H1 — из pages_seo (route «/»), $seo задаёт index.php
$homepage = fetchItems('homepage', ['fields' => 'hero_title,hero_subtitle,hero_trust,hero_house.slug,hero_house.main_image,hero_house.square,hero_house.location,hero_house.construction_period,map_code']);
if (is_array($homepage) && isset($homepage[0])) $homepage = $homepage[0];
$heroTitle = trim($homepage['hero_title'] ?? '') ?: trim($seo['h1'] ?? '') ?: 'Каркасные дома в Ленинградской и Московской области';
$heroSubtitle = trim($homepage['hero_subtitle'] ?? '');
$trust = array_filter(array_map(fn($t) => trim($t['text'] ?? ''), $homepage['hero_trust'] ?? []));
$mapCode = $homepage['map_code'] ?? '';

// Минимальная цена тёплого контура — для «от ... ₽» в hero
$cheapest = fetchItems('projects', [
    'filter[status][_eq]' => 'published',
    'filter[price_tk][_nnull]' => 'true',
    'fields' => 'price_tk',
    'sort' => 'price_tk',
    'limit' => 1
]);
$minPrice = (int) ($cheapest[0]['price_tk'] ?? 0);

// Популярные проекты
$projects = fetchItems('projects', [
    'filter[status][_eq]' => 'published',
    'filter[show_on_main][_eq]' => true,
    'fields' => 'id,name,slug,main_image,main_plan,floors,square,length,width,bedrooms,wc,price_tk',
    'limit' => 6
]) ?? [];

// Построенные дома
$finished = fetchItems('finished', [
    'fields' => 'id,name,slug,main_image,plan_1,floors,square,length,width,bedrooms,wc,finish_date,location,construction_period',
    'filter[show_on_main][_eq]' => 'true',
    'sort' => 'sort',
    'limit' => 3
]) ?? [];

// Дом для фото в hero (с табличкой «где и за сколько построили»): выбран в homepage.hero_house, иначе первый построенный
$heroHouse = is_array($homepage['hero_house'] ?? null) ? $homepage['hero_house'] : ($finished[0] ?? null);

// Ипотека: выделенная (льготная) программа, иначе первая
$mortgagePrograms = fetchItems('ipoteka_programs', [
    'filter[status][_eq]' => 'published',
    'sort' => 'sort',
    'fields' => 'name,rate,pv,is_highlighted',
]) ?? [];
$mainProgram = array_values(array_filter($mortgagePrograms, fn($p) => !empty($p['is_highlighted'])))[0] ?? ($mortgagePrograms[0] ?? null);
$fmtPercent = fn($x) => str_replace('.', ',', (string) (float) $x);

// Отзывы
$feedback = fetchItems('feedback', [
    'fields' => 'id,client_name,date,content,link,images.directus_files_id',
    'filter[show_on_main][_eq]' => 'true',
    'sort' => '-date',
    'limit' => 2
]) ?? [];

// FAQ главной: в коллекции faq show_on должен содержать home
$faqs = fetchItems('faq', [
    'filter[status][_eq]' => 'published',
    'filter[show_on][_contains]' => 'home',
    'sort' => 'sort',
    'fields' => 'question,answer'
]) ?? [];

// Форма 3 — получить расчёт. Выводится один раз со скрытой кнопкой, видимые кнопки кликают по ней
$calcForm = fetchItems('forms', ['filter[id][_eq]' => 3, 'fields' => 'bitrix_code'])[0]['bitrix_code'] ?? '';
$openCalc = "document.getElementById('home_form_3_trigger').click()";

// Направления — ссылки на разделы услуг
$directions = [
    ['href' => '/services/stroitelstvo', 'icon' => '/assets/icons/home__services_1.webp', 'title' => 'Дома под ключ', 'text' => 'Каркасные дома по типовым и индивидуальным проектам'],
    ['href' => '/services/fundament', 'icon' => '/assets/icons/home__services_2.webp', 'title' => 'Фундаменты', 'text' => 'Сваи и монолитная плита после пробного бурения'],
    ['href' => '/services/inzhenerka', 'icon' => '/assets/icons/home__services_3.webp', 'title' => 'Инженерные сети', 'text' => 'Электрика, водоснабжение, отопление, канализация'],
    ['href' => '/ipoteka', 'icon' => '/assets/icons/home__services_4.webp', 'title' => 'Ипотека', 'text' => 'Строим с эскроу-счётом, поможем с одобрением'],
];

?>

<div class="stroy home2">

<!-- 1. Hero: фото реального построенного дома (на десктопе — на весь блок, слева оффер на градиенте) -->
<section class="home2__hero">
    <div class="home2__hero_text">
        <h1><?= htmlspecialchars($heroTitle) ?></h1>
        <?php if ($heroSubtitle): ?>
        <p class="home2__hero_subtitle"><?= htmlspecialchars($heroSubtitle) ?></p>
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
            <button type="button" class="button big" onclick="<?= $openCalc ?>">Рассчитать стоимость</button>
            <a class="button big stroy__button_ghost" href="/projects">Выбрать проект</a>
        </div>
    </div>
    <?php if ($heroHouse): ?>
    <figure class="home2__hero_photo">
        <img src="<?= getAssetUrl($heroHouse['main_image']) ?>?width=1400&format=webp" alt="Построенный каркасный дом, <?= htmlspecialchars($heroHouse['location']) ?>" fetchpriority="high">
        <figcaption class="home2__plate">
            <a href="/finished/<?= $heroHouse['slug'] ?>">
                <?php if (!empty($heroHouse['construction_period'])): ?>
                <b>Этот дом мы построили за <?= htmlspecialchars($heroHouse['construction_period']) ?></b>
                <?php else: ?>
                <b>Этот дом построили мы</b>
                <?php endif; ?>
                <?= htmlspecialchars($heroHouse['location']) ?>, <?= $heroHouse['square'] ?> м²
            </a>
        </figcaption>
    </figure>
    <?php endif; ?>
</section>

<!-- 2. Направления -->
<nav class="home2__directions" aria-label="Услуги">
    <?php foreach ($directions as $d): ?>
    <a class="home2__direction" href="<?= $d['href'] ?>">
        <img src="<?= asset($d['icon']) ?>" alt="" loading="lazy">
        <span>
            <b><?= $d['title'] ?></b>
            <?= $d['text'] ?>
        </span>
    </a>
    <?php endforeach; ?>
</nav>

<!-- 3. Популярные проекты -->
<?php if (!empty($projects)): ?>
<section class="stroy__band stroy__band--white">
    <h2>Популярные проекты</h2>
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

<!-- 4. Этапы (статичный блок) -->
<?php $blockClass = 'stroy__band'; include 'includes/blocks/steps.php'; ?>

<!-- 5. Экскурсия (статичный блок) -->
<?php $blockClass = 'stroy__band stroy__band--white'; include 'includes/blocks/tour.php'; ?>

<!-- 6. Построенные дома -->
<?php if (!empty($finished)): ?>
<section class="stroy__band">
    <h2>Мы построили</h2>
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

<!-- 7. Почему строят с нами (статичный блок) -->
<?php $blockClass = 'stroy__band stroy__band--white'; include 'includes/blocks/why_us.php'; ?>

<!-- 8. Отзывы -->
<?php if (!empty($feedback)): ?>
<section class="stroy__band">
    <h2>Отзывы</h2>
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

<!-- 9. Способы оплаты -->
<section class="stroy__band stroy__band--white">
    <h2>Строим в ипотеку или на свои средства</h2>
    <p class="ipoteka__lead">Дом и гарантия одинаковые, отличается только порядок расчётов.</p>
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
                <li>Деньги хранятся на эскроу-счёте и переходят к нам только после сдачи дома</li>
                <li>Ипотечный брокер в команде поможет получить одобрение</li>
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
            <a class="button stroy__pay_option_button" href="/services/stroitelstvo#payments">Как устроена оплата</a>
        </div>
    </div>
</section>

<!-- 10. География -->
<?php if ($mapCode): ?>
<section class="stroy__band">
    <h2>География построенных домов</h2>
    <div class="home2__map"><?= $mapCode ?></div>
</section>
<?php endif; ?>

<!-- 11. FAQ (появится, когда у вопросов в faq будет show_on = home) -->
<?php if (!empty($faqs)): ?>
<section class="stroy__band stroy__band--white">
    <h2>Частые вопросы</h2>
    <?php foreach ($faqs as $f): ?>
        <?php include 'includes/cards/faq_card.php'; ?>
    <?php endforeach; ?>
</section>
<?php endif; ?>

<!-- 12. Финальный CTA -->
<section class="stroy__band stroy__band--white stroy__final">
    <div class="ipoteka__final_cta"<?php if ($heroHouse): ?> style="--cta-image: url('<?= getAssetUrl($heroHouse['main_image']) ?>?width=1600&format=webp')"<?php endif; ?>>
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

<!-- 13. SEO-текст из pages_seo, сворачивается -->
<?php if (!empty($seo['seo_text'])): ?>
<section class="stroy__band stroy__band--white stroy__band--tight">
    <?php include 'includes/seo_text.php'; ?>
</section>
<?php endif; ?>

<?= $calcForm ?>
<button type="button" id="home_form_3_trigger" hidden></button>

</div>
