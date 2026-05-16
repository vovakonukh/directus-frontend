<?php
$items = fetchItems('blog', [
    'fields' => 'id,slug,pagetitle,content,blog_image,date,seo_title,description',
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

$processed = buildContentsAndAnchors($post['content']);

// Другие статьи
$others = fetchItems('blog', [
    'fields' => 'slug,pagetitle,blog_image',
    'filter[status][_eq]' => 'published',
    'filter[id][_neq]' => $post['id'],
    'sort' => '-date',
    'limit' => 6,
]);
?>

<section class="page__wrap">
    <div class="blog__content">
        <h1><?= htmlspecialchars($post['pagetitle']) ?></h1>
        <?= $processed['contents'] ?>
        <?= $processed['content'] ?>
    </div>

    <?php if (!empty($others)): ?>
        <div class="blog__other_box">
            <span>Смотрите также:</span>
            <?php foreach ($others as $o): ?>
                <a href="/blog/<?= htmlspecialchars($o['slug']) ?>" class="blog__other_item">
                    <?php if (!empty($o['blog_image'])): ?>
                        <img src="<?= getAssetUrl($o['blog_image']) ?>" alt="<?= htmlspecialchars($o['pagetitle']) ?>" />
                    <?php endif; ?>
                    <?= htmlspecialchars($o['pagetitle']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>