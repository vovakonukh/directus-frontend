<a class="blog_index__card" href="/blog/<?= htmlspecialchars($post['slug']) ?>">
    <span class="blog_index__card_image">
        <?php if (!empty($post['blog_image'])): ?>
            <img src="<?= getAssetUrl($post['blog_image']) ?>" alt="<?= htmlspecialchars($post['pagetitle']) ?>" loading="lazy" />
        <?php endif; ?>
    </span>
    <span class="blog_index__card_body">
        <span class="blog_index__card_header"><?= htmlspecialchars($post['pagetitle']) ?></span>
        <span class="blog_index__card_more">Читать статью</span>
    </span>
</a>
