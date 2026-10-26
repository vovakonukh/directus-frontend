<?php
// Блок «Вы защищены по договору на 100%» — статичный, в Directus не выносится.
// Необязательно перед include:
//   $blockClass       — доп. классы секции (например, полоса фона страницы)
//   $contractButtonJs — JS для onclick кнопки «Получить расчёт»; не задан — кнопки нет
$contractItems = [
    ['icon' => 'service__money.webp', 'text' => 'Стоимость работ'],
    ['icon' => 'service__payments.webp', 'text' => 'Порядок оплаты'],
    ['icon' => 'service__period.webp', 'text' => 'Сроки работы'],
    ['icon' => 'service__warranty.webp', 'text' => 'Гарантия до 10 лет'],
    ['icon' => 'service__smeta.webp', 'text' => 'Спецификация используемых материалов'],
];
?>
<section class="contract_block <?= $blockClass ?? '' ?>">
    <div class="contract_block__inner">
        <div class="contract_block__text">
            <h2>Вы защищены по договору на 100%</h2>
            <p class="contract_block__lead">Перед началом работ заключаем договор, поэтому вы защищены законодательством РФ. Персональный руководитель проекта на связи весь срок строительства.</p>
            <?php if (!empty($contractButtonJs)): ?>
            <button type="button" class="button" onclick="<?= $contractButtonJs ?>">Получить расчёт</button>
            <?php endif; ?>
        </div>
        <div class="contract_block__doc">
            <h3>В договоре зафиксированы:</h3>
            <ul class="contract_block__list">
                <?php foreach ($contractItems as $c): ?>
                <li>
                    <img src="/assets/icons/<?= $c['icon'] ?>" alt="" loading="lazy">
                    <span><?= htmlspecialchars($c['text']) ?></span>
                </li>
                <?php endforeach; ?>
            </ul>
            <div class="contract_block__sign" aria-hidden="true">
                <span>Заказчик</span>
                <span>Подрядчик</span>
            </div>
        </div>
    </div>
</section>
<?php unset($blockClass, $contractButtonJs);
