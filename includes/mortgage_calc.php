<?php
// Ипотечный калькулятор. Подключение:
//   $calc = ['price' => 8000000];  // все ключи необязательные
//   include 'includes/mortgage_calc.php';
// Стоимость можно менять событием: $dispatch('project-total', 5000000) —
// так комплектация на странице проекта передаёт выбранную сумму.
// Кнопка CTA открывает форму Bitrix из коллекции forms (form_id) —
// код формы выводится прямо перед кнопкой, как на главной.
$calc = array_merge([
    'price' => 8000000,
    'pv_percent' => 20,
    'rate' => 6,
    'years' => 30,
    'pv_buttons' => [20, 30, 40, 50],
    'years_buttons' => [15, 20, 30],
    'button_text' => 'Получить точный расчёт',
    'form_id' => 4,
], $calc ?? []);

$calcForm = fetchItems('forms', [
    'filter[id][_eq]' => $calc['form_id'],
    'fields' => 'bitrix_code'
]);
$calcFormCode = $calcForm[0]['bitrix_code'] ?? '';

// Стартовый результат считает PHP — цифры видны до загрузки Alpine
$calcPv = (int) round($calc['price'] * $calc['pv_percent'] / 100);
$calcLoan = $calc['price'] - $calcPv;
$calcPayment = annuityPayment($calcLoan, $calc['rate'], $calc['years']);
$calcFmt = fn($x) => number_format($x, 0, '', ' ');
?>
<div class="mortgage_calc" x-data="mortgageCalc(<?= htmlspecialchars(json_encode([
    'price' => $calc['price'],
    'pvPercent' => $calc['pv_percent'],
    'rate' => $calc['rate'],
    'years' => $calc['years'],
]), ENT_QUOTES) ?>)" @project-total.window="setPriceValue($event.detail)">
    <div class="mortgage_calc__fields">
        <label class="mortgage_calc__field">
            <span class="mortgage_calc__label">Стоимость дома</span>
            <span class="mortgage_calc__input">
                <input type="text" inputmode="numeric" value="<?= $calcFmt($calc['price']) ?>" :value="fmt(price)" @input="setPrice($event)">
                <span>₽</span>
            </span>
        </label>

        <div class="mortgage_calc__field">
            <label class="mortgage_calc__label" for="mortgage_calc_pv">
                <span>Первоначальный взнос — <span x-text="pvShare + '%'"><?= $calc['pv_percent'] ?>%</span></span>
            </label>
            <span class="mortgage_calc__input">
                <input id="mortgage_calc_pv" type="text" inputmode="numeric" value="<?= $calcFmt($calcPv) ?>" :value="fmt(pv)" @input="setPv($event)">
                <span>₽</span>
            </span>
            <div class="mortgage_calc__chips">
                <?php foreach ($calc['pv_buttons'] as $p): ?>
                <button type="button" :class="{ 'active': pvShare === <?= $p ?> }" @click="setPvPercent(<?= $p ?>)"><?= $p ?>%</button>
                <?php endforeach; ?>
            </div>
        </div>

        <label class="mortgage_calc__field">
            <span class="mortgage_calc__label">Ставка</span>
            <span class="mortgage_calc__input">
                <input type="number" inputmode="decimal" min="0" max="40" step="0.1" value="<?= $calc['rate'] ?>" x-model.number="rate">
                <span>%</span>
            </span>
        </label>

        <div class="mortgage_calc__field">
            <label class="mortgage_calc__label" for="mortgage_calc_years">Срок кредита</label>
            <span class="mortgage_calc__input">
                <input id="mortgage_calc_years" type="number" inputmode="numeric" min="1" max="30" step="1" value="<?= $calc['years'] ?>" x-model.number="years">
                <span>лет</span>
            </span>
            <div class="mortgage_calc__chips">
                <?php foreach ($calc['years_buttons'] as $y): ?>
                <button type="button" :class="{ 'active': years === <?= $y ?> }" @click="years = <?= $y ?>"><?= $y ?> лет</button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="mortgage_calc__result">
        <span class="mortgage_calc__result_label">Ежемесячный платёж</span>
        <span class="mortgage_calc__result_value" x-text="fmt(payment) + ' ₽/мес'"><?= $calcFmt($calcPayment) ?> ₽/мес</span>
        <div class="mortgage_calc__result_rows">
            <div>Стоимость дома <span x-text="fmt(price) + ' ₽'"><?= $calcFmt($calc['price']) ?> ₽</span></div>
            <div>Первоначальный взнос <span x-text="fmt(pv) + ' ₽ (' + pvShare + '%)'"><?= $calcFmt($calcPv) ?> ₽ (<?= $calc['pv_percent'] ?>%)</span></div>
            <div>Сумма кредита <span x-text="fmt(loan) + ' ₽'"><?= $calcFmt($calcLoan) ?> ₽</span></div>
            <div>Срок кредита <span x-text="termYears + ' ' + yearsWord"><?= $calc['years'] ?> <?= pluralRu($calc['years'], ['год', 'года', 'лет']) ?></span></div>
            <div>Ставка <span x-text="(rate || 0) + '%'"><?= $calc['rate'] ?>%</span></div>
        </div>
        <?= $calcFormCode ?>
        <button type="button" class="button mortgage_calc__button"><?= htmlspecialchars($calc['button_text']) ?></button>
        <p class="mortgage_calc__note">Расчёт предварительный. Точные условия зависят от банка и программы.</p>
    </div>
</div>

<?php if (empty($GLOBALS['mortgageCalcScript'])): $GLOBALS['mortgageCalcScript'] = true; ?>
<script>
function mortgageCalc(init) {
    return {
        price: init.price,
        pv: Math.round(init.price * init.pvPercent / 100),
        pvPercent: init.pvPercent, // выбран кнопкой — взнос пересчитывается при смене стоимости
        rate: init.rate,
        years: init.years,

        digits(e) {
            return parseInt(e.target.value.replace(/\D/g, ''), 10) || 0;
        },
        setPrice(e) {
            this.price = this.digits(e);
            if (this.pvPercent !== null) this.pv = Math.round(this.price * this.pvPercent / 100);
            e.target.value = this.fmt(this.price);
        },
        // цена пришла снаружи (событие project-total)
        setPriceValue(v) {
            this.price = Math.round(Number(v)) || 0;
            if (this.pvPercent !== null) this.pv = Math.round(this.price * this.pvPercent / 100);
        },
        setPv(e) {
            this.pv = this.digits(e);
            this.pvPercent = null;
            e.target.value = this.fmt(this.pv);
        },
        setPvPercent(p) {
            this.pvPercent = p;
            this.pv = Math.round(this.price * p / 100);
        },

        get pvShare() {
            return this.price ? Math.round(this.pv / this.price * 100) : 0;
        },
        get loan() {
            return Math.max(0, this.price - this.pv);
        },
        // та же формула, что annuityPayment() в includes/helpers.php
        get termYears() {
            return Math.min(Math.max(this.years || 0, 1), 30);
        },
        get yearsWord() {
            const n = this.termYears % 100, n1 = n % 10;
            if (n > 10 && n < 20) return 'лет';
            if (n1 === 1) return 'год';
            if (n1 > 1 && n1 < 5) return 'года';
            return 'лет';
        },
        get payment() {
            const n = this.termYears * 12;
            const i = (this.rate || 0) / 1200;
            if (!this.loan) return 0;
            if (i === 0) return Math.round(this.loan / n);
            return Math.round(this.loan * i / (1 - Math.pow(1 + i, -n)));
        },
        fmt(x) {
            return Math.round(x).toLocaleString('ru-RU');
        },
    };
}
</script>
<?php endif; ?>
