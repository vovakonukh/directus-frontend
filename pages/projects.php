<?php
$projects = fetchItems('projects', [
    'filter[status][_eq]' => 'published',
    'sort' => '-popular,square',
    'fields' => 'id,name,slug,main_image,main_plan,floors,square,length,width,bedrooms,wc,price_tk'
]);

$breadcrumbs = [
    ['title' => 'Каталог проектов']
];
include 'includes/breadcrumbs.php';
?>

<section class="page__wrap">
    <h1>Проекты каркасных домов</h1>

    <div class="projects__filters_section" x-data="projectsFilter()">
        <div class="projects__filters">
            <div class="projects__filter_group">
                <h3>Этажность</h3>
                <div class="projects__filter_inputs_wrap">
                    <template x-for="v in [1,2]">
                        <span>
                            <input type="checkbox" :id="'floor_'+v" :value="v" x-model="filters.floors">
                            <label class="projects__filter_label" :for="'floor_'+v" x-text="v+' '+(v==1?'этаж':'этажа')"></label>
                        </span>
                    </template>
                </div>
            </div>

            <div class="projects__filter_group">
                <h3>Площадь</h3>
                <div class="projects__filter_inputs_wrap">
                    <template x-for="r in areaRanges" :key="r.label">
                        <span>
                            <input type="checkbox" :id="'sq_'+r.min" :value="r.min+'-'+r.max" x-model="filters.area">
                            <label class="projects__filter_label" :for="'sq_'+r.min" x-text="r.label"></label>
                        </span>
                    </template>
                </div>
            </div>

            <div class="projects__filter_group">
                <h3>Спальни</h3>
                <div class="projects__filter_inputs_wrap">
                    <template x-for="v in [1,2,3,4]">
                        <span>
                            <input type="checkbox" :id="'bed_'+v" :value="v" x-model="filters.bedrooms">
                            <label class="projects__filter_label" :for="'bed_'+v" x-text="v+' '+(v==1?'спальня':'спальни')"></label>
                        </span>
                    </template>
                </div>
            </div>

            <div class="projects__filter_group">
                <h3>Санузлы</h3>
                <div class="projects__filter_inputs_wrap">
                    <template x-for="v in [1,2,3]">
                        <span>
                            <input type="checkbox" :id="'wc_'+v" :value="v" x-model="filters.wc">
                            <label class="projects__filter_label" :for="'wc_'+v" x-text="v+' '+(v==1?'санузел':'санузла')"></label>
                        </span>
                    </template>
                </div>
            </div>
        </div>

        <div class="projects__filters_end">
            <div class="projects__filters_cards_amount">
                Показано <span x-text="visibleCount"></span> из <span><?= count($projects) ?></span>
            </div>
            <div class="projects__filters_reset_button" @click="resetFilters()">Сбросить фильтры</div>
        </div>
    </div>

    <div class="projects__collection">
        <?php foreach ($projects as $item): ?>
            <div class="project_card"
                 data-floors="<?= $item['floors'] ?>"
                 data-area="<?= $item['square'] ?>"
                 data-bedrooms="<?= $item['bedrooms'] ?>"
                 data-bathrooms="<?= $item['wc'] ?>">
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
        <?php endforeach; ?>
    </div>

    <div id="no-results" class="hidden">Ничего не найдено</div>
</section>

<script>
function projectsFilter() {
    return {
        filters: { floors: [], area: [], bedrooms: [], wc: [] },
        visibleCount: <?= count($projects) ?>,
        areaRanges: [
            { min: 50, max: 70, label: 'от 50 до 70' },
            { min: 70, max: 100, label: 'от 70 до 100' },
            { min: 100, max: 130, label: 'от 100 до 130' },
            { min: 130, max: 170, label: 'от 130 до 170' },
        ],
        resetFilters() {
            this.filters = { floors: [], area: [], bedrooms: [], wc: [] };
        },
        init() {
            this.$watch('filters', () => this.applyFilters(), { deep: true });
        },
        applyFilters() {
            const cards = document.querySelectorAll('.project_card');
            let visible = 0;
            cards.forEach(card => {
                const f = card.dataset.floors;
                const a = parseFloat(card.dataset.area);
                const b = card.dataset.bedrooms;
                const w = card.dataset.bathrooms;

                const okFloors = !this.filters.floors.length || this.filters.floors.includes(f);
                const okBed = !this.filters.bedrooms.length || this.filters.bedrooms.includes(b);
                const okWc = !this.filters.wc.length || this.filters.wc.includes(w);
                const okArea = !this.filters.area.length || this.filters.area.some(range => {
                    const [min, max] = range.split('-').map(Number);
                    return a >= min && a <= max;
                });

                if (okFloors && okArea && okBed && okWc) {
                    card.style.display = '';
                    visible++;
                } else {
                    card.style.display = 'none';
                }
            });
            this.visibleCount = visible;
            document.getElementById('no-results').classList.toggle('hidden', visible > 0);
        }
    }
}
</script>