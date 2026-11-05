<?php
$items = fetchItems('blog', [
    'fields' => 'id,slug,pagetitle,content,blog_image,date,seo_title,description,faq_blog,topic,'
        . 'similar_articles.related_blog_id.slug,similar_articles.related_blog_id.pagetitle,similar_articles.related_blog_id.blog_image,similar_articles.related_blog_id.status',
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

// Смотрите также:
// 1) статьи из поля similar_articles (только опубликованные);
// 2) иначе 3 последние статьи той же темы (topic), недостающие добираются последними из всех;
// 3) если тема не указана — 3 последние статьи
$others = array_values(array_filter(
    array_column($post['similar_articles'] ?? [], 'related_blog_id'),
    fn($o) => is_array($o) && ($o['status'] ?? '') === 'published'
));
if (!$others) {
    $othersQuery = [
        'fields' => 'id,slug,pagetitle,blog_image',
        'filter[status][_eq]' => 'published',
        'filter[id][_neq]' => $post['id'],
        'sort' => '-date',
        'limit' => 3,
    ];
    if (!empty($post['topic'])) {
        $others = fetchItems('blog', $othersQuery + ['filter[topic][_eq]' => $post['topic']]) ?: [];
    }
    if (count($others) < 3) {
        $exclude = implode(',', array_merge([$post['id']], array_column($others, 'id')));
        $others = array_merge($others, fetchItems('blog', [
            'filter[id][_nin]' => $exclude,
            'limit' => 3 - count($others),
        ] + $othersQuery) ?: []);
    }
}
?>

<section class="page__wrap">
    <div class="blog__content">
        <h1><?= htmlspecialchars($post['pagetitle']) ?></h1>
        <?php if ($processed['contents']): ?>
            <?php /* на мобильных подразделы (h3) свёрнуты; в HTML они есть всегда — для поисковиков */ ?>
            <div class="page_toc" x-data="{ open: false }" :class="{ 'page_toc--open': open }">
                <?= $processed['contents'] ?>
                <?php if (strpos($processed['contents'], '<ul>') !== false): ?>
                    <button type="button" class="page_toc__toggle" @click="open = !open" x-text="open ? 'Скрыть подразделы' : 'Показать подразделы'">Показать подразделы</button>
                <?php endif; ?>
            </div>
        <?php endif; ?>
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