<div class="project_card finished">
    <a href="/pages/finished_item.php?slug=<?= $item['slug'] ?>">
        <img src="<?= getAssetUrl($item['main_image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
    </a>
    <div class="project_card__description_wrap">
        <div class="project_card_description">
            <span class="project_card_superscript">
                <?= $item['floors'] == 1 ? 'Одноэтажный' : 'Двухэтажный' ?>
            </span>
            <h2><?= $item['floors'] == 1 ? 'Одноэтажный дом' : 'Двухэтажный дом' ?> <?= $item['length'] ?> на <?= $item['width'] ?> м</h2>
            <div class="project_card__info_box_wrap">
                <div class="project_card__info_box">
                    <img src="/assets/icons/square.svg" />
                    <?= $item['square'] ?> м²
                </div>
                <div class="project_card__info_box">
                    <img src="/assets/icons/dimensions.svg" />
                    <?= $item['length'] ?>⨉<?= $item['width'] ?>
                </div>
                <div class="project_card__info_box">
                    <img src="/assets/icons/bedrooms.svg" />
                    <?= $item['bedrooms'] ?> спальни
                </div>
                <div class="project_card__info_box">
                    <img src="/assets/icons/wc.svg" />
                    <?= $item['wc'] ?> санузел
                </div>
            </div>
        </div>
        <?php if ($item['plan_1']): ?>
            <a class="project_card__plan_lightbox" data-fancybox="plan_<?= $item['id'] ?>" href="<?= getAssetUrl($item['plan_1']) ?>">
                <img src="<?= getAssetUrl($item['plan_1']) ?>" />
            </a>
        <?php endif; ?>
    </div>
    <div class="finished_card_description">
        <ul>
            <li>Местоположение: <?= htmlspecialchars($item['location']) ?></li>
            <li>Дата сдачи: <?= htmlspecialchars($item['finish_date']) ?></li>
            <li>Срок строительства: <?= htmlspecialchars($item['construction_period']) ?></li>
        </ul>
        <a href="/pages/finished_item.php?slug=<?= $item['slug'] ?>">Посмотреть дом</a>
    </div>
</div>