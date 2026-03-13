<?php
// Получаем данные построенного дома
$finished = fetchItems('finished', [
    'filter[slug][_eq]' => $slug,
    'filter[status][_eq]' => 'published',
    'fields' => '*,gallery.directus_files_id',
    'limit' => 1
]);

if (empty($finished)) {
    echo '<section class="page__wrap"><h1>Дом не найден</h1></section>';
    return;
}

$item = $finished[0];

// Формируем заголовок
$floorText = $item['floors'] == 1 ? 'Одноэтажный дом' : 'Двухэтажный дом';
$pageTitle = $floorText . ' ' . $item['length'] . ' на ' . $item['width'] . ' м';

// Хлебные крошки
$breadcrumbs = [
    ['title' => 'Построенные дома', 'url' => '/finished'],
    ['title' => $pageTitle]
];
include 'includes/breadcrumbs.php';
?>

<section>
    <h1><?= htmlspecialchars($pageTitle) ?></h1>
    <div class="finished__main_wrap">
        <div class="finished__main_content">

            <!-- Галерея -->
            <?php if (!empty($item['gallery'])): ?>
            <div class="finished__gallery">
                <?php foreach ($item['gallery'] as $img): ?>
                    <a href="<?= getAssetUrl($img['directus_files_id']) ?>" data-fancybox="finished">
                        <img src="<?= getAssetUrl($img['directus_files_id']) ?>" alt="<?= htmlspecialchars($pageTitle) ?>" />
                    </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Видео -->
            <?php if (!empty($item['video_link'])):
                $video = fetchItems('video', [
                    'filter[id][_eq]' => $item['video_link'],
                    'fields' => 'video_code',
                    'limit' => 1
                ]);
                if (!empty($video)):
            ?>
                <h2>Видео-обзор</h2>
                <div class="finished__video_wrap">
                    <?= $video[0]['video_code'] ?>
                </div>
            <?php endif; endif; ?>

            <!-- Планировка -->
            <h2>Планировка</h2>
            <div class="finished__plan_wrap" <?php if (empty($item['plan_2'])): ?>style="justify-content: center;"<?php endif; ?>>
                <?php if (!empty($item['plan_1'])): ?>
                <a href="<?= getAssetUrl($item['plan_1']) ?>" data-fancybox="plan" class="finished__plan" <?php if (empty($item['plan_2'])): ?>style="width: 100%;"<?php endif; ?>>
                    <img src="<?= getAssetUrl($item['plan_1']) ?>" alt="Планировка 1 этаж" />
                </a>
                <?php endif; ?>
                <?php if (!empty($item['plan_2'])): ?>
                <a href="<?= getAssetUrl($item['plan_2']) ?>" data-fancybox="plan" class="finished__plan">
                    <img src="<?= getAssetUrl($item['plan_2']) ?>" alt="Планировка 2 этаж" />
                </a>
                <?php endif; ?>
            </div>

            <!-- Карточка оригинального проекта -->
            <?php if (!empty($item['original_project'])):
                $project = fetchItems('projects', [
                    'filter[id][_eq]' => $item['original_project'],
                    'fields' => 'id,name,slug,main_plan,price_tk,gallery.directus_files_id',
                    'limit' => 1
                ]);
                if (!empty($project)):
                    $project = $project[0];
            ?>
                <h2>Построен по проекту <a class="finished__original_project_header_link" href="/projects/<?= $project['slug'] ?>"><?= htmlspecialchars($project['name']) ?></a></h2>
                <div class="finished__original_project_card">
                    <div class="finished__original_project_info_wrap">
                        <?php if (!empty($project['main_plan'])): ?>
                        <a href="<?= getAssetUrl($project['main_plan']) ?>" data-fancybox="original_project">
                            <img src="<?= getAssetUrl($project['main_plan']) ?>" alt="<?= htmlspecialchars($project['name']) ?>" />
                        </a>
                        <?php endif; ?>
                        <?php if (!empty($project['price_tk'])): ?>
                        <div class="finished__original_project_prices">
                            <div class="finished__original_project_price">
                                <span>от <?= number_format($project['price_tk'], 0, '', ' ') ?> ₽</span>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($project['gallery'])): ?>
                    <div class="finished__original_project_gallery">
                        <?php foreach (array_slice($project['gallery'], 0, 4) as $img): ?>
                            <a href="<?= getAssetUrl($img['directus_files_id']) ?>" data-fancybox="original_project">
                                <img src="<?= getAssetUrl($img['directus_files_id']) ?>" alt="" />
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            <?php endif; endif; ?>

            <!-- Отзыв -->
            <?php if (!empty($item['feedback'])):
                $feedback = fetchItems('feedback', [
                    'filter[id][_eq]' => $item['feedback'],
                    'fields' => 'id,client_name,date,content,link,images.directus_files_id',
                    'limit' => 1
                ]);
                if (!empty($feedback)):
            ?>
                <h2>Отзыв заказчика</h2>
                <?php
                    $saved_item = $item;
                    $item = $feedback[0];
                    include 'includes/cards/feedback_card.php';
                    $item = $saved_item;
                ?>
            <?php endif; endif; ?>

        </div>

        <!-- Боковая панель с информацией -->
        <div class="finished__info_box">
            <div class="finished__info_box_project_ref">
                <?php if (!empty($item['original_project'])):
                    $origProject = fetchItems('projects', [
                        'filter[id][_eq]' => $item['original_project'],
                        'fields' => 'name,slug',
                        'limit' => 1
                    ]);
                    if (!empty($origProject)):
                ?>
                    <span>По проекту&nbsp;</span>
                    <a class="finished__info_box_project_link" href="/projects/<?= $origProject[0]['slug'] ?>">
                        <?= htmlspecialchars($origProject[0]['name']) ?>
                    </a>
                <?php else: ?>
                    <span>Построен по индивидуальному проекту</span>
                <?php endif; else: ?>
                    <span>Построен по индивидуальному проекту</span>
                <?php endif; ?>
            </div>

            <div class="project__info_params_wrap">
                <div class="project__info_params_item">
                    <img src="/assets/icons/square.svg" />
                    <div class="project__info_params_item_text">
                        <span>Площадь</span>
                        <span><?= $item['square'] ?> м²</span>
                    </div>
                </div>
                <div class="project__info_params_item">
                    <img src="/assets/icons/dimensions.svg" />
                    <div class="project__info_params_item_text">
                        <span>Габариты</span>
                        <span><?= formatDimension($item['length']) ?>⨉<?= formatDimension($item['width']) ?> м</span>
                    </div>
                </div>
                <div class="project__info_params_item">
                    <img src="/assets/icons/bedrooms.svg" />
                    <div class="project__info_params_item_text">
                        <span>Спален</span>
                        <span><?= $item['bedrooms'] ?></span>
                    </div>
                </div>
                <div class="project__info_params_item">
                    <img src="/assets/icons/wc.svg" />
                    <div class="project__info_params_item_text">
                        <span>Санузлов</span>
                        <span><?= $item['wc'] ?></span>
                    </div>
                </div>
                <div class="project__info_params_item">
                    <img src="/assets/icons/bedrooms.svg" />
                    <div class="project__info_params_item_text">
                        <span>Второй свет</span>
                        <span><?= $item['second_light'] ? 'Есть' : 'Нет' ?></span>
                    </div>
                </div>
                <div class="project__info_params_item">
                    <img src="/assets/icons/bedrooms.svg" />
                    <div class="project__info_params_item_text">
                        <span>Срок строительства</span>
                        <span><?= htmlspecialchars($item['construction_period']) ?></span>
                    </div>
                </div>
                <div class="project__info_params_item">
                    <img src="/assets/icons/bedrooms.svg" />
                    <div class="project__info_params_item_text">
                        <span>Местоположение</span>
                        <span><?= htmlspecialchars($item['location']) ?></span>
                    </div>
                </div>
            </div>

            <div class="finished_info_box_cta_wrap">
                <span>Задать вопрос по проекту</span>
                <?php if (!empty($contacts)): ?>
                    <?php foreach ($contacts as $contact): ?>
                        <?php if ($contact['type'] === 'phone'): ?>
                            <a class="finished__info_box_phone" href="tel:<?= preg_replace('/[^+0-9]/', '', $contact['value']) ?>"><?= htmlspecialchars($contact['value']) ?></a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
                <div class="finished__info_box_msg_wrap">
                    <?php if (!empty($contacts)): ?>
                        <?php foreach ($contacts as $contact): ?>
                            <?php if ($contact['type'] === 'telegram'): ?>
                                <span>в </span><a href="<?= htmlspecialchars($contact['value']) ?>">Telegram</a>
                            <?php endif; ?>
                            <?php if ($contact['type'] === 'whatsapp'): ?>
                                <span> или в </span><a href="<?= htmlspecialchars($contact['value']) ?>">WhatsApp</a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <?php
                    $form = fetchItems('forms', [
                        'fields' => 'bitrix_code',
                        'filter[id][_eq]' => 1
                    ]);
                    $form_code = $form[0]['bitrix_code'] ?? '';
                ?>
                <?= $form_code ?>
                <div class="project__info_box_button">
                    Заказать звонок
                </div>
            </div>
        </div>

    </div>
</section>

<section class="page__wrap project_callout">
    <span>Понравился проект?</span>
    <p>Построим вам точно такой же или бесплатно внесем изменения индивидуально под вас.</p>
    <p>Оставьте заявку, чтобы назначить встречу с проектировщиком.</p>
    <?= $form_code ?>
    <a class="button big">Оставить заявку</a>
</section>
