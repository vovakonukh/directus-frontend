<?php
// Нижнее мобильное меню (показывается до 767px).
// $topItems и $children приходят из header.php, $footer_form_code — из footer.php
$bottomMenuSection = explode('/', $uri)[0];
?>

<div class="bottom_menu"
    x-data="{ open: false, paneVisible: false, paneReleased: false }"
    x-effect="document.body.style.overflow = open ? 'hidden' : ''"
    @keydown.escape.window="open = false"
    @sticky-pane-visible.window="paneVisible = $event.detail"
    @sticky-pane-released.window="paneReleased = $event.detail">

    <div class="bottom_menu__backdrop" :class="{ 'active': open }" @click="open = false"></div>

    <!-- Всплывающее окно с полным меню -->
    <div class="bottom_menu__popup" :class="{ 'active': open }">

        <nav class="bottom_menu__nav">
            <?php foreach ($topItems as $item): ?>
                <?php $subs = array_filter($children, fn($child) => $child['parent']['label'] === $item['label']); ?>
                <?php if (!empty($subs)): ?>
                    <div x-data="{ dropdownOpen: false }">
                        <div class="bottom_menu__dropdown">
                            <a href="<?= trim($item['url']) ?>"><?= $item['label'] ?></a>
                            <span class="bottom_menu__dropdown_icon" :class="{ 'active': dropdownOpen }" @click="dropdownOpen = !dropdownOpen">
                                <img src="/assets/icons/dropdown_arrow.svg" alt="" />
                            </span>
                        </div>
                        <div class="bottom_menu__dropdown_list" x-show="dropdownOpen" style="display:none">
                            <?php foreach ($subs as $sub): ?>
                                <a href="<?= trim($sub['url']) ?>"><?= $sub['label'] ?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?= trim($item['url']) ?>"><?= $item['label'] ?></a>
                <?php endif; ?>
            <?php endforeach; ?>
        </nav>

        <div class="bottom_menu__contacts_row">
            <div class="bottom_menu__contacts">
                <a href="tel:<?= $contacts['phone'] ?? '' ?>"><?= $contacts['phone'] ?? '' ?></a>
                <a href="mailto:<?= $contacts['email'] ?? '' ?>"><?= $contacts['email'] ?? '' ?></a>
            </div>
            <div class="bottom_menu__messengers">
                <a href="<?= $contacts['vk_message'] ?? '' ?>" onclick="ym(62605987, 'reachGoal', 'messenger-vk'); return true;"><img src="/assets/icons/vk-colored-bg.webp" alt="VK" /></a>
                <a href="<?= $shiftTelegram ?>" onclick="ym(62605987, 'reachGoal', 'messenger-telegram'); return true;"><img src="/assets/icons/telegram-colored-bg.svg" alt="Telegram" /></a>
                <a href="<?= $contacts['max'] ?? '' ?>" onclick="ym(62605987, 'reachGoal', 'messenger-max'); return true;"><img src="/assets/icons/max.svg" alt="MAX" /></a>
            </div>
        </div>

        <?= $footer_form_code ?>
        <div class="bottom_menu__button" @click="open = false">Заказать звонок</div>

    </div>

    <!-- Нижняя полоса -->
    <nav class="bottom_menu__bar" :class="{ 'hidden': paneVisible && !paneReleased }">
        <a class="bottom_menu__item <?= $bottomMenuSection === 'projects' ? 'active' : '' ?>" href="/projects">
            <svg viewBox="0 0 24 24"><path d="M3 11.5 12 4l9 7.5"/><path d="M5.5 9.8V20h13V9.8"/><path d="M10 20v-5.5h4V20"/></svg>
            <span>Проекты</span>
        </a>
        <a class="bottom_menu__item <?= $bottomMenuSection === 'finished' ? 'active' : '' ?>" href="/finished">
            <svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2.5"/><circle cx="8.5" cy="10" r="1.6"/><path d="m4 17 5-4.5 3.5 3 3-2.5L20 17"/></svg>
            <span>Работы</span>
        </a>
        <button class="bottom_menu__item accent" type="button" :aria-expanded="open" @click="open = !open">
            <span class="bottom_menu__item_icon">
                <svg viewBox="0 0 24 24" x-show="!open"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
                <svg viewBox="0 0 24 24" x-show="open" style="display:none"><path d="m6 6 12 12M18 6 6 18"/></svg>
            </span>
            <span x-text="open ? 'Закрыть' : 'Меню'">Меню</span>
        </button>
        <a class="bottom_menu__item <?= $bottomMenuSection === 'contacts' ? 'active' : '' ?>" href="/contacts">
            <svg viewBox="0 0 24 24"><path d="M12 21s-6.5-6-6.5-11a6.5 6.5 0 0 1 13 0c0 5-6.5 11-6.5 11Z"/><circle cx="12" cy="10" r="2.4"/></svg>
            <span>Контакты</span>
        </a>
        <?= $footer_form_code ?>
        <button class="bottom_menu__item" type="button" @click="open = false">
            <!-- иконка /assets/icons/new_lead.svg, вставлена инлайном, чтобы красилась в цвет текста (в файле она чёрная) -->
            <svg class="filled" viewBox="0 0 24 24"><path d="m14.776 18.689 7.012-7.012c.133-.133.217-.329.217-.532 0-.179-.065-.363-.218-.515l-2.423-2.415c-.144-.143-.333-.215-.522-.215s-.378.072-.523.215l-7.027 6.996c-.442 1.371-1.158 3.586-1.265 3.952-.125.433.199.834.573.834.41 0 .696-.099 4.176-1.308zm-2.258-2.392 1.17 1.171c-.704.232-1.275.418-1.729.566zm.968-1.154 5.356-5.331 1.347 1.342-5.346 5.347zm-4.486-1.393c0-.402-.356-.75-.75-.75-2.561 0-2.939 0-5.5 0-.394 0-.75.348-.75.75s.356.75.75.75h5.5c.394 0 .75-.348.75-.75zm5-3c0-.402-.356-.75-.75-.75-2.561 0-7.939 0-10.5 0-.394 0-.75.348-.75.75s.356.75.75.75h10.5c.394 0 .75-.348.75-.75zm0-3c0-.402-.356-.75-.75-.75-2.561 0-7.939 0-10.5 0-.394 0-.75.348-.75.75s.356.75.75.75h10.5c.394 0 .75-.348.75-.75zm0-3c0-.402-.356-.75-.75-.75-2.561 0-7.939 0-10.5 0-.394 0-.75.348-.75.75s.356.75.75.75h10.5c.394 0 .75-.348.75-.75z"/></svg>
            <span>Заявка</span>
        </button>
    </nav>

</div>
