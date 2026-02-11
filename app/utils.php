<?php
function e($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function old($key) {
    return $_POST[$key] ?? '';
}