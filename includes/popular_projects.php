<?php
$projects = fetchItems('projects', [
    'filter[status][_eq]' => 'published',
    'filter[show_on_main][_eq]' => true,
    'fields' => 'id,name,slug,main_image,main_plan,floors,square,length,width,bedrooms,wc,price_tk'
]);

if (!empty($projects)):
?>
<section>
    <h2>Популярные проекты</h2>
    <div class="projects__collection">
        <?php foreach ($projects as $project): ?>
        <div class="project_card">
            <a href="/projects/<?= $project['slug'] ?>">
                <img src="<?= getAssetUrl($project['main_image']) ?>" />
            </a>
            <div class="project_card__description_wrap">
                <div class="project_card_description">
                    <span class="project_card_superscript">
                        <?= $project['floors'] == 1 ? 'Одноэтажный' : 'Двухэтажный' ?>
                    </span>
                    <h2><?= $project['name'] ?></h2>
                    <span class="project_card__price">от <?= number_format($project['price_tk'], 0, '', ' ') ?> ₽</span>
                    <div class="project_card__info_box_wrap">
                        <div class="project_card__info_box">
                            <img src="/assets/icons/square.svg" />
                            <?= $project['square'] ?> м²
                        </div>
                        <?php if ($project['length'] && $project['width']): ?>
                        <div class="project_card__info_box">
                            <img src="/assets/icons/dimensions.svg" />
                            <?= formatDimension($project['length']) ?>⨉<?= formatDimension($project['width']) ?>
                        </div>
                        <?php endif; ?>
                        <div class="project_card__info_box">
                            <img src="/assets/icons/bedrooms.svg" />
                            <?= $project['bedrooms'] ?> спальни
                        </div>
                        <div class="project_card__info_box">
                            <img src="/assets/icons/wc.svg" />
                            <?= $project['wc'] ?> санузел
                        </div>
                    </div>
                </div>
                <a class="project_card__plan_lightbox" data-fancybox="plan-<?= $project['id'] ?>" data-caption="<?= $project['name'] ?>" href="<?= getAssetUrl($project['main_plan']) ?>">
                    <img src="<?= getAssetUrl($project['main_plan']) ?>" />
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>