<?php $galleryId = 'gallery_' . htmlspecialchars($house['slug']); ?>
<div class="article_block">
    <div class="f-carousel article_carousel">
        <div class="f-carousel__viewport">
            <div class="f-carousel__track">
                <?php foreach ($items as $fileId): ?>
                    <div class="f-carousel__slide" data-fancybox="<?= $galleryId ?>" href="<?= getAssetUrl($fileId) ?>">
                        <img src="<?= getAssetUrl($fileId) ?>?width=1200" alt="<?= htmlspecialchars(finishedTitle($house)) ?>" loading="lazy">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <a class="article_block__more" href="/finished/<?= htmlspecialchars($house['slug']) ?>">Подробнее: <?= htmlspecialchars(mb_strtolower(mb_substr(finishedTitle($house), 0, 1)) . mb_substr(finishedTitle($house), 1)) ?></a>
</div>
<?php if (empty($GLOBALS['articleCarouselScript'])): $GLOBALS['articleCarouselScript'] = true; ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.article_carousel').forEach(el => new Carousel(el, { Dots: false }));
});
</script>
<?php endif; ?>
