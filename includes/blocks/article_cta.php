<?php
// Скрипт click-формы Bitrix вешает открытие на элемент сразу после себя.
// Поэтому код формы со скрытой кнопкой выводим один раз на страницу, а кнопки CTA кликают по ней.
$ctaTrigger = 'cta_form_' . $formId . '_trigger';
$GLOBALS['ctaFormsPrinted'] ??= [];
?>
<div class="article_block article_cta">
    <p class="article_cta__text"><?= htmlspecialchars($text) ?></p>
    <button type="button" class="button article_cta__button" onclick="document.getElementById('<?= $ctaTrigger ?>').click()"><?= htmlspecialchars($button) ?></button>
    <?php if (empty($GLOBALS['ctaFormsPrinted'][$formId])): $GLOBALS['ctaFormsPrinted'][$formId] = true; ?>
        <?= $formCode ?>
        <button type="button" id="<?= $ctaTrigger ?>" hidden></button>
    <?php endif; ?>
</div>
