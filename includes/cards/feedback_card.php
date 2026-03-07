<div class="feedback__card">
    <span><?= htmlspecialchars($item['client_name']) ?></span>
    <div class="feedback__stars_wrap">
        <?php for ($i = 0; $i < 5; $i++): ?>
            <img src="/assets/icons/rating-star-full-yellow.svg" />
        <?php endfor; ?>
        <span><?= date('d.m.Y', strtotime($item['date'])) ?></span>
    </div>
    <p><?= nl2br(htmlspecialchars($item['content'])) ?></p>
    <?php if (!empty($item['images'])): ?>
        <div class="feedback__images_collection">
            <?php foreach ($item['images'] as $img): ?>
                <a href="<?= getAssetUrl($img['directus_files_id']) ?>" data-fancybox="feedback_<?= $item['id'] ?>">
                    <img class="feedback__image" src="<?= getAssetUrl($img['directus_files_id']) ?>" />
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <a class="feedback__source" href="<?= htmlspecialchars($item['link']) ?>" target="_blank">
        <span>Отзыв оставлен на Яндекс Картах</span>
        <img src="/assets/icons/external_link.svg" />
    </a>
</div>