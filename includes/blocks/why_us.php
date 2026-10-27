<?php
// Блок «Почему строят с нами» — статичный, в Directus не выносится.
// Числа берутся из шапки сайта ($header['domrf_deals']) и контактов ($contacts['yandex_feedback_amount']).
// Необязательно перед include: $blockClass — доп. классы секции (например, полоса фона страницы)
$domrfDeals = (int) ($header['domrf_deals'] ?? 0) ?: 37;
$yandexReviews = (int) ($contacts['yandex_feedback_amount'] ?? 0) ?: 75;
// text выводится как HTML (нужна ссылка) — подставляются только числа
$reasons = [
    ['icon' => '/assets/icons/home__whywe_1.webp', 'header' => 'Подтверждённый опыт', 'text' => $domrfDeals . ' успешно ' . pluralRu($domrfDeals, ['завершённая сделка', 'завершённые сделки', 'завершённых сделок']) . ' на портале <a href="https://xn--h1aieheg.xn--d1aqf.xn--p1ai/contractor/5053" target="_blank" rel="nofollow noopener">строим.дом.рф</a>'],
    ['icon' => '/assets/icons/home__whywe_2.webp', 'header' => 'Довольные заказчики', 'text' => 'Это подтверждают их хорошие отзывы на Яндекс Картах, накопилось уже ' . $yandexReviews . ' ' . pluralRu($yandexReviews, ['штука', 'штуки', 'штук'])],
    ['icon' => '/assets/icons/home__whywe_3.webp', 'header' => 'Гарантия до 10 лет в договоре', 'text' => 'Гарантийные обязательства прописаны в договоре. Если что-то пойдёт не так, приедем и устраним за свой счёт.'],
    ['icon' => '/assets/icons/home__whywe_4.webp', 'header' => 'Деньги защищены эскроу-счётом', 'text' => 'При строительстве в ипотеку деньги хранятся в банке и переходят к нам только после сдачи дома.'],
    ['icon' => '/assets/icons/home__whywe_5.webp', 'header' => 'Видите стройку, не выезжая на участок', 'text' => 'Каждые 2–3 дня руководитель проекта присылает фотографии со строительной площадки.'],
    ['icon' => '/assets/icons/home__whywe_6.webp', 'header' => 'Индивидуальный проект бесплатно', 'text' => 'Архитектор доработает планировку из каталога или сделает проект с нуля по вашему эскизу. До начала стройки покажем 3D-модель дома.'],
];
?>
<section class="<?= $blockClass ?? '' ?>">
    <h2>Почему строят с нами</h2>
    <ul class="home2__why_list">
        <?php foreach ($reasons as $r): ?>
        <li>
            <img src="<?= asset($r['icon']) ?>" alt="" loading="lazy">
            <h3><?= htmlspecialchars($r['header']) ?></h3>
            <p><?= $r['text'] ?></p>
        </li>
        <?php endforeach; ?>
    </ul>
</section>
