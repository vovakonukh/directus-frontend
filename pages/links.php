<?php
// Ссылки берутся из коллекции contacts ($contacts и $shiftTelegram получены в index.php)
// Карточки с пустой ссылкой не выводятся
$phone = $contacts['phone'] ?? '';
$email = $contacts['email'] ?? '';

$linkGroups = [
    [
        'title' => 'Связаться с нами',
        'links' => [
            ['type' => 'phone',    'icon' => 'phone-white.svg',    'title' => 'Позвонить',         'text' => $phone, 'url' => $phone ? 'tel:' . preg_replace('/[^\d+]/', '', $phone) : ''],
            ['type' => 'email',    'icon' => 'email-white.svg',    'title' => 'Написать на почту', 'text' => $email, 'url' => $email ? 'mailto:' . $email : ''],
        ]
    ],
    [
        'title' => 'Написать в мессенджер',
        'links' => [
            ['type' => 'telegram', 'icon' => 'telegram-white.svg', 'title' => 'Телеграм',  'text' => 'Написать сообщение', 'url' => $shiftTelegram, 'goal' => 'messenger-telegram'],
            ['type' => 'whatsapp', 'icon' => 'whatsapp-white.svg', 'title' => 'WhatsApp',  'text' => 'Написать сообщение', 'url' => $contacts['whatsapp'] ?? '', 'goal' => 'messenger-whatsapp'],
            ['type' => 'max',      'icon' => 'max.svg',            'title' => 'Макс',      'text' => 'Написать сообщение', 'url' => $contacts['max'] ?? '', 'goal' => 'messenger-max'],
            ['type' => 'vk',       'icon' => 'vk-white.svg',       'title' => 'Вконтакте', 'text' => 'Написать сообщение', 'url' => $contacts['vk_message'] ?? '', 'goal' => 'messenger-vk'],
        ]
    ],
    [
        'title' => 'Мы в соцсетях',
        'links' => [
            ['type' => 'youtube',   'icon' => 'youtube-white.svg',  'title' => 'Youtube',   'text' => 'Видео-обзоры домов', 'url' => $contacts['youtube'] ?? ''],
            ['type' => 'vk',        'icon' => 'vk-white.svg',       'title' => 'Вконтакте', 'text' => 'Группа компании',    'url' => $contacts['vk_group'] ?? ''],
            ['type' => 'telegram',  'icon' => 'telegram-white.svg', 'title' => 'Телеграм',  'text' => 'Канал компании',     'url' => $contacts['telegram_channel'] ?? ''],
            ['type' => 'max',       'icon' => 'max.svg',            'title' => 'Макс',      'text' => 'Канал компании',     'url' => $contacts['max_channel'] ?? ''],
            ['type' => 'instagram', 'icon' => 'insta-white.svg',    'title' => 'Instagram', 'text' => 'Фото со строек',     'url' => $contacts['instagram'] ?? ''],
        ]
    ],
];

$breadcrumbs = [
    ['title' => 'Ссылки']
];
include 'includes/breadcrumbs.php';
?>

<section class="page__wrap">
    <h1><?= htmlspecialchars(!empty($seo['h1']) ? $seo['h1'] : 'Класс Хаус на связи') ?></h1>

    <?php foreach ($linkGroups as $group): ?>
        <div class="links__group">
            <span class="contacts__subheader"><?= htmlspecialchars($group['title']) ?></span>
            <div class="links__collection">
                <?php foreach ($group['links'] as $link): ?>
                    <?php if (empty($link['url'])) continue; ?>
                    <a class="links__card" href="<?= htmlspecialchars($link['url']) ?>"<?php if (!empty($link['goal'])): ?> onclick="ym(62605987, 'reachGoal', '<?= $link['goal'] ?>'); return true;"<?php endif; ?>>
                        <div class="links__card_icon <?= $link['type'] ?>">
                            <img src="/assets/icons/<?= $link['icon'] ?>" alt="" />
                        </div>
                        <div class="links__card_text">
                            <span><?= htmlspecialchars($link['title']) ?></span>
                            <span><?= htmlspecialchars($link['text']) ?></span>
                        </div>
                        <img class="links__card_arrow" src="/assets/icons/arrow-green-right.svg" alt="" />
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</section>
