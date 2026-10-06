<?php if (!empty($seo['seo_text'])): ?>
    <div class="seo_text_wrap" x-data="{ open: false }" x-init="if ($refs.text.scrollHeight <= $refs.text.clientHeight) open = true">
        <div class="seo_text seo_text--collapsed" x-ref="text" :class="{ 'seo_text--collapsed': !open }"><?= $seo['seo_text'] ?></div>
        <button type="button" class="seo_text__toggle" x-show="!open" @click="open = true">Читать далее</button>
    </div>
<?php endif; ?>
