<?php
// Кнопка «В избранное». Перед include задать:
// $favType — 'projects' или 'finished', $favId — id записи,
// $favMod — модификатор вида: 'card' (на картинке карточки) или 'round' (рядом с кнопкой заявки)
$favId = (int) $favId;
?>
<button type="button" class="fav_btn fav_btn--<?= $favMod ?>"
        x-data
        :class="{ 'is-active': $store.fav.has('<?= $favType ?>', <?= $favId ?>) }"
        :aria-pressed="$store.fav.has('<?= $favType ?>', <?= $favId ?>)"
        :title="$store.fav.has('<?= $favType ?>', <?= $favId ?>) ? 'Убрать из избранного' : 'Добавить в избранное'"
        @click="$store.fav.toggle('<?= $favType ?>', <?= $favId ?>)"
        aria-label="В избранное">
    <svg viewBox="0 0.75 24 24" aria-hidden="true"><path d="M12 21s-7.5-4.6-9.6-9.3C.9 8.4 3 4.5 6.7 4.5c2.1 0 3.5 1.1 4.3 2.4l1 1.5 1-1.5c.8-1.3 2.2-2.4 4.3-2.4 3.7 0 5.8 3.9 4.3 7.2C19.5 16.4 12 21 12 21z"/></svg>
</button>
