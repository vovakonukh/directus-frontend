<?php
$contacts = fetchItems('contacts');
$header = fetchItems('header', ['fields' => '*,form.bitrix_code']);
$menu = fetchItems('menu', [
    'fields' => '*,parent.label',
    'sort' => 'sort',
    'filter[status][_eq]' => 'published'
]);
$topItems = array_filter($menu, fn($item) => $item['parent'] === null);
$children = array_filter($menu, fn($item) => $item['parent'] !== null);
?>
<section class="header">
    <div class="header_info">

        <div class="header_info__logo_wrap">
            <a href="/"><img src="/assets/logo/logo-colored.svg" /></a>
            <span>Санкт-Петербург <br/> Москва</span>
        </div>

        <div class="header_info__rating_wrap">
            <span class="header_info__rating_number"><?= $contacts['yandex_rating'] ?? '' ?></span>
            <div class="column">
                <div class="header_info__rating_stars_wrap">
                    <img src="/assets/icons/rating-star-full-yellow.svg" />
                    <img src="/assets/icons/rating-star-full-yellow.svg" />
                    <img src="/assets/icons/rating-star-full-yellow.svg" />
                    <img src="/assets/icons/rating-star-full-yellow.svg" />
                    <img src="/assets/icons/rating-star-full-yellow.svg" />
                </div>
                <span class="header_info__rating_label_bold">Рейтинг Яндекс</span>
                <span class="header_info__rating_label_light">на основании <?= $contacts['yandex_feedback_amount'] ?? '' ?> отзывов</span>
            </div>
        </div>

        <div class="header_info__socials_wrap">
            <div class="header_info__online_wrap">
                <div class="header_info__online_circle"></div>
                <span class="header_info__online_label">Пишите, мы онлайн</span>
            </div>
            <div class="header_info__messengers">
                <a href="<?= $contacts['vk_message'] ?? '' ?>"><img src="/assets/icons/vk-colored-bg.webp" /></a>
                <a href="<?= $contacts['telegram_message'] ?? '' ?>"><img src="/assets/icons/telegram-colored-bg.svg" /></a>
                <a href="<?= $contacts['whatsapp'] ?? '' ?>"><img src="/assets/icons/whatsapp-colored-bg.svg" /></a>
            </div>
        </div>

        <div class="header_info__contacts_wrap">
            <a class="header_info_phone" href="tel:<?= $contacts['phone'] ?? '' ?>"><?= $contacts['phone'] ?? '' ?></a>
            <a class="header_info_email" href="mailto:<?= $contacts['email'] ?? '' ?>"><?= $contacts['email'] ?? '' ?></a>
        </div>

        <?= $header['form']['bitrix_code'] ?? '' ?>
        <div class="button header_info__button"><?= $header['button_text'] ?? '' ?></div>

        <div class="header_info__burger_menu" x-data @click="$dispatch('toggle-mobile-menu')">
            <img src="/assets/icons/burger_menu.svg" />
        </div>
    </div>

    <!-- Десктопное меню -->
    <nav class="header_menu">
        <ul>
            <?php foreach ($topItems as $item): ?>
                <?php $subs = array_filter($children, fn($child) => $child['parent']['label'] === $item['label']); ?>
                <?php if (!empty($subs)): ?>
                    <li>
                        <a href="<?= $item['url'] ?>"><?= $item['label'] ?></a>
                        <ul class="header_menu__dropdown_content">
                            <?php foreach ($subs as $sub): ?>
                                <li><a href="<?= $sub['url'] ?>"><?= $sub['label'] ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                <?php else: ?>
                    <li><a href="<?= $item['url'] ?>"><?= $item['label'] ?></a></li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </nav>

    <!-- Мобильное меню (Alpine только для открытия/закрытия) -->
    <div class="header_mobile_menu" x-data="{ open: false }" @toggle-mobile-menu.window="open = true" :class="{ 'active': open }">
        <div class="header_mobile_menu__close_button" @click="open = false">
            <img src="/assets/icons/close_cross.svg" />
        </div>
        <?php foreach ($topItems as $item): ?>
            <?php $subs = array_filter($children, fn($child) => $child['parent']['label'] === $item['label']); ?>
            <?php if (!empty($subs)): ?>
                <div x-data="{ dropdownOpen: false }">
                    <div class="header_mobile_menu__dropdown">
                        <a href="<?= $item['url'] ?>"><?= $item['label'] ?></a>
                        <span class="header_mobile_menu__dropdown_icon" @click="dropdownOpen = !dropdownOpen" style="cursor:pointer;">
                            <img src="/assets/icons/dropdown_arrow.svg" :style="dropdownOpen ? 'transform:rotate(180deg)' : ''" />
                        </span>
                    </div>
                    <div class="header_mobile_menu__dropdown_list" :style="dropdownOpen ? 'display:flex' : 'display:none'">
                        <?php foreach ($subs as $sub): ?>
                            <a href="<?= $sub['url'] ?>"><?= $sub['label'] ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <a href="<?= $item['url'] ?>"><?= $item['label'] ?></a>
            <?php endif; ?>
        <?php endforeach; ?>
        <div class="header_mobile_menu__contacts__wrap margin-bottom-30">
            <a href="tel:<?= $contacts['phone'] ?? '' ?>"><?= $contacts['phone'] ?? '' ?></a>
            <a href="mailto:<?= $contacts['email'] ?? '' ?>"><?= $contacts['email'] ?? '' ?></a>
        </div>
        <div class="button margin-bottom-20">Заказать звонок</div>
        <div class="header_info__messengers">
            <a href="<?= $contacts['vk_message'] ?? '' ?>"><img src="/assets/icons/vk-colored-bg.webp" /></a>
            <a href="<?= $contacts['telegram_message'] ?? '' ?>"><img src="/assets/icons/telegram-colored-bg.svg" /></a>
            <a href="<?= $contacts['whatsapp'] ?? '' ?>"><img src="/assets/icons/whatsapp-colored-bg.svg" /></a>
        </div>
    </div>
</section>