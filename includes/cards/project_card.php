<div class="project_card">
    <a href="/projects/<?= $item['slug'] ?>">
        <img src="<?= getAssetUrl($item['main_image']) ?>" />
    </a>
    <div class="project_card__description_wrap">
        <div class="project_card_description">
            <span class="project_card_superscript">
                <?= $item['floors'] == 1 ? 'Одноэтажный' : 'Двухэтажный' ?>
            </span>
            <h2><?= $item['name'] ?></h2>
            <span class="project_card__price">от <?= number_format($item['price_tk'], 0, '', ' ') ?> ₽</span>
            <div class="project_card__info_box_wrap">
                <div class="project_card__info_box">
                    <img src="/assets/icons/square.svg" />
                    <?= $item['square'] ?> м²
                </div>
                <?php if ($item['length'] && $item['width']): ?>
                <div class="project_card__info_box">
                    <img src="/assets/icons/dimensions.svg" />
                    <?= formatDimension($item['length']) ?>⨉<?= formatDimension($item['width']) ?>
                </div>
                <?php endif; ?>
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
        <?php if ($item['main_plan']): ?>
        <a class="project_card__plan_lightbox" data-fancybox="plan-<?= $item['id'] ?>" data-caption="<?= $item['name'] ?>" href="<?= getAssetUrl($item['main_plan']) ?>">
            <img src="<?= getAssetUrl($item['main_plan']) ?>" />
        </a>
        <?php endif; ?>
    </div>
</div>