<?php
// Блок «Комплектация» (новый дизайн). Старый вариант: _archive/complectation/
// Использует из pages/projects_item.php: $project, $complectation, $priceSvai, $pricePlita, $priceInzh, $formCode

$kPrices = [
    'tk'    => (int) ($project['price_tk_auto'] ?? 0),
    'svai'  => (int) $priceSvai,
    'plita' => (int) $pricePlita,
    'inzh'  => min(array_filter($priceInzh) ?: [0]), // для «от» в шапке этапа
    'inzh_convector'    => $priceInzh['convector'],
    'inzh_heated_floor' => $priceInzh['heated_floor'],
    'otd'   => (int) ($project['price_otdelka'] ?? 0),
];

// Характеристики по этапам: ['tk' => [[label, value], ...], ...]
$kSpecs = [];
foreach ($complectation as $item) {
    $kSpecs[$item['stage']][] = $item;
}

$kRub = fn($n) => number_format($n, 0, '', ' ') . ' ₽';

// Сетка характеристик этапа
$kSpecsGrid = function ($stage) use ($kSpecs) {
    if (empty($kSpecs[$stage])) return;
    echo '<dl class="kompl__specs">';
    foreach ($kSpecs[$stage] as $item) {
        echo '<div><dt>' . htmlspecialchars($item['label']) . '</dt><dd>' . htmlspecialchars($item['value']) . '</dd></div>';
    }
    echo '</dl>';
};

// Варианты отопления в инженерке: ключ => название
$kInzhTypes = ['convector' => 'Конвекторы', 'heated_floor' => 'Тёплый пол'];

// Этапы-переключатели (кроме фундамента)
$kToggles = [
    'tk'   => ['num' => '02', 'key' => 'tk',   'title' => 'Тёплый контур',   'stage' => 'tk',         'from' => false, 'note' => 'Основа дома: каркас, утепление, кровля, окна и двери'],
    'inzh' => ['num' => '03', 'key' => 'inzh', 'title' => 'Инженерные сети', 'stage' => 'inzhenerka', 'from' => true,  'note' => 'Электрика, водоснабжение, канализация, отопление'],
    'otd'  => ['num' => '04', 'key' => 'otd',  'title' => 'Отделка',         'stage' => 'otdelka',    'from' => true,  'note' => 'Внутренняя отделка под ключ'],
];
?>
<!-- Комплектация -->
<section id="complectation" class="kompl" x-data="komplektaciya(<?= htmlspecialchars(json_encode($kPrices), ENT_QUOTES) ?>)">
    <div class="kompl__head">
        <h2>Комплектация</h2>
        <p>Соберите дом по этапам — стоимость пересчитается автоматически</p>
    </div>

    <div class="kompl__layout">
        <div class="kompl__stages">

            <!-- 01 Фундамент: один вариант или ни одного -->
            <?php if ($kPrices['svai'] > 0 || $kPrices['plita'] > 0): ?>
            <div class="kompl__stage" :class="fund && 'is-on'">
                <div class="kompl__stage_head">
                    <span class="kompl__num">01</span>
                    <div class="kompl__stage_title">
                        <span>Фундамент</span>
                        <small>Окончательно определиться с типом фундамента можно только после пробного бурения и анализа вашего участка</small>
                    </div>
                </div>

                <div class="kompl__options" role="radiogroup" aria-label="Тип фундамента">
                    <label class="kompl__option">
                        <input type="radio" name="kompl_fund" value="" x-model="fund">
                        <span class="kompl__option_name">Без фундамента</span>
                        <span class="kompl__option_price">Есть свой</span>
                    </label>
                    <?php if ($kPrices['svai'] > 0): ?>
                    <label class="kompl__option">
                        <input type="radio" name="kompl_fund" value="svai" x-model="fund">
                        <span class="kompl__option_name">ЖБ сваи</span>
                        <span class="kompl__option_price"><?= $kRub($kPrices['svai']) ?></span>
                    </label>
                    <?php endif; ?>
                    <?php if ($kPrices['plita'] > 0): ?>
                    <label class="kompl__option">
                        <input type="radio" name="kompl_fund" value="plita" x-model="fund">
                        <span class="kompl__option_name">ЖБ плита</span>
                        <span class="kompl__option_price"><?= $kRub($kPrices['plita']) ?></span>
                    </label>
                    <?php endif; ?>
                </div>

                <div x-show="fund === 'svai'" x-cloak><?php $kSpecsGrid('svai'); ?></div>
                <div x-show="fund === 'plita'" x-cloak><?php $kSpecsGrid('plita'); ?></div>
            </div>
            <?php endif; ?>

            <!-- 02–04 Этапы-переключатели -->
            <?php foreach ($kToggles as $t): if ($kPrices[$t['key']] <= 0) continue; ?>
            <div class="kompl__stage" :class="<?= $t['key'] ?> && 'is-on'">
                <label class="kompl__stage_head is-toggle">
                    <span class="kompl__num"><?= $t['num'] ?></span>
                    <span class="kompl__stage_title">
                        <span><?= $t['title'] ?></span>
                        <small><?= $t['note'] ?></small>
                    </span>
                    <span class="kompl__stage_price"><?= $t['from'] ? '<small>от</small> ' : '' ?><?= $kRub($kPrices[$t['key']]) ?></span>
                    <input type="checkbox" class="kompl__switch" x-model="<?= $t['key'] ?>" aria-label="<?= $t['title'] ?>">
                </label>

                <?php if ($t['key'] === 'inzh'): ?>
                <!-- Вариант отопления: виден, когда этап включён -->
                <div class="kompl__options kompl__options--2" role="radiogroup" aria-label="Вариант отопления" x-show="inzh" x-cloak>
                    <?php foreach ($kInzhTypes as $type => $name): if ($kPrices['inzh_' . $type] <= 0) continue; ?>
                    <label class="kompl__option">
                        <input type="radio" name="kompl_inzh" value="<?= $type ?>" x-model="inzhType">
                        <span class="kompl__option_name"><?= $name ?></span>
                        <span class="kompl__option_price"><?= $kRub($kPrices['inzh_' . $type]) ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <?php if (!empty($kSpecs[$t['stage']])): ?>
                <details class="kompl__details" <?= $t['key'] === 'tk' ? 'open' : '' ?>>
                    <summary<?= $t['key'] === 'inzh' ? ' x-text="inzh ? \'Что ещё входит\' : \'Что входит\'"' : '' ?>>Что входит</summary>
                    <?php $kSpecsGrid($t['stage']); ?>
                    <?php if ($t['key'] === 'inzh'): foreach ($kInzhTypes as $type => $name): ?>
                    <div x-show="inzhType === '<?= $type ?>'" <?= $type === 'convector' ? '' : 'x-cloak' ?>><?php $kSpecsGrid('inzh_' . $type); ?></div>
                    <?php endforeach; endif; ?>
                </details>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>

        </div>

        <!-- Итог: на десктопе липнет справа -->
        <aside class="kompl__summary">
            <div class="kompl__summary_title">Ваш дом</div>
            <ul class="kompl__summary_list">
                <li x-show="fund === 'svai'" x-cloak><span>Фундамент: ЖБ сваи</span><span><?= $kRub($kPrices['svai']) ?></span></li>
                <li x-show="fund === 'plita'" x-cloak><span>Фундамент: ЖБ плита</span><span><?= $kRub($kPrices['plita']) ?></span></li>
                <?php foreach ($kToggles as $t): if ($kPrices[$t['key']] <= 0) continue; ?>
                <?php if ($t['key'] === 'inzh'): foreach ($kInzhTypes as $type => $name): if ($kPrices['inzh_' . $type] <= 0) continue; ?>
                <li x-show="inzh && inzhType === '<?= $type ?>'" x-cloak><span><?= $t['title'] ?>: <?= mb_strtolower($name) ?></span><span><?= $kRub($kPrices['inzh_' . $type]) ?></span></li>
                <?php endforeach; continue; endif; ?>
                <li x-show="<?= $t['key'] ?>" <?= $t['key'] === 'tk' ? '' : 'x-cloak' ?>><span><?= $t['title'] ?></span><span><?= $kRub($kPrices[$t['key']]) ?></span></li>
                <?php endforeach; ?>
                <li class="is-empty" x-show="total === 0" x-cloak><span>Выберите хотя бы один этап</span></li>
            </ul>
            <div class="kompl__summary_total">
                <span>Итого</span>
                <strong><span x-text="totalFormatted"><?= number_format($kPrices['tk'], 0, '', ' ') ?></span> ₽</strong>
            </div>
            <div class="kompl__summary_note">Стоимость в ипотеку уточняйте у менеджера</div>
            <?= $formCode ?>
            <div class="button big kompl__summary_button">Оставить заявку</div>
        </aside>
    </div>

    <!-- Мобильная плавающая панель с ценой (на десктопе скрыта, там есть aside) -->
    <div class="kompl__pane"
        x-show="total > 0"
        x-intersect:enter="$dispatch('sticky-pane-visible', true)"
        x-intersect:leave="$dispatch('sticky-pane-visible', false)">
        <div class="kompl__pane_price">
            <span>Итого</span>
            <strong><span x-text="totalFormatted"></span> ₽</strong>
            <small>Цену в ипотеку уточняйте у менеджера</small>
        </div>
        <?= $formCode ?>
        <div class="button kompl__pane_button">Оставить заявку</div>
    </div>

    <!-- Метка на «родном» месте панели: когда она поднялась на 90px над низом экрана,
         панель уже отлипла — нижнее мобильное меню возвращается -->
    <div class="kompl__pane_sentinel"
        x-intersect:enter.margin.99999px.0px.-90px.0px="$dispatch('sticky-pane-released', true)"
        x-intersect:leave.margin.99999px.0px.-90px.0px="$dispatch('sticky-pane-released', false)"></div>
</section>

<script>
function komplektaciya(prices) {
    return {
        prices,
        fund: '',      // '' | 'svai' | 'plita'
        tk: true,
        inzh: false,
        inzhType: prices.inzh_convector > 0 ? 'convector' : 'heated_floor',
        otd: false,
        get total() {
            return (this.fund ? this.prices[this.fund] : 0)
                + (this.tk ? this.prices.tk : 0)
                + (this.inzh ? this.prices['inzh_' + this.inzhType] : 0)
                + (this.otd ? this.prices.otd : 0);
        },
        get totalFormatted() {
            return new Intl.NumberFormat('ru-RU').format(this.total);
        },
        init() {
            // ипотечный калькулятор подхватывает сумму
            this.$watch('total', sum => this.$dispatch('project-total', sum));
        }
    }
}
</script>
