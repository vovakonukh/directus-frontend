<?php
// Блок «Почему каркасный дом» — статичный, в Directus не выносится.
// Необязательно перед include: $blockClass — доп. классы секции (например, полоса фона страницы)
$whyFrame = [
    ['icon' => 'fast.webp', 'header' => 'Быстро', 'text' => 'Коробку под ключ собираем за 1–3 месяца: на наших сданных объектах — от 35 дней. Без «мокрых» процессов и перерывов на усадку.'],
    ['icon' => 'warm.webp', 'header' => 'Тепло и экономно', 'text' => '150 мм каменной ваты в стенах и 200 мм в перекрытиях. Такой дом быстро прогревается и дёшев в отоплении.'],
    ['icon' => 'usadka.webp', 'header' => 'Без усадки', 'text' => 'Каркас из доски камерной сушки не «ведёт», отделку можно делать сразу после монтажа.'],
    ['icon' => 'light.webp', 'header' => 'Лёгкий фундамент', 'text' => 'Дом весит в разы меньше каменного — подходит свайный фундамент, это экономия на нулевом цикле.'],
    ['icon' => 'whole_year.webp', 'header' => 'Строим круглый год', 'text' => 'Каркасная технология не боится мороза: заезжаем на участок в любой сезон.'],
    ['icon' => 'for_mortgage.webp', 'header' => 'Подходит для ипотеки', 'text' => 'Строим по семейной ипотеке с эскроу-счётом — банк перечисляет деньги после сдачи дома.'],
];
?>
<section class="<?= $blockClass ?? '' ?>">
    <h2>Почему каркасный дом</h2>
    <div class="ipoteka__tiles">
        <?php foreach ($whyFrame as $tile): ?>
        <div class="ipoteka__tile">
            <img class="stroy__tile_icon" src="/assets/icons/<?= $tile['icon'] ?>" alt="" loading="lazy">
            <h3><?= htmlspecialchars($tile['header']) ?></h3>
            <p><?= htmlspecialchars($tile['text']) ?></p>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php unset($blockClass);
