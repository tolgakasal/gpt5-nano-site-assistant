<?php
if (!defined('ABSPATH')) exit;

class GNSA_OpenAI {
    public static function chat($api_key, $model, $system, $user, $temperature = 0.2) {
        $url = 'https://api.openai.com/v1/chat/completions';
        $payload = [
            'model' => $model,
            'messages' => [
                ['role' => 'system', 'content' => $system],
                ['role' => 'user', 'content' => $user],
            ],
            'temperature' => $temperature,
        ];
        $args = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $api_key,
            ],
            'body'    => wp_json_encode($payload),
            'timeout' => 30,
            'method'  => 'POST',
        ];
        $resp = wp_remote_post($url, $args);
        if (is_wp_error($resp)) return $resp;
        $code = wp_remote_retrieve_response_code($resp);
        $body = wp_remote_retrieve_body($resp);
        if ($code < 200 || $code >= 300) {
            return new WP_Error('openai_error', 'OpenAI API error: ' . $code . ' ' . $body);
        }
        $data = json_decode($body, true);
        $answer = $data['choices'][0]['message']['content'] ?? '';
        $usage = isset($data['usage']) ? $data['usage'] : null;
        return ['answer' => $answer, 'usage' => $usage];
    }
}
