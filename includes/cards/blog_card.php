<a class="blog_index__card" href="/blog/<?= htmlspecialchars($post['slug']) ?>">
    <?php if (!empty($post['blog_image'])): ?>
        <img src="<?= getAssetUrl($post['blog_image']) ?>" alt="<?= htmlspecialchars($post['pagetitle']) ?>" loading="lazy" />
    <?php endif; ?>
    <span class="blog_index__card_header"><?= htmlspecialchars($post['pagetitle']) ?></span>
    <?php if (!empty($post['description'])): ?>
        <span class="blog_index__card_descr"><?= htmlspecialchars($post['description']) ?></span>
    <?php endif; ?>
</a>
