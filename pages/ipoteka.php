<?php
// Синглтон страницы ипотеки
$ipotekaData = fetchItems('ipoteka', ['fields' => '*']);
$ipoteka = is_array($ipotekaData) && isset($ipotekaData[0]) ? $ipotekaData[0] : $ipotekaData;

// Tiles через junction
$tilesJunction = fetchItems('ipoteka_tiles', [
    'filter[ipoteka_id][_eq]' => $ipoteka['id'],
    'fields' => 'tiles_id.id,tiles_id.icon,tiles_id.header,tiles_id.text,type,sort',
    'sort' => 'sort'
]);

$advantages = [];
$documents = [];
foreach ($tilesJunction ?? [] as $item) {
    if ($item['type'] === 'advantage') {
        $advantages[] = $item['tiles_id'];
    } elseif ($item['type'] === 'document') {
        $documents[] = $item['tiles_id'];
    }
}

// Ипотечные программы
$programs = fetchItems('ipoteka_programs', [
    'filter[status][_eq]' => 'published',
    'sort' => 'sort',
    'fields' => '*'
]);

// FAQ
$faqs = fetchItems('faq', [
    'filter[status][_eq]' => 'published',
    'filter[show_on][_contains]' => 'ipoteka',
    'fields' => 'question,answer'
]);

// CTA форма
$ctaFormCode = '';
if (!empty($ipoteka['cta_form'])) {
    $ctaForm = fetchItems('forms', [
        'filter[id][_eq]' => $ipoteka['cta_form'],
        'fields' => 'bitrix_code'
    ]);
    $ctaFormCode = $ctaForm[0]['bitrix_code'] ?? '';
}

// Hero форма (если есть)
$heroFormCode = '';
if (!empty($ipoteka['hero_form'])) {
    $heroForm = fetchItems('forms', [
        'filter[id][_eq]' => $ipoteka['hero_form'],
        'fields' => 'bitrix_code'
    ]);
    $heroFormCode = $heroForm[0]['bitrix_code'] ?? '';
}
?>

<!-- Hero -->
<section class="ipoteka__jumbo">
    <div class="ipoteka__jumbo_inner">
        <h1><?= htmlspecialchars($ipoteka['hero_header']) ?></h1>
        <?php if (!empty($heroFormCode)): ?>
            <?= $heroFormCode ?>
        <?php elseif (!empty($ipoteka['hero_button_link'])): ?>
            <a href="<?= htmlspecialchars($ipoteka['hero_button_link']) ?>" class="button_image">
                <span>Рассчитать стоимость</span>
                <span>и получить консультацию</span>
                <img src="/assets/icons/calculator.webp">
            </a>
        <?php endif; ?>
    </div>
</section>

<!-- Преимущества -->
<?php if (!empty($advantages)): ?>
<section class="ipoteka__advantages">
    <?php foreach ($advantages as $adv): ?>
    <div class="ipoteka__advantage_card">
        <div class="ipoteka__advantage_header">
            <?php if (!empty($adv['icon'])): ?>
                <img src="<?= getAssetUrl($adv['icon']) ?>">
            <?php endif; ?>
            <p><?= htmlspecialchars($adv['header']) ?></p>
        </div>
        <p><?= htmlspecialchars($adv['text']) ?></p>
    </div>
    <?php endforeach; ?>
</section>
<?php endif; ?>

<!-- Ипотечные программы -->
<?php if (!empty($programs)): ?>
<section>
    <h2>Доступные ипотечные программы</h2>
    <div class="ipoteka__programs_wrap">
        <?php foreach ($programs as $prog): ?>
        <div class="ipoteka__program_card">
            <?php if (!empty($prog['image'])): ?>
                <img src="<?= getAssetUrl($prog['image']) ?>" alt="<?= htmlspecialchars($prog['name']) ?>">
            <?php endif; ?>
            <h3><?= htmlspecialchars($prog['name']) ?></h3>

            <?php if (!empty($prog['rate']) || !empty($prog['pv']) || !empty($prog['srok']) || !empty($prog['max_amount'])): ?>
            <div class="ipoteka__params_wrap">
                <?php if (!empty($prog['rate'])): ?>
                <div class="ipoteka__param">
                    Ставка <span>от <?= $prog['rate'] ?>%</span>
                </div>
                <?php endif; ?>
                <?php if (!empty($prog['pv'])): ?>
                <div class="ipoteka__param">
                    Первый взнос <span>от <?= number_format($prog['pv'], 0, '', '') ?>%</span>
                </div>
                <?php endif; ?>
                <?php if (!empty($prog['srok'])): ?>
                <div class="ipoteka__param">
                    Срок <span>до <?= number_format($prog['srok'], 0, '', '') ?> лет</span>
                </div>
                <?php endif; ?>
                <?php if (!empty($prog['max_amount'])): ?>
                <div class="ipoteka__param">
                    Сумма <span><?= htmlspecialchars($prog['max_amount']) ?></span>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($prog['description'])): ?>
                <p><?= htmlspecialchars($prog['description']) ?></p>
            <?php endif; ?>

            <?php if (!empty($prog['button_text'])): ?>
                <a class="button"><?= htmlspecialchars($prog['button_text']) ?></a>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <div class="ipoteka__programs_other">
        <div>
            <img src="/assets/icons/info_green.svg">
            Также работаем с другими вариантами ипотечных программ
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Документы -->
<?php if (!empty($documents)): ?>
<section>
    <h2>Что нужно для оформления ипотеки?</h2>
    <div class="ipoteka__docs_wrap">
        <?php foreach ($documents as $index => $doc): ?>
        <div class="ipoteka__docs_card">
            <span><?= str_pad($index + 1, 2, '0', STR_PAD_LEFT) ?></span>
            <h3><?= htmlspecialchars($doc['header']) ?></h3>
            <?= htmlspecialchars($doc['text']) ?>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- CTA блок -->
<section>
    <div class="ipoteka__cta_wrap">
        <div class="ipoteka__cta_content">
            <h2><?= htmlspecialchars($ipoteka['cta_header']) ?></h2>
            <div>
                <?php foreach ($ipoteka['cta_items'] ?? [] as $ctaItem): ?>
                <div class="ipoteka__cta_list_item">
                    <img src="/assets/icons/logo_classhouse.webp" alt="">
                    <p><?= htmlspecialchars($ctaItem['text']) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="ipoteka__cta_form">
            <?= $ctaFormCode ?>
        </div>
    </div>
</section>

<!-- Договор -->
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
                <img src="/assets/icons/service__money.webp" />
                <p>Стоимость работ</p>
            </div>
            <div class="service__contract_list">
                <img src="/assets/icons/service__payments.webp" />
                <p>Порядок оплаты</p>
            </div>
            <div class="service__contract_list">
                <img src="/assets/icons/service__period.webp" />
                <p>Сроки работы</p>
            </div>
            <div class="service__contract_list">
                <img src="/assets/icons/service__warranty.webp" />
                <p>Гарантия 5 лет</p>
            </div>
            <div class="service__contract_list">
                <img src="/assets/icons/service__smeta.webp" />
                <p>Спецификация используемых материалов</p>
            </div>
        </div>
    </div>
</section>

<!-- FAQ -->
<?php if (!empty($faqs)): ?>
<section class="margin-bottom-50">
    <h2>Частые вопросы по ипотеке</h2>
    <?php foreach ($faqs as $faq): ?>
    <details class="faq__card">
        <summary class="faq__header"><?= htmlspecialchars($faq['question']) ?></summary>
        <p><?= nl2br(htmlspecialchars($faq['answer'])) ?></p>
    </details>
    <?php endforeach; ?>
</section>
<?php endif; ?>