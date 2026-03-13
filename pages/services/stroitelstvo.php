<?php
$stroitelstvo = fetchItems('stroitelstvo', [
    'fields' => '*, tiles.tiles_id.icon, tiles.tiles_id.header, tiles.tiles_id.text, tiles.tiles_id.sort',
    'deep[tiles][_sort]' => 'tiles_id.sort'
]);

// singleton
if (is_array($stroitelstvo) && isset($stroitelstvo[0])) {
    $stroitelstvo = $stroitelstvo[0];
}

$hero = [
    'header' => $stroitelstvo['hero_header'] ?? '',
    'description' => $stroitelstvo['hero_description'] ?? '',
    'link' => $stroitelstvo['hero_link'] ?? '#',
];

$tiles = array_map(function($t) {
    return $t['tiles_id'];
}, $stroitelstvo['tiles'] ?? []);
?>

<style>
    .jumbo_slide.static {
        background: 
        linear-gradient(rgba(255, 255, 255, 0.3), rgba(255, 255, 255, 0.3)), 
        url('<?= getAssetUrl($stroitelstvo['hero_image']) ?>') center/cover no-repeat;
    }
</style>
<section class="jumbo_section">
    <div class="jumbo_slide static dark_text">
        <div class="jumbo_content_wrap">
            <p class="jumbo_header"><?= $hero['header'] ?></p>
            <p class="jumbo_description"><?= $hero['description'] ?></p>
            <a href="<?= $hero['link'] ?>" class="jumbo_button">
                <span>Рассчитать стоимость</span>
            </a>
        </div>
    </div>
</section>

<section class="main_advantages_section">
    <div class="main_advantages_inner">
        <?php foreach ($tiles as $tile): ?>
        <div class="main_advantage_card">
            <img src="<?= getAssetUrl($tile['icon']) ?>" alt="">
            <span><?= $tile['header'] ?></span>
            <span><?= $tile['text'] ?></span>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<section>
    <h2>У вас уже есть зарисовки? Посмотрите на свой дом до начала строительства</h2>
    <p>Отправьте нам планировку нарисованную от руки и мы сделаем объемную модель вашего проекта</p>
    <div class="build__draw_wrap">
        <img class="build__draw_first_image" src="/assets/images/build__draw_first.webp" />
        <img class="build__draw_arrow" src="/assets/icons/arrow-green-right.svg" />
        <div class="build__draw_col-2">
            <img class="build__draw_result_image" src="/assets/images/build__draw_result_1.webp">
            <img class="build__draw_result_image" src="/assets/images/build__draw_result_2.webp">
            <img class="build__draw_result_image" src="/assets/images/build__draw_result_3.webp">
            <img class="build__draw_result_image" src="/assets/images/build__draw_result_4.webp">
            <img class="build__draw_result_image" src="/assets/images/build__draw_result_5.webp">
            <img class="build__draw_result_image" src="/assets/images/build__draw_result_6.webp">
        </div>
    </div>
</section>

<section class="service__compare_background">
    <div class="services__compare_inner">
        <div class="service__compare_content">
            <h2>Сравните предложение или проект другой компании</h2>
            <p class="service__description">Бесплатно проанализируем смету и сравним с нашим расчетом</p>
            <div class="service__compare_list_item">
                <img src="/assets/icons/complectation_plus.webp" />
                <p>Подскажем как улучшить комплектацию без увеличения стоимости</p>
            </div>
            <div class="service__compare_list_item">
                <img src="/assets/icons/hidden_payments.webp" />
                <p>Укажем на возможные недостатки или скрытые платежи</p>
            </div>
        </div>
        <div class="service__embed_form_card">
            <script data-b24-form="inline/4/29asoq" data-skip-moving="true">
                (function(w,d,u){var s=d.createElement('script');s.async=true;s.src=u+'?'+(Date.now()/180000|0);var h=d.getElementsByTagName('script')[0];h.parentNode.insertBefore(s,h);})(window,document,'https://cdn-ru.bitrix24.ru/b14099574/crm/form/loader_4.js');
            </script>
        </div>
    </div>
</section>

<section class="service__calculator_section">
    <h2>Еще нет проекта? Подберем по вашим параметрам</h2>
    <p class="service__description">Ответьте на 7 вопросов и мы предложим несколько подходящих вариантов.</p>
    <a href="#" class="button">Ответить на вопросы</a>
</section>

<section class="service__projecting_background">
    <div class="service__projecting_inner">
        <div class="service__projecting_content">
            <h2>Мы понимаем, что идеального проекта не существует</h2>
            <p class="service__description">Каждый дом строим по индивидуальному проекту. Меняем размер дома, количество окон, переставляем террасу, добавляем комнаты и пр.</p>
            <div class="service__projecting_list_item">
                <img src="/assets/icons/house.webp" />
                <p>Создадим дом с нуля по вашим требованиям</p>
            </div>
            <div class="service__projecting_list_item">
                <img src="/assets/icons/pen.webp" />
                <p>Доработаем существующую планировку</p>
            </div>
            <div class="service__projecting_list_item">
                <img src="/assets/icons/tree.webp" />
                <p>Впишем дом на участок с учетом солнца и сторон света</p>
            </div>
        </div>
        <div class="service__embed_form_card">
            <script data-b24-form="inline/4/29asoq" data-skip-moving="true">
                (function(w,d,u){var s=d.createElement('script');s.async=true;s.src=u+'?'+(Date.now()/180000|0);var h=d.getElementsByTagName('script')[0];h.parentNode.insertBefore(s,h);})(window,document,'https://cdn-ru.bitrix24.ru/b14099574/crm/form/loader_4.js');
            </script>
        </div>
    </div>
</section>

<section class="background-color-white">
    <div class="build__land_wrap">
        <div class="build__land_info">
            <h2>Поможем подобрать участок</h2>
            <span>Расскажем</span>
            <ul>
                <li>На что смотреть при выборе участка</li>
                <li>Как проверить участок перед покупкой</li>
                <li>Какие бывают категории земель — СНТ, ИЖС и т. д.</li>
                <li>Как расположить дом на участке</li>
            </ul>
            <span>Поможем</span>
            <ul>
                <li>сделаем пробное бурение, оценим несущую способность грунта</li>
                <li>сделаем геодезию участка, вынесем границы участка</li>
            </ul>
            <a class="button">Подобрать участок</a>
        </div>
        <div class="build__land_img_frame">
            <img src="/assets/images/build__land.webp" alt="">
        </div>
    </div>
</section>

<section>
    <h2>Что говорят те, кто построил дом у нас</h2>
    <div class="service__feedback_inner">
        <div class="feedback__card">
            <span>Ионин Валерий</span>
            <div class="feedback__stars_wrap">
                <img src="/assets/icons/rating-star-full-yellow.svg" />
                <img src="/assets/icons/rating-star-full-yellow.svg" />
                <img src="/assets/icons/rating-star-full-yellow.svg" />
                <img src="/assets/icons/rating-star-full-yellow.svg" />
                <img src="/assets/icons/rating-star-full-yellow.svg" />
                <span>01.01.2025</span>
            </div>
            <p>Дом сдан!!! Ура!!! Ооочень долго выбирали строительную компанию, объездили всю Лен.область: смотрели на строящиеся и готовые дома разных организаций. В итоге тщательного отбора в лидеры выбились несколько стоит.фирм. Вели переговоры, обсуждали, задавали вопросы. Терпению менеджера Алексея можно позавидовать. Наконец, выбор был сделан, о чем ни капли не жалеем!! Все четко, по делу, вежливо, качественно, всегда на связи. Построили в срок, очень аккуратно. Соседи тоже оценили безупречную аккуратность и точность. Однозначно рекомендуем Класс Хаус. Качество на высоте!!!</p>
            <div class="feedback__images_collection" data-fancybox="feedback-gallery">
                <a href="/assets/images/feedback/valensiya-1.jpg" data-fancybox="feedback-gallery">
                    <img class="feedback__image" src="/assets/images/feedback/valensiya-1.jpg" />
                </a>
                <a href="/assets/images/feedback/valensiya-2.jpg" data-fancybox="feedback-gallery">
                    <img class="feedback__image" src="/assets/images/feedback/valensiya-2.jpg" />
                </a>
            </div>
            <a class="feedback__source" href="#">
                <span>Отзыв оставлен на Яндекс Картах</span>
                <img src="/assets/icons/external_link.svg" />
            </a>
        </div>
        <div class="service__feedback_big_rating_wrap">
            <p>5,0</p>
            <span>Наш рейтинг на Яндекс Картах</span>
            <div class="service__feedback_stars">
                <img src="/assets/icons/rating-star-full-yellow.svg" />
                <img src="/assets/icons/rating-star-full-yellow.svg" />
                <img src="/assets/icons/rating-star-full-yellow.svg" />
                <img src="/assets/icons/rating-star-full-yellow.svg" />
                <img src="/assets/icons/rating-star-full-yellow.svg" />
            </div>
        </div>
    </div>
</section>

<section class="service__materials_section">
    <h2>Какие материалы мы используем</h2>
    <p class="service__description">Мы используем строительные материалы высокого качества, которые проверены временем и не уступают по своим характеристикам импортным аналогам</p>
    <div class="service__materials_wrap">
        <div class="service__materials_item">
            <img src="/assets/images/pilomaterial.webp" />
            <span>Пиломатериал из хвойных пород</span>
        </div>
        <div class="service__materials_item">
            <img src="/assets/images/uteplitel.webp" />
            <span>Базальтовая вата</span>
        </div>
        <div class="service__materials_item">
            <img src="/assets/images/steklopaket.webp" />
            <span>Стеклопакеты</span>
        </div>
        <div class="service__materials_item">
            <img src="/assets/images/imitacia_brusa.webp" />
            <span>Имитация бруса</span>
        </div>
        <div class="service__materials_item">
            <img src="/assets/images/metallocherepizza.webp" />
            <span>Металлочерепица</span>
        </div>
    </div>
</section>

<section class="background-color-white">
    <div class="build__tour_inner">
        <div class="build__tour_col1">
            <h2>Лучше один раз увидеть, чем 100 раз услышать</h2>
            <p class="service__description">Мы провезем вас по нашим строящимся домам, и вы сможете самостоятельно оценить качество строительства и наш подход к работе.</p>
            <script data-b24-form="inline/50/hp7jmn" data-skip-moving="true">
                (function(w,d,u){var s=d.createElement('script');s.async=true;s.src=u+'?'+(Date.now()/180000|0);var h=d.getElementsByTagName('script')[0];h.parentNode.insertBefore(s,h);})(window,document,'https://cdn-ru.bitrix24.ru/b14099574/crm/form/loader_50.js');
            </script>
        </div>
        <div class="build__tour_col2">
            <img src="/assets/images/build__tour.webp" />
            <p class="image_description">Так выглядит строящийся каркасный дом</p>
        </div>
    </div>
</section>

<section class="service__contract_section">
    <div class="service__contract_inner">
        <div class="service__contract_col_1">
            <h2>Вы защищены по договору на 100%</h2>
            <p class="service__description">Перед началом работ мы заключаем договор, поэтому вы защищены законодательством РФ</p>
        </div>
        <div class="service__contract_col_2">
            <h3>В договоре зафиксированы:</h3>
            <div class="service__contract_list">
                <img src="/assets/icons/service__money.webp" />
                <p>Стоимость работ</p>
            </div>
            <div class="service__contract_list">
                <img src="/assets/icons/service__payments.webp" />
                <p>Порядок оплаты</p>
            </div>
            <div class="service__contract_list">
                <img src="/assets/icons/service__period.webp" />
                <p>Сроки работы</p>
            </div>
            <div class="service__contract_list">
                <img src="/assets/icons/service__warranty.webp" />
                <p>Гарантия 5 лет</p>
            </div>
            <div class="service__contract_list">
                <img src="/assets/icons/service__smeta.webp" />
                <p>Спецификация используемых материалов</p>
            </div>
        </div>
    </div>
</section>

<section>
    <h2 class="margin-bottom-0">Никаких скрытых платежей</h2>
    <p class="build__payments_description">Оплата происходит поэтапно, притом основную часть вы платите, когда строительные материалы уже разгружают на ваш участок</p>
    <div class="build__payments_inner">
        <div class="build__payments_list">
            <div class="build__payments_list_item">
                <span>1</span>
                <div class="build__payments_list_item_text">
                    <strong>100 000 руб</strong>
                    <p>После подписания договора. Нужен для начала подготовки проектной документации, заказа материалов на объект.</p>
                </div>
            </div>
            <div class="build__payments_list_item">
                <span>2</span>
                <div class="build__payments_list_item_text">
                    <strong>60% от суммы договора</strong>
                    <p>Во время заезда на участок. Приезжает машина со всем пиломатериалом, прораб, рабочие. Оплата пиломатериала, командировочные рабочим.</p>
                </div>
            </div>
            <div class="build__payments_list_item">
                <span>3</span>
                <div class="build__payments_list_item_text">
                    <strong>15% от суммы договора</strong>
                    <p>После возведения силового каркаса и укладки кровельного материала.</p>
                </div>
            </div>
            <div class="build__payments_list_item">
                <span>4</span>
                <div class="build__payments_list_item_text">
                    <strong>15% от суммы договора</strong>
                    <p>После окончания работ по наружней отделке.</p>
                </div>
            </div>
            <div class="build__payments_list_item">
                <span>5</span>
                <div class="build__payments_list_item_text">
                    <strong>10% от суммы договора</strong>
                    <p>После окончания всех работ.</p>
                </div>
            </div>
        </div>
        <div class="build__payments_video_wrap">
            <iframe src="https://vkvideo.ru/video_ext.php?oid=-195326596&id=456239196&hd=3" width="300" height="535" allow="autoplay; encrypted-media; fullscreen; picture-in-picture; screen-wake-lock;" frameborder="0" allowfullscreen></iframe>
        </div>
    </div>
</section>

<section class="service__manager_section">
    <h2>Персональный менеджер на весь срок строительства</h2>
    <p>За ваш дом будет отвечать руководитель проекта который всегда в курсе ситуации на участке и которому можно задать любые вопросы</p>
</section>

<section class="service__cta_section">
    <h2>Получите подробную комплектацию дома и консультацию по строительству</h2>
    <p>Заполните форму и в ближайшее время с вами свяжется специалист, вышлет подробную комплектацию и ответит на все вопросы</p>
    <div class="service__cta_form_wrap">
        <script data-b24-form="inline/54/s65u02" data-skip-moving="true">
            (function(w,d,u){var s=d.createElement('script');s.async=true;s.src=u+'?'+(Date.now()/180000|0);var h=d.getElementsByTagName('script')[0];h.parentNode.insertBefore(s,h);})(window,document,'https://cdn-ru.bitrix24.ru/b14099574/crm/form/loader_54.js');
        </script>
    </div>
</section>