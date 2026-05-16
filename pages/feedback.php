<?php
$feedbacks = fetchItems('feedback', [
    'fields' => '*,images.directus_files_id.*',
    'filter[status][_eq]' => 'published',
    'sort' => '-date',
]);
?>

<section class="page__wrap">
    <h1>Отзывы</h1>
    <div class="feedback__page_inner">
        <div class="feedback_gallery">
            <?php foreach ($feedbacks as $item): ?>
                <div class="feedback__card">
                    <span><?= htmlspecialchars($item['client_name']) ?></span>
                    <div class="feedback__stars_wrap">
                        <img src="/assets/icons/rating-star-full-yellow.svg" />
                        <img src="/assets/icons/rating-star-full-yellow.svg" />
                        <img src="/assets/icons/rating-star-full-yellow.svg" />
                        <img src="/assets/icons/rating-star-full-yellow.svg" />
                        <img src="/assets/icons/rating-star-full-yellow.svg" />
                        <?php if (!empty($item['date'])): ?>
                            <span><?= date('d.m.Y', strtotime($item['date'])) ?></span>
                        <?php endif; ?>
                    </div>
                    <div><?= $item['content'] ?></div>

                    <?php if (!empty($item['images'])): ?>
                        <div class="feedback__images_collection">
                            <?php foreach ($item['images'] as $img): ?>
                                <?php $file = $img['directus_files_id']; ?>
                                <a href="<?= getAssetUrl($file['id']) ?>" data-fancybox="feedback-<?= $item['id'] ?>">
                                    <img src="<?= getAssetUrl($file['id']) ?>?width=300&height=300&fit=cover" alt="" />
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($item['link'])): ?>
                        <a class="feedback__source" href="<?= htmlspecialchars($item['link']) ?>" target="_blank" rel="noopener">
                            <span>Отзыв оставлен на Яндекс Картах</span>
                            <img src="/assets/icons/external_link.svg" />
                        </a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="feedback_rating_sticky"></div>
    </div>
</section>