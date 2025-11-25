<?php
// sanitize.php

// Remove espaços extras + impede HTML e JS
function sanitize_string($value) {
    return trim(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
}

// Garante inteiro seguro
function sanitize_int($value) {
    return intval($value);
}

// Faz uma sanitização segura em arrays grandes (ex.: $_POST)
function sanitize_array($arr) {
    $clean = [];
    foreach ($arr as $key => $value) {
        if (is_numeric($value)) {
            $clean[$key] = sanitize_int($value);
        } else {
            $clean[$key] = sanitize_string($value);
        }
    }
    return $clean;
}
