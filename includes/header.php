<?php
$header = fetchItems('header', ['fields' => '*,form.bitrix_code']);
$menu = fetchItems('menu', [
    'fields' => '*,parent.label',
    'sort' => 'sort',
    'filter[status][_eq]' => 'published'
]);
$topItems = array_filter($menu, fn($item) => $item['parent'] === null);
$children = array_filter($menu, fn($item) => $item['parent'] !== null);
?>


<!-- Yandex.Metrika counter -->
<script type="text/javascript">
    (function(m,e,t,r,i,k,a){
        m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
        m[i].l=1*new Date();
        for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}
        k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)
    })(window, document,'script','https://mc.yandex.ru/metrika/tag.js', 'ym');

    ym(62605987, 'init', {webvisor:true, clickmap:true, referrer: document.referrer, url: location.href, accurateTrackBounce:true, trackLinks:true});
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/62605987" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->

<!-- Top.Mail.Ru counter -->
<script type="text/javascript">
var _tmr = window._tmr || (window._tmr = []);
_tmr.push({id: "3324398", type: "pageView", start: (new Date()).getTime(), pid: "USER_ID"});
(function (d, w, id) {
  if (d.getElementById(id)) return;
  var ts = d.createElement("script"); ts.type = "text/javascript"; ts.async = true; ts.id = id;
  ts.src = "https://top-fwz1.mail.ru/js/code.js";
  var f = function () {var s = d.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ts, s);};
  if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); }
})(document, window, "tmr-code");
</script>
<noscript><div><img src="https://top-fwz1.mail.ru/counter?id=3324398;js=na" style="position:absolute;left:-9999px;" alt="Top.Mail.Ru" /></div></noscript>
<!-- /Top.Mail.Ru counter -->

<!-- UIS -->
    <script type="text/javascript" async src="https://app.uiscom.ru/static/cs.min.js?k=OvAQ2cPIHwADag6GyBm_tvvgsltTzHIL"></script>
<!-- UIS -->

<!-- Roistat Counter Start -->
<script>
(function(w, d, s, h, id) {
    w.roistatProjectId = id; w.roistatHost = h; w.roistatPage = d.location.href; w.roistatReferrer = d.referrer;
    var p = d.location.protocol == "https:" ? "https://" : "http://";
    var u = /^.*roistat_visit=[^;]+(.*)?$/.test(d.cookie) ? "/dist/module.js" : "/api/site/1.0/"+id+"/init?referrer="+encodeURIComponent(d.location.href);
    var js = d.createElement(s); js.charset="UTF-8"; js.async = 1; js.src = p+h+u; var js2 = d.getElementsByTagName(s)[0]; js2.parentNode.insertBefore(js, js2);
})(window, document, 'script', 'cloud.roistat.com', 'bbed64cef44909172e74d8d17aba0985');
</script>
<!-- Roistat Counter End -->

<!-- Marquiz script start -->
<script>
(function(w, d, s, o){
  var j = d.createElement(s); j.async = true; j.src = '//script.marquiz.ru/v2.js';j.onload = function() {
    if (document.readyState !== 'loading') Marquiz.init(o);
    else document.addEventListener("DOMContentLoaded", function() {
      Marquiz.init(o);
    });
  };
  d.head.insertBefore(j, d.head.firstElementChild);
})(window, document, 'script', {
    host: '//quiz.marquiz.ru',
    region: 'ru',
    id: '67347643f5091100264023cb',
    autoOpen: false,
    autoOpenFreq: 'once',
    openOnExit: false,
    disableOnMobile: false
  }
);
</script>
<!-- Marquiz script end -->


<section class="header">
    <div class="hdr">

        <!-- Логотип -->
        <a class="hdr__logo" href="/" aria-label="Класс Хаус — на главную">
            <img src="<?= asset('/assets/logo/logo-colored.svg') ?>" alt="Класс Хаус" />
        </a>

        <!-- Доверие: эскроу + рейтинг (средний приоритет, прячется на узких экранах) -->
        <div class="hdr__trust">
            <?php if (!empty($header['domrf_deals'])): ?>
                <div class="hdr__badge">
                    <span class="hdr__badge_num"><?= $header['domrf_deals'] ?></span>
                    <span class="hdr__badge_text">сделок по эскроу<br>на строим.дом.рф</span>
                </div>
            <?php endif; ?>
            <?php if (!empty($contacts['yandex_rating'])): ?>
                <div class="hdr__badge">
                    <span class="hdr__badge_num"><?= $contacts['yandex_rating'] ?></span>
                    <span class="hdr__badge_text">
                        <span class="hdr__stars" aria-label="5 звёзд">★★★★★</span>
                        Яндекс · <?= $contacts['yandex_feedback_amount'] ?? '' ?> отзывов
                    </span>
                </div>
            <?php endif; ?>
        </div>

        <!-- Мессенджеры -->
        <div class="hdr__messengers">
            <?php
            // Рабочее время по Москве из Directus (header.start_work / finish_work, формат "09:00:00").
            // PHP задаёт начальное состояние (без мигания), Alpine пересчитывает в браузере — на случай кэша страницы.
            $toMin = fn($t, $def) => preg_match('/^(\d{1,2}):(\d{2})/', $t ?? '', $m) ? $m[1] * 60 + $m[2] : $def;
            $workStart = $toMin($header['start_work'] ?? null, 9 * 60);
            $workEnd   = $toMin($header['finish_work'] ?? null, 21 * 60);
            $mskNow    = new DateTime('now', new DateTimeZone('Europe/Moscow'));
            $nowMin    = $mskNow->format('G') * 60 + (int) $mskNow->format('i');
            $isNight   = $nowMin < $workStart || $nowMin >= $workEnd;
            ?>
            <span class="hdr__online<?= $isNight ? ' is-night' : '' ?>"
                  x-data="{ night: <?= $isNight ? 'true' : 'false' ?> }"
                  x-init="const [h, m] = new Intl.DateTimeFormat('ru-RU', { hour: 'numeric', minute: 'numeric', hourCycle: 'h23', timeZone: 'Europe/Moscow' }).format(new Date()).split(':').map(Number); const now = h * 60 + m; night = now < <?= $workStart ?> || now >= <?= $workEnd ?>"
                  :class="{ 'is-night': night }">
                <i></i><span x-text="night ? 'Пишите, ответим утром' : 'Пишите, мы онлайн'"><?= $isNight ? 'Пишите, ответим утром' : 'Пишите, мы онлайн' ?></span>
            </span>
            <div class="hdr__icons">
                <a href="<?= $contacts['vk_message'] ?? '' ?>" aria-label="Написать во ВКонтакте" onclick="ym(62605987, 'reachGoal', 'messenger-vk'); return true;"><img src="<?= asset('/assets/icons/vk-colored-bg.webp') ?>" alt="" /></a>
                <a href="<?= $shiftTelegram ?>" aria-label="Написать в Telegram" onclick="ym(62605987, 'reachGoal', 'messenger-telegram'); return true;"><img src="<?= asset('/assets/icons/telegram-colored-bg.svg') ?>" alt="" /></a>
                <a href="<?= $contacts['max'] ?? '' ?>" aria-label="Написать в MAX" onclick="ym(62605987, 'reachGoal', 'messenger-max'); return true;"><img src="<?= asset('/assets/icons/max.svg') ?>" alt="" /></a>
            </div>
        </div>

        <!-- Телефон и почта -->
        <div class="hdr__contacts">
            <a class="hdr__phone" href="tel:<?= $contacts['phone'] ?? '' ?>"><?= $contacts['phone'] ?? '' ?></a>
            <a class="hdr__email" href="mailto:<?= $contacts['email'] ?? '' ?>"><?= $contacts['email'] ?? '' ?></a>
        </div>

        <!-- Кнопка расчёта (низкий приоритет). Скрипт Bitrix должен стоять прямо перед кнопкой -->
        <?= $header['form']['bitrix_code'] ?? '' ?>
        <div class="button hdr__button"><?= $header['button_text'] ?? '' ?></div>
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
</section>