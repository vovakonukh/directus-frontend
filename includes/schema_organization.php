<?php
// Микроразметка компании (JSON-LD) — подключается на главной и в контактах.
// Данные берутся из той же записи $contacts (Directus), что и видимые контакты на сайте.
$ldOrganization = [
    '@context' => 'https://schema.org',
    '@type' => 'HomeAndConstructionBusiness',
    '@id' => $siteUrl . '/#organization',
    'name' => 'Класс Хаус',
    'url' => $siteUrl . '/',
    'logo' => $siteUrl . '/assets/logo/logo-colored.svg',
    'image' => $siteUrl . '/assets/logo/logo-colored.svg',
    'telephone' => $contacts['phone'] ?? '',
    'email' => $contacts['email'] ?? '',
    'address' => [
        '@type' => 'PostalAddress',
        'addressCountry' => 'RU',
        'addressLocality' => 'Санкт-Петербург',
        // В Directus адрес хранится одной строкой — убираем из неё город
        'streetAddress' => preg_replace('/^г\.\s*Санкт-Петербург,\s*/u', '', $contacts['address_spb'] ?? ''),
    ],
    // Ссылки на официальные страницы компании — пустые поля пропускаются
    'sameAs' => array_values(array_filter([
        $contacts['vk_group'] ?? null,
        $contacts['youtube'] ?? null,
        $contacts['telegram_channel'] ?? null,
    ])),
];
?>
<script type="application/ld+json"><?= json_encode($ldOrganization, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) ?></script>
