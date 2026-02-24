<?php
$slides = fetchItems('homepage_slides', [
    'filter[status][_eq]' => 'published',
    'sort' => 'sort',
    'fields' => '*,form.bitrix_code'
]);

if (!empty($slides)):
?>
<style>
<?php foreach ($slides as $i => $slide): ?>
#jumbo_slide_<?= $i ?> {
    background: linear-gradient(rgba(0,0,0,<?= $slide['slide_opacity'] ?? 0.5 ?>), rgba(0,0,0,<?= $slide['slide_opacity'] ?? 0.5 ?>)), url(<?= getAssetUrl($slide['background_desktop']) ?>) center/cover no-repeat;
    @media (max-width: 767px) {
        background: linear-gradient(rgba(0,0,0,<?= $slide['slide_opacity'] ?? 0.5 ?>), rgba(0,0,0,<?= $slide['slide_opacity'] ?? 0.5 ?>)), url(<?= getAssetUrl($slide['background_mobile']) ?>) center/cover no-repeat;
    }
}
<?php endforeach; ?>
</style>

<section class="jumbo_section">
    <div class="f-carousel" id="jumbo_slider">
        <div class="f-carousel__viewport">
            <div class="f-carousel__track">
                <?php foreach ($slides as $i => $slide): ?>
                <div class="f-carousel__slide jumbo_slide" id="jumbo_slide_<?= $i ?>">
                    <div class="jumbo_content_wrap">
                        <p class="jumbo_header"><?= $slide['header'] ?></p>
                        <p class="jumbo_description"><?= $slide['description'] ?></p>
                        <?php if (!empty($slide['form'])): ?>
                            <?= $slide['form']['bitrix_code'] ?>
                            <a class="jumbo_button"><?= $slide['button_text'] ?></a>
                        <?php else: ?>
                            <a class="jumbo_button" href="<?= $slide['button_link'] ?>"><?= $slide['button_text'] ?></a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('jumbo_slider');
    if (!el) return;
    new Carousel(el, {
        infinite: true,
        Dots: false,
        Autoplay: { timeout: 6000, pauseOnHover: true }
    });
});
</script>
<?php endif; ?>