<?php
// Получаем проект по slug
$projectData = fetchItems('projects', [
    'filter[slug][_eq]' => $slug,
    'filter[status][_eq]' => 'published',
    'fields' => '*',
    'limit' => 1
]);

if (empty($projectData)) {
    echo '<section class="page__wrap"><h1>Проект не найден</h1></section>';
    return;
}

$project = $projectData[0];

// Глобальные цены
$pricesData = fetchItems('project_prices_global', ['fields' => '*']);
$prices = $pricesData; // singleton возвращает объект, не массив

$priceSvai = ($prices['pile_price'] ?? 0) * ($project['piles_amount'] ?? 0);
$pricePlita = ($prices['plita_price'] ?? 0) * $project['square'];
$priceInzhenerka = ($prices['inzhenerka_kvm_price'] ?? 0) * $project['square'];

// Галереи из junction-таблицы
$allFiles = fetchItems('projects_files', [
    'filter[projects_id][_eq]' => $project['id'],
    'fields' => 'directus_files_id,type',
    'sort' => 'sort'
]);

$gallery = [];
$plan1 = [];
$plan2 = [];
foreach ($allFiles ?? [] as $f) {
    switch ($f['type']) {
        case 'visual': $gallery[] = $f['directus_files_id']; break;
        case 'plan_1': $plan1[] = $f['directus_files_id']; break;
        case 'plan_2': $plan2[] = $f['directus_files_id']; break;
    }
}

// Комплектация
$complectation = fetchItems('project_complectation', [
    'filter[status][_eq]' => 'published',
    'sort' => 'sort',
    'fields' => '*'
]);

// Дополнения
$additions = fetchItems('project_addition', [
    'filter[status][_eq]' => 'published',
    'sort' => 'sort',
    'fields' => '*'
]);

// Интерьеры
$interiors = fetchItems('project_interior', [
    'filter[status][_eq]' => 'published',
    'fields' => 'image'
]);

// Форма Bitrix24
$formData = fetchItems('forms', [
    'filter[id][_eq]' => 2,
    'fields' => 'bitrix_code'
]);
$formCode = $formData[0]['bitrix_code'] ?? '';

// Видео
$video = null;
if (!empty($project['video_link'])) {
    $videoData = fetchItems('video', [
        'filter[id][_eq]' => $project['video_link'],
        'fields' => 'video_code,title',
        'limit' => 1
    ]);
    $video = $videoData[0] ?? null;
}

// Построенные дома
$finishedHouses = fetchItems('finished', [
    'filter[original_project][_eq]' => $project['id'],
    'filter[status][_eq]' => 'published',
    'fields' => 'id,slug,length,width,floors'
]);

// Фото для построенных домов (из junction)
if (!empty($finishedHouses)) {
    foreach ($finishedHouses as &$house) {
        $houseFiles = fetchItems('finished_files', [
            'filter[finished_id][_eq]' => $house['id'],
            'fields' => 'directus_files_id',
            'limit' => 4
        ]);
        $house['images'] = array_column($houseFiles ?? [], 'directus_files_id');
    }
    unset($house);
}

// Похожие проекты
$similarProjects = [];
if (!empty($project['similar_projects'])) {
    $projectFull = fetchItems('projects/' . $project['id'], [
        'fields' => 'similar_projects.related_projects_id.id,similar_projects.related_projects_id.name,similar_projects.related_projects_id.slug,similar_projects.related_projects_id.main_image,similar_projects.related_projects_id.main_plan,similar_projects.related_projects_id.floors,similar_projects.related_projects_id.square,similar_projects.related_projects_id.length,similar_projects.related_projects_id.width,similar_projects.related_projects_id.bedrooms,similar_projects.related_projects_id.wc,similar_projects.related_projects_id.price_tk'
    ]);
    foreach ($projectFull['similar_projects'] ?? [] as $sp) {
        $similarProjects[] = $sp['related_projects_id'];
    }
}

// FAQ
$faq = fetchItems('faq', [
    'filter[status][_eq]' => 'published',
    'filter[show_on][_contains]' => 'project_item',
    'fields' => '*'
]);

// Хлебные крошки
$breadcrumbs = [
    ['title' => 'Каталог проектов', 'url' => '/projects'],
    ['title' => 'Каркасный дом ' . formatDimension($project['length']) . '×' . formatDimension($project['width']) . ' «' . $project['name'] . '»']
];
include 'includes/breadcrumbs.php';
?>



<section class="page__wrap">
    <h1>Каркасный дом <?= formatDimension($project['length']) ?>×<?= formatDimension($project['width']) ?> «<?= htmlspecialchars($project['name']) ?>»</h1>

    <div class="project__jumbo">

        <!-- Основной слайдер -->
        <div class="project__slider">
            <div class="f-carousel" id="project_carousel">
                <div class="f-carousel__viewport">
                    <div class="f-carousel__track">
                        <?php foreach ($gallery as $fileId): ?>
                        <div class="f-carousel__slide" data-fancybox="project_main" href="<?= getAssetUrl($fileId) ?>">
                            <img src="<?= getAssetUrl($fileId) ?>" />
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Карточка параметров -->
        <div class="project__info_box">
            <div class="project__info_params_wrap">
                <?php
                $params = [
                    ['icon' => 'square.svg', 'label' => 'Площадь', 'value' => $project['square'] . ' м²'],
                    ['icon' => 'dimensions.svg', 'label' => 'Габариты', 'value' => formatDimension($project['length']) . '⨉' . formatDimension($project['width'])],
                    ['icon' => 'bedrooms.svg', 'label' => 'Спален', 'value' => $project['bedrooms']],
                    ['icon' => 'wc.svg', 'label' => 'Санузлов', 'value' => $project['wc']],
                    ['icon' => 'bedrooms.svg', 'label' => 'Второй свет', 'value' => $project['second_light'] ? 'Да' : 'Нет'],
                    ['icon' => 'bedrooms.svg', 'label' => 'Высота потолков', 'value' => ($project['ceiling_height'] ?? '—') . ' м'],
                ];
                foreach ($params as $p): ?>
                <div class="project__info_params_item">
                    <img src="/assets/icons/<?= $p['icon'] ?>" />
                    <div class="project__info_params_item_text">
                        <span><?= $p['label'] ?></span>
                        <span><?= $p['value'] ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="project__info_links">
                <a href="#complectation">Комплектация</a>
                <a href="#planirovka">Планировка</a>
                <?php if (!empty($finishedHouses)): ?>
                    <a href="#finished">Реальные фото</a>
                <?php endif; ?>
                <?php if ($video): ?>
                    <a href="#video">Видео-обзор</a>
                <?php endif; ?>
            </div>

            <div class="column">
                <span class="project__info_box_price_label">Цена:</span>
                <span class="project__info_box_price">от <?= number_format($project['price_tk'], 0, '', ' ') ?> ₽</span>
            </div>

            <?= $formCode ?>
            <div class="project__info_box_button">Оставить заявку</div>
        </div>

        <!-- Миниатюры -->
        <div class="project__slider_control">
            <div class="f-carousel" id="project_thumbs">
                <div class="f-carousel__viewport">
                    <div class="f-carousel__track">
                        <?php foreach ($gallery as $fileId): ?>
                        <div class="f-carousel__slide">
                            <img src="<?= getAssetUrl($fileId) ?>" />
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

    <!-- Планировки -->
    <div class="project__plan_section" id="planirovka">
        <h2>Планировка</h2>
    <div class="project__plan_tabs" x-data="{ activeFloor: 0 }">

        <?php if ($project['floors'] > 1): ?>
        <div class="project__plan_tabs_nav">
            <button class="project__plan_tab_btn" :class="activeFloor === 0 && 'project__plan_tab_btn_active'" @click="activeFloor = 0">1 этаж</button>
            <button class="project__plan_tab_btn" :class="activeFloor === 1 && 'project__plan_tab_btn_active'" @click="activeFloor = 1">2 этаж</button>
        </div>
        <?php endif; ?>

        <div class="project__plan_tabs_content">

            <!-- 1 этаж -->
            <div class="project__plan_tabs_pane" :class="activeFloor === 0 && 'project__plan_tabs_pane_show'">
                <?php if (count($plan1) > 1): ?>
                <div class="project__plan1_variants_wrap">
                    <div class="f-carousel" id="plan1_thumbs">
                        <div class="f-carousel__viewport">
                            <div class="f-carousel__track">
                                <?php foreach ($plan1 as $i => $fileId): ?>
                                <div class="f-carousel__slide"><?= $i + 1 ?> вариант</div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <div class="project__slider">
                    <div class="f-carousel" id="plan1_carousel">
                        <div class="f-carousel__viewport">
                            <div class="f-carousel__track">
                                <?php foreach ($plan1 as $fileId): ?>
                                <div class="f-carousel__slide" data-fancybox="plan1" href="<?= getAssetUrl($fileId) ?>">
                                    <img src="<?= getAssetUrl($fileId) ?>" />
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2 этаж -->
            <?php if ($project['floors'] > 1): ?>
            <div class="project__plan_tabs_pane" :class="activeFloor === 1 && 'project__plan_tabs_pane_show'">
                <?php if (count($plan2) > 1): ?>
                <div class="project__plan2_variants_wrap">
                    <div class="f-carousel" id="plan2_thumbs">
                        <div class="f-carousel__viewport">
                            <div class="f-carousel__track">
                                <?php foreach ($plan2 as $i => $fileId): ?>
                                <div class="f-carousel__slide"><?= $i + 1 ?> вариант</div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <div class="project__slider">
                    <div class="f-carousel" id="plan2_carousel">
                        <div class="f-carousel__viewport">
                            <div class="f-carousel__track">
                                <?php foreach ($plan2 as $fileId): ?>
                                <div class="f-carousel__slide" data-fancybox="plan2" href="<?= getAssetUrl($fileId) ?>">
                                    <img src="<?= getAssetUrl($fileId) ?>" />
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>

</div></section>

<!-- Комплектация -->
<section id="complectation" x-data="complectationCalc()">
    <h2>Комплектация</h2>

    <!-- Сваи -->
    <div class="project__complectation_card">
        <div>
            <input type="checkbox" id="fund_svai" :value="<?= $priceSvai ?>" @change="recalc($el)">
            <label for="fund_svai">Фундамент: ЖБ сваи <span><?= number_format($priceSvai, 0, '', ' ') ?> ₽</span></label>
            <span class="project__complectation_fund_disclaimer">Окончательно определиться с типом фундамента можно только после пробного бурения и анализа вашего участка</span>
        </div>
        <div class="project__complectation_card_items">
            <?php foreach ($complectation as $item): ?>
                <?php if ($item['stage'] === 'svai'): ?>
                <div class="project__complectation_card_item">
                    <span><?= htmlspecialchars($item['label']) ?></span>
                    <span><?= htmlspecialchars($item['value']) ?></span>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Плита -->
    <div class="project__complectation_card">
        <div>
            <input type="checkbox" id="fund_plita" :value="<?= $pricePlita ?>" @change="recalc($el)">
            <label for="fund_plita">Фундамент: ЖБ плита <span><?= number_format($pricePlita, 0, '', ' ') ?> ₽</span></label>
            <span class="project__complectation_fund_disclaimer">Окончательно определиться с типом фундамента можно только после пробного бурения и анализа вашего участка</span>
        </div>
        <div class="project__complectation_card_items">
            <?php foreach ($complectation as $item): ?>
                <?php if ($item['stage'] === 'plita'): ?>
                <div class="project__complectation_card_item">
                    <span><?= htmlspecialchars($item['label']) ?></span>
                    <span><?= htmlspecialchars($item['value']) ?></span>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Теплый контур -->
    <div class="project__complectation_card">
        <div>
            <input type="checkbox" id="teplyi_kontur" checked :value="<?= $project['price_tk'] ?>" @change="recalc($el)">
            <label for="teplyi_kontur">Теплый контур <span><?= number_format($project['price_tk'], 0, '', ' ') ?> ₽</span></label>
        </div>
        <div class="project__complectation_card_items">
            <?php foreach ($complectation as $item): ?>
                <?php if ($item['stage'] === 'tk'): ?>
                <div class="project__complectation_card_item">
                    <span><?= htmlspecialchars($item['label']) ?></span>
                    <span><?= htmlspecialchars($item['value']) ?></span>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Инженерные сети -->
    <div class="project__complectation_card">
        <div>
            <input type="checkbox" id="inzhenerka" :value="<?= $priceInzhenerka ?>" @change="recalc($el)">
            <label for="inzhenerka">Инженерные сети от <span><?= number_format($priceInzhenerka, 0, '', ' ') ?> ₽</span></label>
        </div>
        <div class="project__complectation_card_items">
            <?php foreach ($complectation as $item): ?>
                <?php if ($item['stage'] === 'inzhenerka'): ?>
                <div class="project__complectation_card_item">
                    <span><?= htmlspecialchars($item['label']) ?></span>
                    <span><?= htmlspecialchars($item['value']) ?></span>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Отделка -->
    <div class="project__complectation_card">
        <div>
            <input type="checkbox" id="otdelka" :value="<?= $project['price_otdelka'] ?>" @change="recalc($el)">
            <label for="otdelka">Отделка от <span><?= number_format($project['price_otdelka'], 0, '', ' ') ?> ₽</span></label>
        </div>
        <div class="project__complectation_card_items">
            <?php foreach ($complectation as $item): ?>
                <?php if ($item['stage'] === 'otdelka'): ?>
                <div class="project__complectation_card_item">
                    <span><?= htmlspecialchars($item['label']) ?></span>
                    <span><?= htmlspecialchars($item['value']) ?></span>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Плавающая панель с ценой -->
    <div class="project__pane" :style="total > 0 ? 'display:flex; position:sticky; bottom:30px;' : 'display:none'">
        <div>
            <div class="project__pane_price_wrap">
                Общая стоимость: <span x-text="totalFormatted"></span> ₽
            </div>
            <div class="project__pane_ipoteka_wrap">Стоимость в ипотеку уточняйте у менеджера</div>
        </div>
        <?= $formCode ?>
        <div class="button big">Оставить заявку</div>
    </div>
</section>

<!-- Дополнения -->
<?php if (!empty($additions)): ?>
<section class="page__wrap">
    <h2>Возможные дополнения</h2>
    <?php foreach ($additions as $item): ?>
    <div class="project__extra_service_item">
        <span><?= htmlspecialchars($item['label']) ?></span>
        <span><?= htmlspecialchars($item['price']) ?></span>
    </div>
    <?php endforeach; ?>
</section>
<?php endif; ?>

<!-- Интерьеры -->
<?php if (!empty($interiors)): ?>
<section class="page__wrap">
    <h2>Вариант внутренней отделки дома</h2>
    <div class="project__inside_gallery">
        <div>
            <a data-fancybox="vnutr_otdelka" class="project__gallery_big_image" href="<?= getAssetUrl($interiors[0]['image']) ?>">
                <img src="<?= getAssetUrl($interiors[0]['image']) ?>" />
            </a>
        </div>
        <div>
            <?php foreach (array_slice($interiors, 1) as $item): ?>
            <a data-fancybox="vnutr_otdelka" class="project__gallery_image" href="<?= getAssetUrl($item['image']) ?>">
                <img src="<?= getAssetUrl($item['image']) ?>" />
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- С нами надежно -->
<section class="page__wrap">
    <h2>С нами надежно</h2>
    <div class="tile_wrap">
        <div class="tile_item">
            <img src="/assets/icons/warranty-contract.svg" />
            <div class="tile_item_text_wrap">
                <span>Гарантия 5 лет</span>
                <p>Мы даем пятилетнюю гарантию на свои дома. В случае проблем — приезжаем и переделываем за свой счет</p>
            </div>
        </div>
        <div class="tile_item">
            <img src="/assets/icons/project.svg" />
            <div class="tile_item_text_wrap">
                <span>Строим по проекту</span>
                <p>Для всех домов мы обязательно делаем проект с расчетом нагрузок и подробной инструкцией по сборке</p>
            </div>
        </div>
        <div class="tile_item">
            <img src="/assets/icons/house-wooden.webp" />
            <div class="tile_item_text_wrap">
                <span>Экскурсия на строящийся дом</span>
                <p>Запишитесь на экскурсию на строящийся дом — своими глазами посмотрите на качество материала и работ</p>
            </div>
        </div>
        <div class="tile_item">
            <img src="/assets/icons/communication.webp" />
            <div class="tile_item_text_wrap">
                <span>Фотоотчеты со стройки</span>
                <p>Вам не нужно приезжать на стройку — фотографии со стройки вы получаете регулярно в удобном для вас мессенджере</p>
            </div>
        </div>
    </div>
</section>

<!-- Видео -->
<?php if ($video): ?>
<section id="video">
    <h2>Видео-обзор</h2>
    <div class="finished__video_wrap">
        <?= $video['video_code'] ?>
    </div>
</section>
<?php endif; ?>

<!-- Построенные дома -->
<?php if (!empty($finishedHouses)): ?>
<section id="finished">
    <h2>Построено по этому проекту</h2>
    <div class="built_slides_wrap">
        <?php foreach ($finishedHouses as $house): ?>
        <div class="built_slides">
            <?php foreach ($house['images'] as $fileId): ?>
            <a class="built__image" data-fancybox="finished_<?= $house['id'] ?>" href="<?= getAssetUrl($fileId) ?>">
                <img src="<?= getAssetUrl($fileId) ?>" />
            </a>
            <?php endforeach; ?>
            <a class="built__link" href="/finished/<?= $house['slug'] ?>">Смотреть этот дом <br>→</a>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- Похожие проекты -->
<?php if (!empty($similarProjects)): ?>
<section class="page__wrap">
    <h2>Похожие проекты</h2>
    <div class="projects__collection">
        <?php foreach ($similarProjects as $item): ?>
            <?php include 'includes/cards/similar_project_card.php'; ?>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- FAQ -->
<?php if (!empty($faq)): ?>
<section class="margin-bottom-50">
    <h2>Частые вопросы</h2>
    <?php foreach ($faq as $f): ?>
        <?php include 'includes/cards/faq_card.php'; ?>
    <?php endforeach; ?>
</section>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const projectCarousel = new Carousel(document.getElementById('project_carousel'), { Dots: false });
    new Carousel(document.getElementById('project_thumbs'), {
        center: false, Dots: false, Navigation: false,
        Sync: { target: projectCarousel }
    });

    // Планировка 1 этаж
    const plan1El = document.getElementById('plan1_carousel');
    if (plan1El) {
        const plan1Carousel = new Carousel(plan1El, { Dots: false });
        const plan1Thumbs = document.getElementById('plan1_thumbs');
        if (plan1Thumbs) {
            new Carousel(plan1Thumbs, {
                center: false, Dots: false, Navigation: false, dragFree: true,
                Sync: { target: plan1Carousel }
            });
        }
    }

    // Планировка 2 этаж
    const plan2El = document.getElementById('plan2_carousel');
    if (plan2El) {
        const plan2Carousel = new Carousel(plan2El, { Dots: false });
        const plan2Thumbs = document.getElementById('plan2_thumbs');
        if (plan2Thumbs) {
            new Carousel(plan2Thumbs, {
                center: false, Dots: false, Navigation: false, dragFree: true,
                Sync: { target: plan2Carousel }
            });
        }
    }
});

function complectationCalc() {
    return {
        total: <?= $project['price_tk'] ?>,
        get totalFormatted() {
            return new Intl.NumberFormat('ru-RU').format(this.total);
        },
        recalc(el) {
            const inputs = document.querySelectorAll('#complectation input[type=checkbox]');
            let sum = 0;
            inputs.forEach(i => { if (i.checked) sum += Number(i.value); });
            this.total = sum;
        }
    }
}
</script>