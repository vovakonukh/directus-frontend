<?php
// ===== Данные из Directus =====
// Синглтон ipoteka
$ipotekaData = fetchItems('ipoteka', [
    'fields' => 'id,hero_header,hero_subtitle,hero_button_link,trust_items,matcap_first,matcap_second,example_price,docs_borrower,docs_land,cta_header,cta_text,cta_items,cta_form'
]);
$ipoteka = (is_array($ipotekaData) && isset($ipotekaData[0]) ? $ipotekaData[0] : $ipotekaData) ?: [];

// Плашки преимуществ (ipoteka_tiles → tiles, type = advantage)
$advantages = [];
if (!empty($ipoteka['id'])) {
    $tilesJunction = fetchItems('ipoteka_tiles', [
        'filter[ipoteka_id][_eq]' => $ipoteka['id'],
        'filter[type][_eq]' => 'advantage',
        'fields' => 'tiles_id.icon,tiles_id.header,tiles_id.text',
        'sort' => 'sort'
    ]);
    foreach ($tilesJunction ?? [] as $item) {
        if (!empty($item['tiles_id'])) $advantages[] = $item['tiles_id'];
    }
}

// Ипотечные программы
$programs = fetchItems('ipoteka_programs', [
    'filter[status][_eq]' => 'published',
    'sort' => 'sort',
    'fields' => 'name,image,description,button_text,rate,pv,srok,max_amount,badge,is_highlighted'
]) ?? [];

// FAQ
$faqs = fetchItems('faq', [
    'filter[status][_eq]' => 'published',
    'filter[show_on][_contains]' => 'ipoteka',
    'sort' => 'sort',
    'fields' => 'question,answer'
]) ?? [];

// Форма Bitrix для финального CTA
$ctaFormCode = '';
if (!empty($ipoteka['cta_form'])) {
    $ctaForm = fetchItems('forms', [
        'filter[id][_eq]' => $ipoteka['cta_form'],
        'fields' => 'bitrix_code'
    ]);
    $ctaFormCode = $ctaForm[0]['bitrix_code'] ?? '';
}
// Скрипт click-формы Bitrix вешает открытие только на элемент сразу после себя.
// Поэтому скрипт выводится один раз (финальный CTA), а остальные кнопки кликают по его кнопке
$openForm = "document.getElementById('ipoteka_form_trigger').click()";

// H1: из pages_seo ($seo задаётся в index.php), запасной вариант — hero_header синглтона
$heroTitle = trim($seo['h1'] ?? '') ?: trim($ipoteka['hero_header'] ?? '');

// ===== Хардкод (раздел 2 ТЗ) =====
$heroNote = '*Ставка 6% — по семейной ипотеке на строительство дома при наличии ребёнка до 7 лет и соблюдении условий банка.';
$matcapText = 'Маткапитал можно направить на первоначальный взнос или на досрочное погашение кредита. Мы рекомендуем первый вариант: свои деньги останутся свободными — на забор, благоустройство участка и обустройство дома после переезда. Или их можно положить на вклад.';
$brokerText = 'В компании работает ипотечный брокер — специалист, который помогает получить одобрение. Ещё ничего не одобрено? Идите не в банк, а сразу к нам.';
// TODO: картинка CTA — сейчас заглушка
$ctaImage = '622c4284-b1b4-426a-b7d1-53124a693fd5';
$disclaimer = 'Информация носит справочный характер и не является публичной офертой. Ставки, лимиты и требования банков могут меняться — актуальные условия уточняются при подаче заявки. Льготная ставка действует при выполнении условий государственной программы и банка.';

$tiles = [
    'approval' => [
        ['header' => 'Проще всего — сотрудникам по найму', 'text' => 'Официальное трудоустройство и подтверждённый доход: 2-НДФЛ, справка по форме банка или выписка по счёту.'],
        ['header' => 'ИП и самозанятым — тоже реально', 'text' => 'Банк строже проверит доход и попросит больше документов. Разберём вашу ситуацию и подскажем подходящий банк.'],
        ['header' => 'Если уже отказали — не страшно', 'text' => 'Отказ одного банка не значит отказ всех. Брокер разберёт причину и подаст заявку туда, где шансы выше.'],
    ],
    'broker' => [
        ['header' => 'Выше шанс одобрения', 'text' => 'Если банк отказал, с брокером шанс получить одобрение заметно выше.'],
        ['header' => 'Больше сумма кредита', 'text' => 'Одобрят на бо́льшую сумму, чем при самостоятельной подаче.'],
        ['header' => 'Сразу во все банки', 'text' => 'Одна заявка — и она уходит во все подходящие банки, не нужно ходить по отделениям.'],
        ['header' => 'Помощь с документами', 'text' => 'Подскажет, что собрать, и проверит пакет перед подачей.'],
        ['header' => 'Общается с банком сам', 'text' => 'Отвечает на все вопросы банка — вам не нужно разбираться в требованиях.'],
    ],
    'bank' => [
        ['icon' => '/assets/icons/banks/sber_logo.svg', 'header' => 'Сбербанк'],
        ['icon' => '/assets/icons/banks/vtb_logo.svg', 'header' => 'ВТБ'],
        ['icon' => '/assets/icons/banks/domrf_logo.svg', 'header' => 'Банк ДОМ.РФ'],
    ],
    'land_rule' => [
        ['header' => 'Без зарегистрированных жилых строений', 'text' => 'Если на участке стоит дом или баня с назначением «жилое», банк откажет. Такое строение придётся снять с учёта до сделки.'],
        ['header' => 'Нужно уведомление о планируемом строительстве', 'text' => 'Дачная амнистия для ипотеки не подходит: банку нужно уведомление о соответствии параметров строительства от администрации.'],
        ['header' => 'Подходит', 'text' => 'Пустой участок, бытовки и временные постройки, ВРИ под индивидуальное жилищное строительство. Обременения и ограничения в большинстве случаев согласуем с банком.'],
    ],
    // лимит семейной ипотеки по числу детей
    'family_limit' => [
        ['header' => '1 ребёнок', 'text' => 'до 12 млн ₽'],
        ['header' => '2 ребёнка', 'text' => 'до 15 млн ₽'],
        ['header' => '3 ребёнка и более', 'text' => 'до 18 млн ₽'],
    ],
    'family_rule' => [
        ['header' => '6% — на первые 15 лет', 'text' => 'Кредит выдаётся на срок до 30 лет, но ставка субсидируется 15 лет. Дальше она станет 10% или ключевая ставка + 3% — применяется то, что выгоднее вам.'],
        ['header' => 'Регистрация в доме', 'text' => 'В течение 271 дня после оформления права собственности нужно зарегистрироваться в доме и подтверждать регистрацию 5 лет.'],
    ],
    'step' => [
        ['header' => 'Консультация', 'text' => 'Разбираем ситуацию, считаем бюджет, говорим, какие документы нужны.'],
        ['header' => 'Предварительное одобрение', 'text' => 'Брокер подаёт заявку сразу в несколько банков.'],
        ['header' => 'Участок', 'text' => 'Проверяем участок на ограничения и получаем все разрешения.'],
        ['header' => 'Согласование объекта', 'text' => 'Проект, смета и наша компания как подрядчик проходят проверку банка.'],
        ['header' => 'Сделка', 'text' => 'Подписываем договор подряда, банк открывает эскроу-счёт.'],
        ['header' => 'Строительство', 'text' => 'Строим по договору, банк контролирует этапы.'],
        ['header' => 'Ключи и регистрация', 'text' => 'Сдаём дом, регистрируем, банк раскрывает эскроу.'],
    ],
];
// ===== Конец хардкода =====

$fmt = fn($x) => number_format($x, 0, '', ' ');
$fmtRub = fn($x) => number_format($x, 2, ',', ' ');
$fmtPercent = fn($x) => str_replace('.', ',', (string) (float) $x);

// Расчёты примеров — из первой выделенной программы (льготная) и example_price
$calc = null;
foreach ($programs as $prog) {
    if (!empty($prog['is_highlighted'])) {
        if (!empty($prog['rate']) && !empty($prog['pv']) && !empty($prog['srok']) && !empty($ipoteka['example_price'])) {
            $price = (float) $ipoteka['example_price'];
            $pvPercent = (float) $prog['pv'];
            $downPayment = (int) round($price * $pvPercent / 100);
            $calc = [
                'price' => $price,
                'pv' => $pvPercent,
                'rate' => (float) $prog['rate'],
                'srok' => (int) $prog['srok'],
                'down_payment' => $downPayment,
                'payment' => annuityPayment($price - $downPayment, (float) $prog['rate'], (int) $prog['srok']),
            ];
        }
        break;
    }
}

// Варианты маткапитала: подписи — хардкод, суммы — из Directus; пустые не выводятся
$matcaps = [];
foreach (['matcap_first' => '1 ребёнок', 'matcap_second' => '2 ребёнка и более'] as $field => $label) {
    if (!empty($ipoteka[$field])) $matcaps[] = ['label' => $label, 'amount' => (float) $ipoteka[$field]];
}

// JSON-LD FAQPage
$ldFaq = null;
if (!empty($faqs)) {
    $ldFaq = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => array_map(fn($faq) => [
            '@type' => 'Question',
            'name' => $faq['question'],
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => trim(strip_tags($faq['answer'] ?? ''))],
        ], $faqs),
    ];
}
?>

<!-- 1. Hero -->
<section class="ipoteka__hero">
    <div class="ipoteka__hero_inner">
        <?php if ($heroTitle): ?>
        <h1><?= htmlspecialchars($heroTitle) ?></h1>
        <?php endif; ?>
        <?php if (!empty($ipoteka['hero_subtitle'])): ?>
        <p class="ipoteka__hero_subtitle"><?= htmlspecialchars($ipoteka['hero_subtitle']) ?></p>
        <?php endif; ?>
        <?php if (!empty($ipoteka['trust_items'])): ?>
        <ul class="ipoteka__trust">
            <?php foreach ($ipoteka['trust_items'] as $trust): ?>
            <?php if (!empty($trust['text'])): ?>
            <li><?= htmlspecialchars($trust['text']) ?></li>
            <?php endif; ?>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
        <?php if (!empty($ipoteka['hero_button_link'])): ?>
        <a href="<?= htmlspecialchars($ipoteka['hero_button_link']) ?>" class="button_image ipoteka__hero_button">
            <span>Рассчитать стоимость</span>
            <span>и получить консультацию</span>
            <img src="/assets/icons/calculator.webp" alt="">
        </a>
        <?php endif; ?>
        <p class="ipoteka__hero_note"><?= htmlspecialchars($heroNote) ?></p>
    </div>
</section>

<!-- 2. Преимущества -->
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

<!-- 3. Ипотечные программы -->
<?php if (!empty($programs)): ?>
<section>
    <h2>Ипотечные программы</h2>
    <div class="ipoteka__programs_v2">
        <?php foreach ($programs as $prog):
            // у выделенной программы ставка без «от»
            $rate = !empty($prog['rate']) ? (empty($prog['is_highlighted']) ? 'от ' : '') . $fmtPercent($prog['rate']) . '%' : '';
            $pv = !empty($prog['pv']) ? 'от ' . $fmtPercent($prog['pv']) . '%' : '';
        ?>
        <div class="ipoteka__program_v2<?= !empty($prog['is_highlighted']) ? ' ipoteka__program_card--highlighted' : '' ?>">
            <div class="ipoteka__program_head">
                <?php if (!empty($prog['image'])): ?>
                    <img class="ipoteka__program_image" src="<?= getAssetUrl($prog['image']) ?>?width=160" alt="<?= htmlspecialchars($prog['name']) ?>" loading="lazy">
                <?php endif; ?>
                <div>
                    <?php if (!empty($prog['badge'])): ?>
                        <span class="ipoteka__program_badge"><?= htmlspecialchars($prog['badge']) ?></span>
                    <?php endif; ?>
                    <h3><?= htmlspecialchars($prog['name']) ?></h3>
                    <?php if (!empty($prog['description'])): ?>
                        <p class="ipoteka__program_note"><?= htmlspecialchars($prog['description']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="ipoteka__program_params">
                <?php if ($rate): ?>
                <div><b><?= htmlspecialchars($rate) ?></b>ставка</div>
                <?php endif; ?>
                <?php if ($pv): ?>
                <div><b><?= htmlspecialchars($pv) ?></b>первый взнос</div>
                <?php endif; ?>
                <?php if (!empty($prog['srok'])): ?>
                <div><b>до <?= $fmt((float) $prog['srok']) ?> лет</b>срок</div>
                <?php endif; ?>
                <?php if (!empty($prog['max_amount'])): ?>
                <div><b><?= htmlspecialchars($prog['max_amount']) ?></b>сумма</div>
                <?php endif; ?>
            </div>

            <?php if (!empty($prog['button_text'])): ?>
                <button type="button" class="button ipoteka__program_button" onclick="<?= $openForm ?>"><?= htmlspecialchars($prog['button_text']) ?></button>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>


</section>
<?php endif; ?>

<!-- 4. Условия льготной ипотеки -->
<section>
    <h2>Условия льготной ипотеки</h2>
    <div class="ipoteka__split">
        <div class="ipoteka__example_card">
            <span class="ipoteka__family_title">Максимальная сумма кредита</span>
            <?php foreach ($tiles['family_limit'] as $limit): ?>
            <div class="ipoteka__example_row ipoteka__family_row"><?= htmlspecialchars($limit['header']) ?> <span><?= htmlspecialchars($limit['text']) ?></span></div>
            <?php endforeach; ?>
            <p class="ipoteka__program_note ipoteka__family_note">Хотя бы один ребёнок должен быть младше 7 лет, возраст старших детей не важен. Для усыновлённых детей правила те же.</p>
        </div>
        <div class="ipoteka__tiles ipoteka__tiles--stack">
            <?php foreach ($tiles['family_rule'] as $tile): ?>
            <div class="ipoteka__tile">
                <h3><?= htmlspecialchars($tile['header']) ?></h3>
                <p><?= htmlspecialchars($tile['text']) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 5. Материнский капитал (пример выводится, если есть расчёт и хотя бы одна сумма маткапитала) -->
<?php if ($calc && $matcaps): ?>
<section>
    <h2>Материнский капитал — в первоначальный взнос</h2>
    <div class="ipoteka__split">
        <p class="ipoteka__lead"><?= htmlspecialchars($matcapText) ?></p>
        <div class="ipoteka__example_card" x-data="{ kids: 0 }">
            <?php if (count($matcaps) > 1): ?>
            <div class="ipoteka__switch">
                <?php foreach ($matcaps as $i => $mc): ?>
                <button type="button" :class="{ 'active': kids === <?= $i ?> }" @click="kids = <?= $i ?>"><?= htmlspecialchars($mc['label']) ?></button>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <div class="ipoteka__example_row">Дом <span><?= $fmt($calc['price']) ?> ₽</span></div>
            <div class="ipoteka__example_row">Первоначальный взнос <?= $fmtPercent($calc['pv']) ?>% <span><?= $fmt($calc['down_payment']) ?> ₽</span></div>
            <div class="ipoteka__example_row">Маткапитал
                <?php foreach ($matcaps as $i => $mc): ?>
                <span x-show="kids === <?= $i ?>"<?= $i ? ' style="display: none"' : '' ?>>− <?= $fmtRub($mc['amount']) ?> ₽</span>
                <?php endforeach; ?>
            </div>
            <div class="ipoteka__example_total">
                Из своих средств
                <?php foreach ($matcaps as $i => $mc): ?>
                <span x-show="kids === <?= $i ?>"<?= $i ? ' style="display: none"' : '' ?>><?= $fmtRub(max(0, $calc['down_payment'] - $mc['amount'])) ?> ₽</span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- 6. Ипотечный брокер -->
<section>
    <h2>Ипотечный брокер в нашей команде</h2>
    <p class="ipoteka__lead ipoteka__broker_text"><?= htmlspecialchars($brokerText) ?></p>
    <div class="ipoteka__tiles ipoteka__tiles--broker">
        <?php foreach ($tiles['broker'] as $tile): ?>
        <div class="ipoteka__tile">
            <h3><?= htmlspecialchars($tile['header']) ?></h3>
            <p><?= htmlspecialchars($tile['text']) ?></p>
        </div>
        <?php endforeach; ?>
        <div class="ipoteka__tile ipoteka__tile--cta">
            <h3>Оставьте заявку — брокер оценит ваши шансы и подберёт банки</h3>
            <button type="button" class="button" onclick="<?= $openForm ?>">Оставить заявку на одобрение</button>
        </div>
    </div>

    <?php if (!empty($tiles['bank'])): ?>
    <div class="ipoteka__banks">
        <span class="ipoteka__banks_title">Работаем с крупными ипотечными банками</span>
        <div class="ipoteka__banks_logos">
            <?php foreach ($tiles['bank'] as $bank): ?>
                <img src="<?= htmlspecialchars($bank['icon']) ?>" alt="<?= htmlspecialchars($bank['header']) ?>" loading="lazy">
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</section>

<!-- 7. Кому одобряют + пример платежа -->
<section>
    <h2>Кому одобрят ипотеку</h2>
    <div class="ipoteka__tiles">
        <?php foreach ($tiles['approval'] as $tile): ?>
        <div class="ipoteka__tile">
            <h3><?= htmlspecialchars($tile['header']) ?></h3>
            <p><?= htmlspecialchars($tile['text']) ?></p>
        </div>
        <?php endforeach; ?>
    </div>
    <?php if ($calc): ?>
    <div class="ipoteka__payment">
        <span class="ipoteka__payment_label">Ежемесячный платёж</span>
        <span class="ipoteka__payment_value">от <?= $fmt($calc['payment']) ?> ₽/мес</span>
        <span class="ipoteka__payment_caption">дом за <?= $fmtPercent(round($calc['price'] / 1000000, 1)) ?> млн ₽, взнос <?= $fmtPercent($calc['pv']) ?>%, ставка <?= $fmtPercent($calc['rate']) ?>%, срок <?= $calc['srok'] ?> лет</span>
    </div>
    <?php endif; ?>
</section>

<!-- 8. Требования к участку -->
<section>
    <h2>Участок должен быть готов к ипотеке</h2>
    <div class="ipoteka__land">
        <img class="ipoteka__land_image" src="/assets/images/uchastok.webp" alt="Участок под строительство дома в ипотеку" loading="lazy">
        <div class="ipoteka__tiles ipoteka__tiles--land">
            <?php foreach ($tiles['land_rule'] as $tile): ?>
            <div class="ipoteka__tile">
                <h3><?= htmlspecialchars($tile['header']) ?></h3>
                <p><?= htmlspecialchars($tile['text']) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="ipoteka__inline_cta">
        <p>Пришлите кадастровый номер — бесплатно проверим участок до подачи заявки</p>
        <button type="button" class="button" onclick="<?= $openForm ?>">Проверить участок</button>
    </div>
</section>

<!-- 9. Документы -->
<?php if (!empty($ipoteka['docs_borrower']) || !empty($ipoteka['docs_land'])): ?>
<section>
    <h2>Какие документы понадобятся</h2>
    <div class="ipoteka__docs_v2">
        <?php foreach (['docs_borrower' => 'От вас', 'docs_land' => 'По участку'] as $field => $title): ?>
        <?php if (!empty($ipoteka[$field])): ?>
        <div class="ipoteka__docs_col">
            <h3><?= $title ?></h3>
            <ul>
                <?php foreach ($ipoteka[$field] as $doc): ?>
                <?php if (!empty($doc['title'])): ?>
                <li><b><?= htmlspecialchars($doc['title']) ?></b><?= !empty($doc['text']) ? ' — ' . htmlspecialchars($doc['text']) : '' ?></li>
                <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- 10. Этапы -->
<section>
    <h2>От консультации до ключей</h2>
    <ol class="ipoteka__steps">
        <?php foreach ($tiles['step'] as $i => $step): ?>
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

<!-- 11. Договор -->
<section class="service__contract_section">
    <div class="service__contract_inner">
        <div class="service__contract_col_1">
            <h2>Вы защищены по договору на 100%</h2>
            <p class="service__description">
                Перед началом работ мы заключаем договор, поэтому вы защищены законодательством РФ
            </p>
        </div>
        <div class="service__contract_col_2">
            <h3>В договоре зафиксированы:</h3>
            <div class="service__contract_list">
                <img src="/assets/icons/service__money.webp" alt="" loading="lazy" />
                <p>Стоимость работ</p>
            </div>
            <div class="service__contract_list">
                <img src="/assets/icons/service__payments.webp" alt="" loading="lazy" />
                <p>Порядок оплаты</p>
            </div>
            <div class="service__contract_list">
                <img src="/assets/icons/service__period.webp" alt="" loading="lazy" />
                <p>Сроки работы</p>
            </div>
            <div class="service__contract_list">
                <img src="/assets/icons/service__warranty.webp" alt="" loading="lazy" />
                <p>Гарантия 5 лет</p>
            </div>
            <div class="service__contract_list">
                <img src="/assets/icons/service__smeta.webp" alt="" loading="lazy" />
                <p>Спецификация используемых материалов</p>
            </div>
        </div>
    </div>
</section>

<!-- 12. FAQ -->
<?php if (!empty($faqs)): ?>
<section>
    <h2>Частые вопросы по ипотеке</h2>
    <?php foreach ($faqs as $faq): ?>
    <details class="faq__card">
        <summary class="faq__header"><?= htmlspecialchars($faq['question']) ?></summary>
        <p><?= nl2br(htmlspecialchars($faq['answer'] ?? '')) ?></p>
    </details>
    <?php endforeach; ?>
</section>
<script type="application/ld+json"><?= json_encode($ldFaq, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) ?></script>
<?php endif; ?>

<!-- 13. Финальный CTA -->
<section>
    <div class="ipoteka__final_cta"<?php if (!empty($ctaImage)): ?> style="--cta-image: url('<?= getAssetUrl($ctaImage) ?>?width=1600')"<?php endif; ?>>
        <div class="ipoteka__final_cta_content">
            <?php if (!empty($ipoteka['cta_header'])): ?>
            <h2><?= htmlspecialchars($ipoteka['cta_header']) ?></h2>
            <?php endif; ?>
            <?php if (!empty($ipoteka['cta_text'])): ?>
            <p><?= htmlspecialchars($ipoteka['cta_text']) ?></p>
            <?php endif; ?>
            <?php if (!empty($ipoteka['cta_items'])): ?>
            <ul class="ipoteka__final_cta_list">
                <?php foreach ($ipoteka['cta_items'] as $ctaItem): ?>
                <?php if (!empty($ctaItem['text'])): ?>
                <li><?= htmlspecialchars($ctaItem['text']) ?></li>
                <?php endif; ?>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
        </div>
        <div class="ipoteka__final_cta_action">
            <?= $ctaFormCode ?>
            <button type="button" class="button big" id="ipoteka_form_trigger">Получить расчёт</button>
        </div>
    </div>
</section>

<!-- 14. Дисклеймер -->
<section>
    <p class="ipoteka__disclaimer"><?= htmlspecialchars($disclaimer) ?></p>
</section>

<!-- 15. SEO-текст из pages_seo ($seo задаётся в index.php) -->
<?php if (!empty($seo['seo_text'])): ?>
<section>
    <?php include 'includes/seo_text.php'; ?>
</section>
<?php endif; ?>
