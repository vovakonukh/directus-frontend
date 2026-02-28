<?php

$contacts = fetchItems('contacts', [
    'fields' => 'phone,address_spb,address_msk,telegram_message,whatsapp,vk_group,youtube,telegram_channel'
]);

?>

<section class="page__wrap">
    <div class="breadcrumbs">
        <a href="/">Главная /</a>
        <a href="/contacts">Контакты</a>
    </div>
    <h1>Контакты</h1>
    <div class="contacts__columns">
        <div>
            <span class="contacts__subheader">Офис в Санкт-Петербурге</span>
            <div class="contacts__element">
                <img src="/assets/icons/geo-green.svg" />
                <span><?= htmlspecialchars($contacts['address_spb']) ?></span>
            </div>
            <div class="contacts__element margin-bottom-40">
                <img src="/assets/icons/phone-green.svg" />
                <span><?= htmlspecialchars($contacts['phone']) ?></span>
            </div>
            <span class="contacts__subheader">Переговорная в Москве</span>
            <div class="contacts__disclaimer">Встреча возможна только по предварительному согласованию</div>
            <div class="contacts__element">
                <img src="/assets/icons/geo-green.svg" />
                <span><?= htmlspecialchars($contacts['address_msk']) ?></span>
            </div>
        </div>
        <div>
            <div class="row margin-bottom-30">
                <a class="contacts__messenger_button telegram" href="<?= htmlspecialchars($contacts['telegram_message']) ?>">
                    <img src="/assets/icons/telegram-white.svg" />
                    <span>Написать в Телеграм</span>
                </a>
                <a class="contacts__messenger_button whatsapp" href="<?= htmlspecialchars($contacts['whatsapp']) ?>">
                    <img src="/assets/icons/whatsapp-white.svg" />
                    <span>Написать в WhatsApp</span>
                </a>
            </div>
            <span class="contacts__subheader">Следите за нами в соцсетях</span>
            <div class="row">
                <div class="column">
                    <a class="contacts__element" href="<?= htmlspecialchars($contacts['vk_group']) ?>">
                        <img src="/assets/icons/vk-colored.svg" />
                        <span>Вконтакте</span>
                    </a>
                    <a class="contacts__element" href="https://ok.ru/group/63361648361523">
                        <img src="/assets/icons/ok-colored.svg" />
                        <span>Одноклассники</span>
                    </a>
                </div>
                <div class="column">
                    <a class="contacts__element" href="<?= htmlspecialchars($contacts['youtube']) ?>">
                        <img src="/assets/icons/youtube-colored.svg" />
                        <span>Youtube</span>
                    </a>
                    <a class="contacts__element" href="<?= htmlspecialchars($contacts['telegram_channel']) ?>">
                        <img src="/assets/icons/telegram-colored.svg" />
                        <span>Телеграм</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>