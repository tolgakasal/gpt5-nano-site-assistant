<?php
if (!defined('ABSPATH')) exit;

function gnsa_get_option($key, $default = '') {
    $opts = get_option('gnsa_options', []);
    return isset($opts[$key]) ? $opts[$key] : $default;
}

function gnsa_get_allowed_models() {
    $raw = gnsa_get_option('allowed_models', 'gpt-5-nano,gpt-5-mini,gpt-5');
    $arr = array_filter(array_map('trim', explode(',', $raw)));
    return $arr ?: ['gpt-5-nano'];
}

function gnsa_sanitize_string($str, $max_len = 2000) {
    $str = wp_strip_all_tags( (string)$str );
    $str = preg_replace('/\s+/', ' ', $str);
    if (mb_strlen($str) > $max_len) $str = mb_substr($str, 0, $max_len);
    return trim($str);
}
