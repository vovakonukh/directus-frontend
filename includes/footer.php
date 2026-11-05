<?php
$footer_form = fetchItems('forms', [
    'fields' => 'bitrix_code',
    'filter[id][_eq]' => 1
]);
$footer_form_code = $footer_form[0]['bitrix_code'] ?? '';

// Соцсети: пустые ссылки из Directus не выводим
$footer_socials = array_filter([
    ['url' => $contacts['max_channel'] ?? '',      'icon' => 'max-white.svg',      'name' => 'Канал Класс Хаус в MAX'],
    ['url' => $contacts['telegram_channel'] ?? '', 'icon' => 'telegram-white.svg', 'name' => 'Telegram-канал Класс Хаус'],
    ['url' => $contacts['vk_group'] ?? '',         'icon' => 'vk-white.svg',       'name' => 'Группа Класс Хаус ВКонтакте'],
    ['url' => $contacts['youtube'] ?? '',          'icon' => 'youtube-white.svg',  'name' => 'YouTube-канал Класс Хаус'],
    ['url' => 'https://ok.ru/group/63361648361523', 'icon' => 'ok-white.svg',      'name' => 'Класс Хаус в Одноклассниках'],
], fn($s) => !empty($s['url']));
?>

<footer class="footer">
    <div class="footer__inner">

        <div class="footer__brand">
            <a href="/" aria-label="Класс Хаус — на главную">
                <img class="footer__logo" src="<?= asset('/assets/icons/logo-white.svg') ?>" alt="Класс Хаус" width="150" height="40" />
            </a>
            <p class="footer__about">
                Строим каркасные дома под ключ в Санкт-Петербурге, Ленинградской и Московской области.
                Фиксированная смета, договор и гарантия.
            </p>
            <div class="footer__socials">
                <?php foreach ($footer_socials as $s): ?>
                    <a class="footer__social" href="<?= htmlspecialchars($s['url']) ?>" target="_blank" rel="noopener" aria-label="<?= $s['name'] ?>" title="<?= $s['name'] ?>">
                        <img src="<?= asset('/assets/icons/' . $s['icon']) ?>" alt="" width="20" height="20" />
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <nav class="footer__col" aria-label="Каталог">
            <p class="footer__title">Каталог</p>
            <ul class="footer__list">
                <li><a class="footer__link" href="/projects">Проекты каркасных домов</a></li>
                <li><a class="footer__link" href="/finished">Построенные дома</a></li>
                <li><a class="footer__link" href="/feedback">Отзывы клиентов</a></li>
                <li><a class="footer__link" href="/video">Видео с объектов</a></li>
                <li><a class="footer__link" href="/blog">Блог о строительстве</a></li>
            </ul>
        </nav>

        <nav class="footer__col" aria-label="Услуги">
            <p class="footer__title">Услуги</p>
            <ul class="footer__list">
                <li><a class="footer__link" href="/services/stroitelstvo">Строительство домов под ключ</a></li>
                <li><a class="footer__link" href="/services/fundament">Фундаменты</a></li>
                <li><a class="footer__link" href="/services/inzhenerka">Инженерные системы</a></li>
                <li><a class="footer__link" href="/ipoteka">Дом в ипотеку</a></li>
            </ul>
        </nav>

        <div class="footer__col footer__contacts">
            <p class="footer__title">Контакты</p>
            <address class="footer__address">
                <a class="footer__contact" href="tel:<?= htmlspecialchars($contacts['phone'] ?? '') ?>">
                    <img src="<?= asset('/assets/icons/phone-white.svg') ?>" alt="" width="18" height="18" />
                    <?= htmlspecialchars($contacts['phone'] ?? '') ?>
                </a>
                <a class="footer__contact" href="mailto:<?= htmlspecialchars($contacts['email'] ?? '') ?>">
                    <img src="<?= asset('/assets/icons/email-white.svg') ?>" alt="" width="18" height="18" />
                    <?= htmlspecialchars($contacts['email'] ?? '') ?>
                </a>
                <?php if (!empty($contacts['address_spb'])): ?>
                    <span class="footer__office"><?= htmlspecialchars($contacts['address_spb']) ?></span>
                <?php endif; ?>
            </address>
            <?= $footer_form_code ?>
            <div class="button footer__button" data-b24-form="click/6/t86koa">Заказать звонок</div>
        </div>

    </div>

    <div class="footer__bottom">
        <span>© <?= date('Y') ?> Класс Хаус · ООО «СККХ» · ИНН 7840095917 · ОГРН 1217800062754</span>
        <span>Информация на сайте не является публичной офертой</span>
        <span class="footer__bottom_links">
            <a href="/contacts">Контакты</a>
            <a href="/postavshhikam">Поставщикам</a>
            <a href="/policy">Политика конфиденциальности</a>
        </span>
    </div>
</footer>
