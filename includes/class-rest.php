<?php
if (!defined('ABSPATH')) exit;

class GNSA_REST {
    public static function init() {
        add_action('rest_api_init', [__CLASS__, 'routes']);
    }
    public static function routes() {
        register_rest_route('gnsa/v1', '/ask', [
            'methods'  => 'POST',
            'callback' => [__CLASS__, 'handle'],
            'permission_callback' => function() { return true; }
        ]);
    }

    public static function handle(WP_REST_Request $req) {
        $nonce = $req->get_header('X-WP-Nonce');
        if (!wp_verify_nonce($nonce, 'wp_rest')) {
            return new WP_REST_Response(['message' => __('Invalid nonce', 'gpt5-nano-site-assistant')], 403);
        }

        $rl = GNSA_Security::rate_limit_check();
        if (is_wp_error($rl)) {
            return new WP_REST_Response(['message' => $rl->get_error_message()], $rl->get_error_data()['status']);
        }

        $question = isset($req['question']) ? gnsa_sanitize_string($req['question'], 800) : '';
        if (!$question) return new WP_REST_Response(['message' => __('Empty question', 'gpt5-nano-site-assistant')], 400);

        $model = gnsa_sanitize_string( $req->get_param('model'), 100 );
        $language = gnsa_sanitize_string( $req->get_param('language'), 10 );
        $maxchars = intval( $req->get_param('maxchars') );
        $showsources = filter_var( $req->get_param('showsources'), FILTER_VALIDATE_BOOLEAN );

        $api_key = defined('GPT5_API_KEY') && GPT5_API_KEY ? GPT5_API_KEY : gnsa_get_option('api_key');
        if (!$api_key) return new WP_REST_Response(['message' => __('Missing API key', 'gpt5-nano-site-assistant')], 500);

        $allowed = gnsa_get_allowed_models();
        if (!$model) $model = gnsa_get_option('default_model', 'gpt-5-nano');
        if (!in_array($model, $allowed, true)) {
            return new WP_REST_Response(['message' => __('Model not allowed', 'gpt5-nano-site-assistant')], 400);
        }

        if (!$language) $language = gnsa_get_option('default_language', 'auto');
        if (!$maxchars) $maxchars = intval( gnsa_get_option('max_chars', 1200) );
        $system = gnsa_get_option('system_prompt', "You are a site assistant. Answer ONLY based on the provided CONTEXT. If it is not in the context, say you don't know.");

        $found = GNSA_Search::context_from_query($question, 6);
        $context = $found['context'];
        $sources = $found['sources'];

        if ($language && $language !== 'auto') {
            $system .= "\n". "Always answer in the following language: " . strtoupper($language) . ".";
        } else {
            $system .= "\n". "Answer in the same language the user used.";
        }

        $system .= "\n\nCONTEXT:\n" . $context;

        $chat = GNSA_OpenAI::chat($api_key, $model, $system, $question, 0.2);
        if (is_wp_error($chat)) {
            return new WP_REST_Response(['message' => $chat->get_error_message()], 500);
        }
        $answer = isset($chat['answer']) ? $chat['answer'] : '';

        $answer = GNSA_Security::mask_banned_words($answer);
        if ($maxchars > 0 && mb_strlen($answer) > $maxchars) {
            $answer = mb_substr($answer, 0, $maxchars) . '…';
        }

        $resp = ['answer' => $answer];
        if ($showsources && !empty($sources)) {
            $resp['sources'] = $sources;
        }
        if (!empty($chat['usage'])) {
            $resp['usage'] = $chat['usage'];
        }

        return new WP_REST_Response($resp, 200);
    }
}
GNSA_REST::init();
