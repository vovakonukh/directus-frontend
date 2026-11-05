<div class="article_block article_block--wide">
    <div class="projects__collection">
        <?php foreach ($items as $item): ?>
            <?php include __DIR__ . '/../cards/project_card.php'; ?>
        <?php endforeach; ?>
    </div>
    <a class="article_block__more" href="/projects">Все проекты</a>
</div>
