<?php
$fundament = fetchItems('fundament', [
    'fields' => '*,tiles.tiles_id.icon,tiles.tiles_id.header,tiles.tiles_id.text,tiles.tiles_id.sort',
    'deep[tiles][_sort]' => 'tiles_id.sort'
]);

// singleton
if (is_array($fundament) && isset($fundament[0])) {
    $fundament = $fundament[0];
}

$tiles = array_map(function($t) {
    return $t['tiles_id'];
}, $fundament['tiles'] ?? []);
?>

<style>
    .jumbo_slide.static {
        background:
        linear-gradient(rgba(255, 255, 255, 0.3), rgba(255, 255, 255, 0.3)),
        url('<?= getAssetUrl($fundament['hero_image']) ?>') center/cover no-repeat;
    }
</style>

<section class="jumbo_section">
    <div class="jumbo_slide static dark_text">
        <div class="jumbo_content_wrap">
            <p class="jumbo_header"><?= htmlspecialchars($fundament['hero_header'] ?? '') ?></p>
            <p class="jumbo_description"><?= nl2br(htmlspecialchars($fundament['hero_description'] ?? '')) ?></p>
            <a href="<?= htmlspecialchars($fundament['hero_link'] ?? '#') ?>" class="button double">
                <span>Рассчитать стоимость</span>
                <span>под свой проект</span>
            </a>
        </div>
    </div>
</section>

<section class="main_advantages_section">
    <div class="main_advantages_inner">
        <?php foreach ($tiles as $tile): ?>
        <div class="main_advantage_card">
            <?php if (!empty($tile['icon'])): ?>
                <img src="<?= getAssetUrl($tile['icon']) ?>" alt="">
            <?php endif; ?>
            <span><?= htmlspecialchars($tile['header'] ?? '') ?></span>
            <span><?= htmlspecialchars($tile['text'] ?? '') ?></span>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<section>
    <h2>Делаем инженерно-геологическое исследование участка, чтобы идеально подобрать фундамент под ваш дом</h2>
    <div class="fund__research">
        <div>
            Перед строительством фундамента нужно:
            <ul>
                <li>Рассчитать предполагаемый вес здания.</li>
                <li>Узнать состав грунта на строительном участке и уровень залегания подземных вод.</li>
                <li>Выяснить несущую способность грунта</li>
                <li>Произвести расчёт фундамента</li>
            </ul>
            <script data-b24-form="click/6/t86koa" data-skip-moving="true">
                (function(w,d,u){var s=d.createElement('script');s.async=true;s.src=u+'?'+(Date.now()/180000|0);var h=d.getElementsByTagName('script')[0];h.parentNode.insertBefore(s,h);})(window,document,'https://cdn-ru.bitrix24.ru/b14099574/crm/form/loader_6.js');
            </script>
            <div class="button double">
                <span>Заказать исследование участка</span>
                <span>и подобрать фундамент</span>
            </div>
        </div>
        <img src="https://cdn.prod.website-files.com/60d7939987b38e9279246a11/621f40a40f0371fa4ae2f65f_resource_img_16194820486800.webp" alt="">
    </div>
</section>

<section>
    <h2>Подберем и спроектируем идеальный вариант фундамента для любой постройки</h2>
    <div class="fund__anybuilding">
        <div>
            <ul>
                <li>Дом</li>
                <li>Баня</li>
                <li>Дача</li>
                <li>Гараж</li>
                <li>Коттедж</li>
                <li>Забор</li>
            </ul>
        </div>
        <img src="https://cdn.prod.website-files.com/60d7939987b38e9279246a11/623085d6bb3d754089725be6_MG_9229.jpg" alt="">
    </div>
</section>

<section>
    <h2>Поможем привязать дом к участку</h2>
    <div class="fund__privyazka">
        <div>
            <ul>
                <li>определим пятно застройки</li>
                <li>расположение въезда на участок</li>
                <li>место установки очистной системы</li>
                <li>запланируем места для других построек</li>
            </ul>
            <script data-b24-form="click/6/t86koa" data-skip-moving="true">
                (function(w,d,u){var s=d.createElement('script');s.async=true;s.src=u+'?'+(Date.now()/180000|0);var h=d.getElementsByTagName('script')[0];h.parentNode.insertBefore(s,h);})(window,document,'https://cdn-ru.bitrix24.ru/b14099574/crm/form/loader_6.js');
            </script>
            <div class="button double">
                <span>Привязать дом</span>
                <span>к участку</span>
            </div>
        </div>
        <img src="https://cdn.prod.website-files.com/60d7939987b38e9279246a11/621f8fa625fbf21d5c7957d0_00000073-221_%D0%91%D0%B5%D0%BB%D0%BE%D0%B2%D0%B0_%D0%95%D0%BB%D0%B8%D0%B7%D0%B0%D0%B2%D0%B5%D1%82%D0%B8%D0%BD%D1%81%D0%BA%D0%BE%D0%B5.pdf%202022-03-02%2018-38-49-p-1600.webp" alt="">
    </div>
</section>

<section>
    <h2>Наши работы</h2>
    <div class="fund__cases_gallery">
        <div class="fund__case">
            <img src="https://cdn.prod.website-files.com/60d7939987b38e9279246a11/621f40a32b01b7e1deff0b66_orig-p-1600.webp" alt="">
            КП Березовка, Ленинградская обл.
            <div>
                <div class="fund__case_param_item">
                    <img src="/assets/icons/service_budget.svg" alt="">
                    <div>
                        <span>Бюджет</span>
                        1 100 000 ₽
                    </div>
                </div>
                <div class="fund__case_param_item">
                    <img src="/assets/icons/service_srok.svg" alt="">
                    <div>
                        <span>Сроки</span>
                        13 дней
                    </div>
                </div>
            </div>
        </div>

        <div class="fund__case">
            <img src="https://cdn.prod.website-files.com/60d7939987b38e9279246a11/621f40a30da11e22053ae67f_%D0%9A%D0%BE%D0%BF%D0%B8%D1%8F%2012826593297.webp" alt="">
            п.Черничное, Ленинградская обл.
            <div>
                <div class="fund__case_param_item">
                    <img src="/assets/icons/service_budget.svg" alt="">
                    <div>
                        <span>Бюджет</span>
                        1 300 000 ₽
                    </div>
                </div>
                <div class="fund__case_param_item">
                    <img src="/assets/icons/service_srok.svg" alt="">
                    <div>
                        <span>Сроки</span>
                        17 дней
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section>
    <h2>Комплектация "Все включено"</h2>
    <p>Специально для вас мы сделали процесс возведения фундамента максимально простым и понятным. Все сложности мы берем на себя, вам остается только наблюдать</p>
    <div class="fund__allinclusive">
        <div><img src="/assets/icons/fund_research.svg" alt="">Геологическое исследование</div>
        <div><img src="/assets/icons/fund_project.svg" alt="">Проектирование фундамента</div>
        <div><img src="/assets/icons/fund_kotlovan.svg" alt="">Рытье котлована</div>
        <div><img src="/assets/icons/fund_sand.svg" alt="">Отсыпка</div>
        <div><img src="/assets/icons/fund_opalubka.svg" alt="">Возведение опалубки</div>
        <div><img src="/assets/icons/fund_armirovanie.svg" alt="">Армирование</div>
        <div><img src="/assets/icons/fund_beton.svg" alt="">Залитие бетона</div>
    </div>
</section>

<section class="service__cta_section">
    <h2>Получите смету на фундамент и подробную консультацию</h2>
    <p>Заполните форму ниже и в ближайшее время с вами свяжется специалист, вышлет смету с этапами работ и ответит на все вопросы</p>
    <div class="service__cta_form_wrap">
        <script data-b24-form="inline/54/s65u02" data-skip-moving="true">
            (function(w,d,u){var s=d.createElement('script');s.async=true;s.src=u+'?'+(Date.now()/180000|0);var h=d.getElementsByTagName('script')[0];h.parentNode.insertBefore(s,h);})(window,document,'https://cdn-ru.bitrix24.ru/b14099574/crm/form/loader_54.js');
        </script>
    </div>
</section>