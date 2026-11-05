<div class="article_block">
    <div class="feedback_gallery">
        <?php foreach ($items as $item): ?>
            <?php include __DIR__ . '/../cards/feedback_card.php'; ?>
        <?php endforeach; ?>
    </div>
    <a class="article_block__more" href="/feedback">Все отзывы</a>
</div>
