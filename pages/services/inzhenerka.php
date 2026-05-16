<?php
$inzhenerka = fetchItems('inzhenerka', [
    'fields' => 'id,hero_image,hero_header,hero_description,hero_button_text,hero_link,hero_form,tiles.tiles_id.id,tiles.tiles_id.icon,tiles.tiles_id.header,tiles.tiles_id.text,tiles.type'
]);

$types = fetchItems('inzhenerka_types', [
    'fields' => 'id,name,caption,description,bullets,form,button_text,gallery.directus_files_id,sort',
    'filter[status][_eq]' => 'published',
    'sort' => 'sort'
]);

// Разделяем тайлы по типу
$advantages = [];
$whywe = [];
if (!empty($inzhenerka['tiles'])) {
    foreach ($inzhenerka['tiles'] as $junc) {
        $tile = $junc['tiles_id'];
        $type = $junc['type'] ?? '';
        if ($type === 'advantages') {
            $advantages[] = $tile;
        } elseif ($type === 'whywe') {
            $whywe[] = $tile;
        }
    }
}

// Форма hero
$heroForm = null;
if (!empty($inzhenerka['hero_form'])) {
    $heroForm = fetchItems('forms/' . $inzhenerka['hero_form']);
}
?>

<!-- Hero -->
<section class="jumbo_section">
    <div class="jumbo_slide static dark_text" style="background: linear-gradient(rgba(255,255,255,.3), rgba(255,255,255,.3)), url('<?= getAssetUrl($inzhenerka['hero_image']) ?>') center/cover no-repeat;">
        <div class="jumbo_content_wrap">
            <p class="jumbo_header"><?= htmlspecialchars($inzhenerka['hero_header'] ?? '') ?></p>
            <p class="jumbo_description"><?= htmlspecialchars($inzhenerka['hero_description'] ?? '') ?></p>
            <?= $heroForm['bitrix_code'] ?? '' ?>
            <a class="jumbo_button">
                <span><?= htmlspecialchars($inzhenerka['hero_button_text'] ?? '') ?></span>
            </a>
            
        </div>
    </div>
</section>

<!-- Преимущества -->
<?php if (!empty($advantages)): ?>
<section class="main_advantages_section">
    <div class="main_advantages_inner">
        <?php foreach ($advantages as $tile): ?>
        <div class="main_advantage_card">
            <?php if (!empty($tile['icon'])): ?>
                <img src="<?= getAssetUrl($tile['icon']) ?>" alt="">
            <?php endif; ?>
            <span><?= htmlspecialchars($tile['header'] ?? '') ?></span>
            <span><?= htmlspecialchars($tile['text'] ?? '') ?></span>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- Типы услуг -->
<?php if (!empty($types)): ?>
    <?php foreach ($types as $type): ?>
    <section class="engineering__service_section">
        <div class="engineering__service_columns">
            <div>
                <h2><?= htmlspecialchars($type['name'] ?? '') ?></h2>
                <?php if (!empty($type['bullets'])): ?>
                <div class="engineering__tags">
                    <?php foreach ($type['bullets'] as $bullet): ?>
                        <div><?= htmlspecialchars($bullet['text'] ?? '') ?></div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                <?php if ($heroForm): ?>
                    <?= $heroForm['bitrix_code'] ?? '' ?>
                    <div class="button"><?= htmlspecialchars($type['button_text'] ?? 'Оставить заявку') ?></div>
                <?php endif; ?>
            </div>
            <div class="engineering__service_text">
                <?php if (!empty($type['caption'])): ?>
                    <p><?= htmlspecialchars($type['caption']) ?></p>
                <?php endif; ?>
                <?php if (!empty($type['description'])): ?>
                    <p><?= htmlspecialchars($type['description']) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($type['gallery'])): ?>
        <div class="engineering__service_gallery">
            <?php foreach ($type['gallery'] as $img): 
                $fileId = $img['directus_files_id'];
                $url = getAssetUrl($fileId);
            ?>
                <a href="<?= $url ?>" data-fancybox="gallery_<?= $type['id'] ?>">
                    <img src="<?= $url ?>">
                </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </section>

    <?php endforeach; ?>
<?php endif; ?>

<!-- Почему мы -->
<?php if (!empty($whywe)): ?>
<section>
    <h2>Почему стоит выбрать монтаж инженерных сетей у нас?</h2>
    <div class="tile_wrap">
        <?php foreach ($whywe as $tile): ?>
        <div class="tile_big_item">
            <?php if (!empty($tile['icon'])): ?>
                <img src="<?= getAssetUrl($tile['icon']) ?>">
            <?php endif; ?>
            <p><?= htmlspecialchars($tile['header'] ?? '') ?></p>
            <?= htmlspecialchars($tile['text'] ?? '') ?>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

