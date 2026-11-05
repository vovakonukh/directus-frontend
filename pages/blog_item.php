<?php
$items = fetchItems('blog', [
    'fields' => 'id,slug,pagetitle,content,blog_image,date,seo_title,description,faq_blog',
    'filter[slug][_eq]' => $slug,
    'filter[status][_eq]' => 'published',
    'limit' => 1,
]);

if (empty($items)) {
    echo '<section class="page__wrap"><h1>Статья не найдена</h1></section>';
    return;
}

$post = $items[0];

// Оглавление + якоря
function buildContentsAndAnchors($html) {
    if (!preg_match_all('#<h([1-5])>(.*?)</h[1-5]>#', $html, $headers)) {
        return ['contents' => '', 'content' => $html];
    }
    if (count($headers[0]) < 2) {
        return ['contents' => '', 'content' => $html];
    }

    $from = $to = [];
    $depth = 0;
    $start = null;
    $contents = '<ul id="page-contents">';

    foreach ($headers[2] as $i => $header) {
        $header = preg_replace('#\s+#', ' ', trim(rtrim(strip_tags($header), ':!.?;')));
        $anchor = str_replace(' ', '-', $header);
        $link = '<a href="#' . $anchor . '">' . $header . '</a>';
        $level = (int)$headers[1][$i];

        if ($depth > 0) {
            if ($level > $depth) {
                while ($level > $depth) { $contents .= '<ul>'; $depth++; }
            } elseif ($level < $depth) {
                while ($level < $depth) { $contents .= '</ul>'; $depth--; }
            }
        }
        $depth = $level;
        if ($start === null) $start = $depth;

        $contents .= '<li>' . $link . '</li>';

        $from[$i] = $headers[0][$i];
        $to[$i] = '<a name="' . $anchor . '" class="page-contents-link"></a>' . $headers[0][$i];
    }

    for ($i = 0; $i <= ($depth - $start); $i++) {
        $contents .= '</ul>';
    }

    return ['contents' => $contents, 'content' => str_replace($from, $to, $html)];
}

$processed = buildContentsAndAnchors(renderVars($post['content']));

// FAQ статьи (repeater faq_blog): шорткоды {{...}} подставляются и в вопросы, и в ответы
$faq = array_values(array_filter(array_map(fn($f) => [
    'question' => renderVars($f['question'] ?? ''),
    'answer'   => renderVars($f['answer'] ?? ''),
], $post['faq_blog'] ?? []), fn($f) => trim($f['question']) !== '' && trim($f['answer']) !== ''));

// Смотрите также: 3 последние статьи, кроме текущей
$others = fetchItems('blog', [
    'fields' => 'slug,pagetitle,description,blog_image',
    'filter[status][_eq]' => 'published',
    'filter[id][_neq]' => $post['id'],
    'sort' => '-date',
    'limit' => 3,
]);
?>

<section class="page__wrap">
    <div class="blog__content">
        <h1><?= htmlspecialchars($post['pagetitle']) ?></h1>
        <?= $processed['contents'] ?>
        <?= renderBlocks($processed['content']) /* блоки после оглавления: заголовки карточек в него не попадают */ ?>

        <?php if (!empty($faq)): ?>
            <h2>Часто задаваемые вопросы</h2>
            <?php foreach ($faq as $f): ?>
                <?php include 'includes/cards/faq_card.php'; ?>
            <?php endforeach; ?>
            <?php
            $ldFaq = [
                '@context' => 'https://schema.org',
                '@type' => 'FAQPage',
                'mainEntity' => array_map(fn($f) => [
                    '@type' => 'Question',
                    'name' => $f['question'],
                    'acceptedAnswer' => ['@type' => 'Answer', 'text' => trim(strip_tags($f['answer']))],
                ], $faq),
            ];
            ?>
            <script type="application/ld+json"><?= json_encode($ldFaq, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) ?></script>
        <?php endif; ?>
    </div>

    <?php if (!empty($others)): ?>
        <div class="blog__others">
            <h2>Смотрите также</h2>
            <div class="blog_index__collection">
                <?php $article = $post; ?>
                <?php foreach ($others as $post): ?>
                    <?php include 'includes/cards/blog_card.php'; ?>
                <?php endforeach; ?>
                <?php $post = $article; ?>
            </div>
        </div>
    <?php endif; ?>
</section>