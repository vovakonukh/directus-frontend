<?php
$contacts = fetchItems('contacts', ['fields' => '*']);
$footer_form = fetchItems('forms', [
    'fields' => 'bitrix_code',
    'filter[id][_eq]' => 1
]);
$footer_form_code = $footer_form[0]['bitrix_code'] ?? '';
?>

<footer>
    <div class="footer__column opacity-60">
        <img class="footer__logo" src="/assets/icons/logo-white.svg" />
        <div class="row margin-bottom-10">
            <img class="footer__contacts_icon" src="/assets/icons/phone-white.svg" />
            <a class="footer__contacts_text" href="tel:<?= $contacts['phone'] ?>">
                <?= $contacts['phone'] ?>
            </a>
        </div>
        <div class="row margin-bottom-20">
            <img class="footer__contacts_icon" src="/assets/icons/email-white.svg" />
            <a class="footer__contacts_text" href="mailto:<?= $contacts['email'] ?>">
                <?= $contacts['email'] ?>
            </a>
        </div>
        <div class="row margin-bottom-30">
            <a href="<?= $contacts['instagram'] ?>"><img class="footer__socials_icon" src="/assets/icons/insta-white.svg" /></a>
            <a href="<?= $contacts['telegram_channel'] ?>"><img class="footer__socials_icon" src="/assets/icons/telegram-white.svg" /></a>
            <a href="<?= $contacts['vk_group'] ?>"><img class="footer__socials_icon" src="/assets/icons/vk-white.svg" /></a>
            <a href="<?= $contacts['youtube'] ?>"><img class="footer__socials_icon" src="/assets/icons/youtube-white.svg" /></a>
            <a href="https://ok.ru/group/63361648361523"><img class="footer__socials_icon" src="/assets/icons/ok-white.svg" /></a>
        </div>
        <span class="footer__llc_text">
            ООО "СККХ"<br />
            ИНН: 7840095917<br />
            ОГРН: 1217800062754<br />
            <br />
            Информация на сайте не является публичной офертой
        </span>
    </div>
    <div class="footer__column">
        <a class="footer__link" href="/projects">Каталог проектов</a>
        <a class="footer__link" href="/finished">Построенные дома</a>
        <a class="footer__link" href="/video">Видео</a>
        <a class="footer__link" href="/ipoteka">Строительство в ипотеку</a>
        <span class="footer__header">Услуги</span>
        <a class="footer__link" href="#">Проектирование</a>
        <a class="footer__link" href="#">Фундаменты</a>
        <a class="footer__link" href="/stroitelstvo">Строительство</a>
        <a class="footer__link" href="/inzhenerka">Инженерные системы</a>
        <a class="footer__link" href="#">Вентиляция</a>
    </div>
    <div class="footer__column">
        <?= $footer_form_code ?>
        <div class="button footer__button" data-b24-form="click/6/t86koa">Заказать звонок</div>
        <span class="footer__header">Разное</span>
        <a class="footer__link" href="#">Работа у нас</a>
        <a class="footer__link" href="/postavshhikam">Поставщикам</a>
        <a class="footer__link" href="/policy">Конфиденциальность</a>
        <a class="footer__link" href="/blog">Блог</a>
        <a class="footer__link" href="/contacts">Контакты</a>
    </div>
</footer>