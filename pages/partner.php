<?php
// Страница «Партнёрам»: агенты по недвижимости, ипотечные брокеры, продавцы участков.
// Тексты статичные (как на /postavshhikam). Из Directus: форма Bitrix (forms), FAQ (faq, show_on = partner), H1/SEO (pages_seo).

// ===== Настройки (заполнить) =====
// TODO: id формы Bitrix24 в коллекции forms. Пока null — кнопки ведут в Telegram менеджера
$partnerFormId = 11;

// TODO: менеджер по работе с партнёрами
$manager = [
    'name'     => 'Ольга Кузьмина',
    'role'     => 'Менеджер по работе с партнёрами',
    'phone'    => '+7 (921) 570-21-46',
    'telegram' => 'https://t.me/Oly_Kuzmina',
    'max'      => 'https://max.ru/u/f9LHodD0cOLfjwmIF07nhicM2NREJTkh77E7POqcHTI87Zpq_bIrb_yGffs', // TODO: ссылка на MAX менеджера; пусто — иконки нет
    'photo'    => 'partner_manager.webp', // имя файла в /assets/images/, например 'partner_manager.webp'
];

// TODO: картинки (имена файлов в /assets/images/). Пустая строка — блок без картинки
$heroImage = 'partner__hero.webp';
$tourImage = 'broker_tour.webp';

// Пример расчёта комиссии
$commission = 0.05;
$examplePrice = 6000000;

// ===== Данные из Directus =====
$formCode = '';
if ($partnerFormId) {
    $form = fetchItems('forms', ['filter[id][_eq]' => $partnerFormId, 'fields' => 'bitrix_code']);
    $formCode = $form[0]['bitrix_code'] ?? '';
}

$faqs = fetchItems('faq', [
    'filter[status][_eq]' => 'published',
    'filter[show_on][_contains]' => 'partner',
    'sort' => 'sort',
    'fields' => 'question,answer'
]) ?? [];

// Скрипт click-формы Bitrix вешает открытие только на элемент сразу после себя,
// поэтому форма выводится один раз (финальный CTA), остальные кнопки кликают по её кнопке
$openForm = "document.getElementById('partner_form_trigger').click()";

$heroTitle = trim($seo['h1'] ?? '') ?: 'Приводите клиентов на строительство дома — получайте 5%';

$fmt = fn($x) => number_format($x, 0, '', ' ');
$exampleFee = $examplePrice * $commission;

$audience = [
    ['header' => 'Агентам по недвижимости', 'text' => 'Клиент ищет готовый дом, но не находит подходящий? Предложите построить — и получите комиссию со сделки, которая иначе бы сорвалась.'],
    ['header' => 'Ипотечным брокерам', 'text' => 'Клиенту одобрили ипотеку на строительство — нужен надёжный подрядчик, которого примет банк. Мы работаем с крупными банками и эскроу-счетами.'],
    ['header' => 'Продавцам участков', 'text' => 'Покупателю проще решиться на участок, когда понятно, какой дом на нём встанет и сколько это стоит. Покажите проекты — и получите комиссию со стройки.'],
];

$facts = [
    ['value' => '6 лет', 'text' => 'строим загородные дома в Ленинградской и Московской областях'],
    ['value' => '200+', 'text' => 'семей уже живут в построенных нами домах'],
    ['value' => 'до 10 лет', 'text' => 'официальной гарантии на дома по договору'],
    ['value' => '~15 домов', 'text' => 'прямо сейчас в стадии строительства — готовы показать любой объект вам и вашим клиентам'],
];

$steps = [
    ['header' => 'Знакомимся', 'text' => 'Созваниваемся или встречаемся, рассказываем о компании, проектах и ценах.'],
    ['header' => 'Подписываем договор', 'text' => 'Заключаем агентский договор — комиссия и сроки выплат зафиксированы.'],
    ['header' => 'Передаёте клиента', 'text' => 'Присылаете контакт клиента или приезжаете на встречу вместе.'],
    ['header' => 'Ведём сделку', 'text' => 'Считаем смету, подбираем проект, помогаем с ипотекой. Держим вас в курсе.'],
    ['header' => 'Получаете 5%', 'text' => 'Выплачиваем комиссию по договору: при старте стройки или двумя траншами.'],
];

// Этапы сделки и выплаты по ним: ключ — номер этапа, значение — доля комиссии в %
// При ипотеке стройка начинается сразу после выдачи кредита, поэтому это один этап
$payoutStages = [
    ['name' => 'Договор подряда', 'when' => 'Старт сделки'],
    ['name' => 'Начало строительства', 'when' => 'Через 3–4 недели после договора'],
    ['name' => 'Раскрытие эскроу-счёта', 'when' => 'Ещё через 2–3 месяца'],
];
$payoutLanes = [
    ['title' => 'Клиент строит на свои средства', 'note' => 'Одна выплата', 'payouts' => [1 => 100]],
    ['title' => 'Клиент строит в ипотеку', 'note' => 'Два транша. Стройка начинается сразу после выдачи кредита', 'payouts' => [1 => 50, 2 => 50]],
];

$tourItems = [
    'Экскурсия по готовому дому, который построила наша компания',
    'Рассказываем о технологии, проектах, ценах и ипотеке',
    'Кофе-брейк и ответы на вопросы',
    'Трансфер из города до объекта и обратно',
];
?>

<!-- 1. Hero -->
<section class="ipoteka__hero partner__hero"<?php if ($heroImage): ?> style="--hero-image: url('<?= asset('/assets/images/' . $heroImage) ?>')"<?php endif; ?>>
    <div class="ipoteka__hero_inner">
        <h1><?= htmlspecialchars($heroTitle) ?></h1>
        <p class="ipoteka__hero_subtitle">Партнёрская программа для агентов по недвижимости, ипотечных брокеров и продавцов земельных участков.</p>
        <ul class="ipoteka__trust">
            <li>5% от стоимости дома</li>
            <li>Официально по агентскому договору</li>
            <li>Личный менеджер</li>
        </ul>
        <button type="button" class="button big" onclick="<?= $openForm ?>">Стать партнёром</button>
    </div>
</section>

<!-- 2. Для кого -->
<section>
    <h2>Кому подходит</h2>
    <div class="ipoteka__tiles">
        <?php foreach ($audience as $a): ?>
        <div class="ipoteka__tile">
            <h3><?= htmlspecialchars($a['header']) ?></h3>
            <p><?= htmlspecialchars($a['text']) ?></p>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- 3. Схемы выплат: сумма одна, отличается момент выплаты -->
<section>
    <h2>Когда выплачиваем комиссию</h2>
    <div class="payout">
        <div class="payout__head">
            <div class="payout__fact">
                <p>Комиссия</p>
                <b>5% от стоимости дома</b>
            </div>
            <div class="payout__fact">
                <p>С дома за <?= $fmt($examplePrice) ?> ₽ вы получите</p>
                <b><?= $fmt($exampleFee) ?> ₽</b>
            </div>
            <p class="payout__lead">Сумма не зависит от того, как клиент платит за дом, — меняется только момент выплаты.</p>
        </div>

        <div class="payout__track">
            <div class="payout__axis" aria-hidden="true">
                <span></span>
                <?php foreach ($payoutStages as $stage): ?>
                <span><b><?= htmlspecialchars($stage['name']) ?></b><?= htmlspecialchars($stage['when']) ?></span>
                <?php endforeach; ?>
            </div>
            <?php foreach ($payoutLanes as $lane): ?>
            <div class="payout__lane">
                <div class="payout__lane_title">
                    <h3><?= htmlspecialchars($lane['title']) ?></h3>
                    <p><?= htmlspecialchars($lane['note']) ?></p>
                </div>
                <?php foreach ($payoutStages as $i => $stage):
                    $share = $lane['payouts'][$i] ?? 0;
                ?>
                <div class="payout__cell<?= $share ? ' payout__cell--paid' : '' ?>">
                    <?php if ($share): ?>
                    <span class="payout__stage"><?= htmlspecialchars($stage['name']) ?><small><?= htmlspecialchars($stage['when']) ?></small></span>
                    <span class="payout__share"><?= $share ?>%</span>
                    <span class="payout__sum"><?= $fmt($exampleFee * $share / 100) ?> ₽</span>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 4. Как работаем -->
<section>
    <h2>Как мы работаем</h2>
    <ol class="ipoteka__steps partner__steps">
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

<!-- 5. Брокер-тур и экскурсия -->
<section>
    <h2>Брокер-тур</h2>
    <div class="ipoteka__land partner__tour">
        <?php if ($tourImage): ?>
        <img class="ipoteka__land_image" src="<?= asset('/assets/images/' . $tourImage) ?>" alt="Брокер-тур в построенный каркасный дом" loading="lazy">
        <?php endif; ?>
        <div class="partner__tour_text">
            <p class="ipoteka__lead">Покажем дом, который построили, — чтобы вы рекомендовали клиентам то, что видели своими глазами. Проводим по записи, о ближайшей дате сообщит менеджер.</p>
            <ul class="ipoteka__final_cta_list">
                <?php foreach ($tourItems as $item): ?>
                <li><?= htmlspecialchars($item) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="button" onclick="<?= $openForm ?>">Записаться на брокер-тур</button>
        </div>
    </div>
    <div class="ipoteka__inline_cta partner__excursion">
        <p>Хотите посмотреть, как мы строим? Организуем экскурсию на строящийся объект.</p>
        <button type="button" class="button" onclick="<?= $openForm ?>">Записаться на экскурсию</button>
    </div>
</section>

<!-- 2.1. Факты о компании -->
<section>
    <h2>Надёжный партнёр на рынке ИЖС</h2>
    <ul class="partner__facts">
        <?php foreach ($facts as $fact): ?>
        <li>
            <b><?= htmlspecialchars($fact['value']) ?></b>
            <p><?= htmlspecialchars($fact['text']) ?></p>
        </li>
        <?php endforeach; ?>
    </ul>
</section>

<!-- 6. Менеджер -->
<section>
    <h2>Ваш менеджер</h2>
    <div class="ipoteka__example_card partner__manager">
        <?php if (!empty($manager['photo'])): ?>
        <img class="partner__manager_photo" src="<?= asset('/assets/images/' . $manager['photo']) ?>" alt="<?= htmlspecialchars($manager['name']) ?>" loading="lazy">
        <?php endif; ?>
        <div class="partner__manager_text">
            <h3 class="ipoteka__h3"><?= htmlspecialchars($manager['name']) ?></h3>
            <p class="partner__manager_role"><?= htmlspecialchars($manager['role']) ?></p>
            <p class="ipoteka__lead">Отвечает на вопросы, готовит договор, сопровождает ваших клиентов и следит за выплатами.</p>
            <div class="partner__manager_contacts">
                <a class="partner__manager_phone" href="tel:<?= htmlspecialchars(preg_replace('/[^\d+]/', '', $manager['phone'])) ?>"><?= htmlspecialchars($manager['phone']) ?></a>
                <?php if (!empty($manager['telegram'])): ?>
                <a href="<?= htmlspecialchars($manager['telegram']) ?>" target="_blank" rel="noopener" aria-label="Написать в Telegram"><img src="<?= asset('/assets/icons/telegram-colored-bg.svg') ?>" alt="" width="30" height="30"></a>
                <?php endif; ?>
                <?php if (!empty($manager['max'])): ?>
                <a href="<?= htmlspecialchars($manager['max']) ?>" target="_blank" rel="noopener" aria-label="Написать в MAX"><img src="<?= asset('/assets/icons/max_colored_no_bg.svg') ?>" alt="" width="30" height="30"></a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- 7. FAQ (из Directus: faq, show_on содержит partner) -->
<?php if (!empty($faqs)): ?>
<section>
    <h2>Частые вопросы</h2>
    <?php foreach ($faqs as $f): ?>
        <?php include 'includes/cards/faq_card.php'; ?>
    <?php endforeach; ?>
</section>
<?php endif; ?>

<!-- 8. Финальный CTA -->
<section>
    <div class="ipoteka__final_cta partner__final_cta"<?php if ($heroImage): ?> style="--cta-image: url('<?= asset('/assets/images/' . $heroImage) ?>')"<?php endif; ?>>
        <div class="ipoteka__final_cta_content">
            <h2>Давайте познакомимся</h2>
            <p>Оставьте контакты — менеджер расскажет об условиях и пришлёт агентский договор.</p>
            <ul class="ipoteka__final_cta_list">
                <li>Познакомиться и обсудить условия</li>
                <li>Передать клиента</li>
                <li>Записаться на брокер-тур или экскурсию</li>
            </ul>
        </div>
        <div class="ipoteka__final_cta_action">
            <?php if ($formCode): ?>
            <?= $formCode ?>
            <button type="button" class="button big" id="partner_form_trigger">Оставить заявку</button>
            <?php else: ?>
            <a class="button big" id="partner_form_trigger" href="<?= htmlspecialchars($manager['telegram']) ?>" target="_blank" rel="noopener">Написать менеджеру</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php if (!empty($seo['seo_text'])): ?>
<section>
    <?php include 'includes/seo_text.php'; ?>
</section>
<?php endif; ?>
