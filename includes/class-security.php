<?php
if (!defined('ABSPATH')) exit;

class GNSA_Security {
    public static function rate_limit_check() {
        $count = intval( gnsa_get_option('rate_limit_count', 5) );
        $window = intval( gnsa_get_option('rate_limit_window', 60) );
        if ($count <= 0 || $window <= 0) return true;

        $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
        $key = 'gnsa_rate_' . md5($ip);
        $req = get_transient($key);
        if ($req === false) {
            set_transient($key, 1, $window);
            return true;
        }
        if ($req < $count) {
            set_transient($key, $req + 1, $window);
            return true;
        }
        return new WP_Error('too_many_requests', __('Too many requests. Please slow down.', 'gpt5-nano-site-assistant'), ['status' => 429]);
    }

    public static function mask_banned_words($text) {
        $csv = gnsa_get_option('banned_words', '');
        $words = array_filter(array_map('trim', explode(',', $csv)));
        if (!$words) return $text;
        foreach ($words as $w) {
            $pattern = '/' . preg_quote($w, '/') . '/iu';
            $text = preg_replace($pattern, '█', $text);
        }
        return $text;
    }
}
