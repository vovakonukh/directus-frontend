<details class="faq__card">
    <summary class="faq__header">
        <span class="faq__question"><?= htmlspecialchars($f['question']) ?></span>
        <span class="faq__icon" aria-hidden="true"></span>
    </summary>
    <div class="faq__answer">
        <p><?= nl2br(htmlspecialchars($f['answer'] ?? '')) ?></p>
    </div>
</details>
