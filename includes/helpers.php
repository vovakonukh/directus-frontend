<?php
function formatDimension($value) {
    $num = floatval($value);
    return ($num == intval($num))
        ? intval($num)
        : str_replace('.', ',', $num);
}