<?php
// Блок «Проверим ваш участок перед строительством» — статичный, в Directus не выносится.
// Необязательно перед include:
//   $blockClass   — доп. классы секции (например, полоса фона страницы)
//   $landButtonJs — JS для onclick кнопки «Проверить участок»; не задан — плашки с кнопкой нет
$landChecks = [
    ['header' => 'Исследование грунта', 'text' => 'Делаем пробное бурение и оцениваем несущую способность грунта. По результатам подбираем тип фундамента и решаем, где поставить дом, баню, беседку и септик — с учётом грунтовых вод и рельефа. Выносим границы участка, чтобы дом встал точно на своё место.'],
    ['header' => 'Документальная проверка', 'text' => 'Удостоверимся, что дом на участке без проблем зарегистрируют в Росреестре. Проверяем вид разрешённого использования, зоны подтопления, охранные и санитарные зоны, отступы от границ и другие ограничения.'],
    ['header' => 'Уведомление администрации', 'text' => 'Помогаем подать уведомление о планируемом строительстве. Администрация ещё раз проверит документы по участку и расположение дома и выдаст уведомление о соответствии.'],
];
?>
<section class="<?= $blockClass ?? '' ?>">
    <h2>Проверим ваш участок перед строительством</h2>
    <div class="ipoteka__land">
        <img class="ipoteka__land_image" src="/assets/images/uchastok.webp" alt="Участок под строительство каркасного дома" loading="lazy">
        <div class="ipoteka__tiles stroy__land_tiles">
            <?php foreach ($landChecks as $i => $tile): ?>
            <div class="ipoteka__tile">
                <h3><span class="stroy__land_num"><?= $i + 1 ?></span><?= htmlspecialchars($tile['header']) ?></h3>
                <p><?= htmlspecialchars($tile['text']) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php if (!empty($landButtonJs)): ?>
    <div class="ipoteka__inline_cta">
        <p>Пришлите кадастровый номер — проверим участок до начала строительства</p>
        <button type="button" class="button" onclick="<?= $landButtonJs ?>">Проверить участок</button>
    </div>
    <?php endif; ?>
</section>
<?php unset($blockClass, $landButtonJs);
