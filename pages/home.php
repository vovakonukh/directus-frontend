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
        <?php foreach ($projects as $item): ?>
            <?php include 'includes/cards/project_card.php'; ?>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>


<section>
    <h2>С нами вы можете использовать льготную ипотеку</h2>

	<div class="ipoteka_section">
		<div class="ipoteka_descr_wrap">
			<div class="ipoteka_descr_list">
				<li>работаем по эскроу счету</li>
				<li>консультируем по сбору документов</li>
				<li>помогаем правильно оформить заявку</li>
				<li>подберем условия под вашу ситуацию</li>
				<li>подадим заявку в несколько банков</li>
				<li>одобрение за 24 часа без визита банк</li>
			</div>
			<?php
                $ipoteka_form = fetchItems('forms', [
                    'fields' => 'bitrix_code',
                    'filter[id][_eq]' => 4
                ]);
                $ipoteka_form_code = $ipoteka_form[0]['bitrix_code'] ?? '';
                ?>

                <?= $ipoteka_form_code ?>
                <a class="button">Одобрить ипотеку</a>
		</div>
	
		<div class="ipoteka_banks_wrap">
			<div class="ipoteka_banks_logo_wrap">
				<img src="assets/icons/banks/sber_logo.svg" />
				<img src="assets/icons/banks/rshb_logo.svg" />
				<img src="assets/icons/banks/domrf_logo.svg" />
				<img src="assets/icons/banks/vtb_logo.svg" />
				<img src="assets/icons/banks/alfabank_logo.webp" />
			</div>
			<h3 class="ipoteka_banks_header">Пример условий по ипотеке на строительство дома</h3>
			<div class="ipoteka_conditions_wrap">
				<div class="ipoteka_conditions_item">
					<span>Ставка</span>
					<span>от 3%</span>
				</div>

				<div class="ipoteka_conditions_item">
					<span>Сумма</span>
					<span>до 12 млн</span>
				</div>

				<div class="ipoteka_conditions_item">
					<span>Срок</span>
					<span>до 360 мес</span>
				</div>
			</div>
		</div>
	</div>
</section>

<section>
    <h1>СТРОИТЕЛЬНАЯ КОМПАНИЯ «КЛАСС ХАУС»</h1>
    <div class="tile_wrap">
        <div class="tile_item">
            <img src="assets/icons/camera.svg" />
            <div class="tile_item_text_wrap">
                <span>Следите онлайн за ходом строительства</span>
                <p>Каждые 2-3 дня руководитель проекта присылает фотографии со строительной площадки</p>
            </div>
        </div>

        <div class="tile_item">
            <img src="assets/icons/contract.svg" />
            <div class="tile_item_text_wrap">
                <span>Прозрачная смета и договор</span>
                <p>Проходим по каждому пункту договора при заключении. Никаких подводных камней.</p>
            </div>
        </div>

        <div class="tile_item">
            <img src="assets/icons/house_project.svg" />
            <div class="tile_item_text_wrap">
                <span>Бесплатный индивидуальный проект</span>
                <p>Воплотите свои идеи в жизнь вместе с нашим архитектором. Выражайте свою индивидуальность</p>
            </div>
        </div>

        <div class="tile_item">
            <img src="assets/icons/shield_warranty.svg" />
            <div class="tile_item_text_wrap">
                <span>Гарантия 5 лет</span>
                <p>Гарантия прописана в договоре и мы никогда не отказываем в гарантийных работах</p>
            </div>
        </div>
    </div>
</section>

<?php
$finished = fetchItems('finished', [
    'fields' => 'id,name,slug,main_image,plan_1,floors,square,length,width,bedrooms,wc,finish_date,location,construction_period',
    'filter[show_on_main][_eq]' => 'true',
    'sort' => 'sort'
]);
?>

<section>
    <h2>Мы построили</h2>
    <div class="projects__collection">
        <?php foreach ($finished as $item): ?>
            <?php include 'includes/cards/finished_card.php'; ?>
        <?php endforeach; ?>
    </div>
</section>

<?php
$feedback = fetchItems('feedback', [
    'fields' => 'id,client_name,date,content,link,show_on_main,images.directus_files_id',
    'filter[show_on_main][_eq]' => 'true',
    'sort' => '-date',
    'limit' => 2
]);
?>

<section>
    <h2>Отзывы</h2>
    <div class="feedback_gallery">
        <?php foreach ($feedback as $item): ?>
            <?php include 'includes/cards/feedback_card.php'; ?>
        <?php endforeach; ?>
    </div>
</section>

<?php
$homepage = fetchItems('homepage', [
    'fields' => 'map_code, seo_text'
]);
// singleton возвращает массив с одним элементом или объект — проверь что приходит
$map_code = is_array($homepage) && isset($homepage[0]) ? $homepage[0]['map_code'] : $homepage['map_code'] ?? '';
$seo_text = is_array($homepage) && isset($homepage[0]) ? $homepage[0]['seo_text'] : $homepage['seo_text'] ?? '';
?>

<section>
    <h2>География построенных домов</h2>
    <?= $map_code ?>
</section>

<section>
    <div class="mainpage__content_inner">
        <?= $seo_text ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>