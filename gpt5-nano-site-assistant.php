<?php
/*
Plugin Name: GPT-5 Nano Site Assistant
Description: Site-only content chat assistant using GPT-5-nano. Gutenberg block + shortcode with Bootstrap UI.
Version: 1.0.0
Author: ChatGPT-5 & tolgakasal
Text Domain: gpt5-nano-site-assistant
*/

if (!defined('ABSPATH')) exit;

define('GNSA_VER', '1.0.0');
define('GNSA_PATH', plugin_dir_path(__FILE__));
define('GNSA_URL', plugin_dir_url(__FILE__));

// i18n
add_action('init', function () {
    load_plugin_textdomain('gpt5-nano-site-assistant', false, dirname(plugin_basename(__FILE__)) . '/languages');
});

// Includes
require_once GNSA_PATH . 'includes/helpers.php';
require_once GNSA_PATH . 'includes/class-admin.php';
require_once GNSA_PATH . 'includes/class-security.php';
require_once GNSA_PATH . 'includes/class-search.php';
require_once GNSA_PATH . 'includes/class-openai.php';
require_once GNSA_PATH . 'includes/class-rest.php';
require_once GNSA_PATH . 'includes/block-editor-loader.php';

// Assets (Bootstrap via CDN + plugin assets)
add_action('wp_enqueue_scripts', function(){
    if (apply_filters('gnsa_enqueue_bootstrap', true)) {
        wp_enqueue_style('gnsa-bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css', [], '5.3.3');
        wp_enqueue_script('gnsa-bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', [], '5.3.3', true);
    }
    wp_enqueue_style('gnsa-chat-css', GNSA_URL . 'assets/css/chat.css', [], GNSA_VER);
    wp_enqueue_script('gnsa-chat-js', GNSA_URL . 'assets/js/chat.js', ['jquery'], GNSA_VER, true);
    wp_localize_script('gnsa-chat-js', 'GNSA', [
        'restUrl' => esc_url_raw( rest_url('gnsa/v1/ask') ),
        'nonce'   => wp_create_nonce('wp_rest'),
        'i18n'    => [
            'you' => __('You', 'gpt5-nano-site-assistant'),
            'assistant' => __('Assistant', 'gpt5-nano-site-assistant'),
            'typing' => __('Typing…', 'gpt5-nano-site-assistant'),
            'send' => __('Send', 'gpt5-nano-site-assistant'),
        ]
    ]);
});

// Shortcode
add_shortcode('gpt5_chat', function($atts){
    $atts = shortcode_atts([
        'model' => '',
        'language' => 'auto',
        'maxchars' => 1200,
        'showsources' => 'true',
    ], $atts, 'gpt5_chat');
    return gnsa_render_widget($atts);
});

/**
 * Render widget HTML for both shortcode and block.
 */
function gnsa_render_widget($atts = []) {
    $defaults = [
        'model' => '',
        'language' => 'auto',
        'maxchars' => 1200,
        'showsources' => 'true',
    ];
    $args = wp_parse_args($atts, $defaults);
    $widget_id = 'gnsa-' . wp_generate_uuid4();
    ob_start(); ?>
<div id="<?php echo esc_attr($widget_id); ?>" class="gnsa card shadow-sm" role="region" aria-label="<?php esc_attr_e('Chat window', 'gpt5-nano-site-assistant'); ?>">
  <div class="card-header d-flex align-items-center justify-content-between">
    <strong>GPT-5 Nano Site Assistant</strong>
    <small class="text-muted"><?php echo esc_html( strtoupper($args['language']) ); ?></small>
  </div>
  <div class="card-body gnsa-log" id="<?php echo esc_attr($widget_id); ?>-log" aria-live="polite" aria-atomic="false"></div>
  <div class="card-footer">
    <form class="gnsa-form d-flex gap-2"
          data-model="<?php echo esc_attr($args['model']); ?>"
          data-language="<?php echo esc_attr($args['language']); ?>"
          data-maxchars="<?php echo esc_attr($args['maxchars']); ?>"
          data-showsources="<?php echo esc_attr($args['showsources']); ?>">
      <input type="text" class="form-control gnsa-input" placeholder="<?php esc_attr_e('Type your question…', 'gpt5-nano-site-assistant'); ?>" aria-label="<?php esc_attr_e('Message to send', 'gpt5-nano-site-assistant'); ?>">
      <button type="submit" class="btn btn-primary gnsa-send"><?php esc_html_e('Send', 'gpt5-nano-site-assistant'); ?></button>
    </form>
  </div>
</div>
<?php
    return ob_get_clean();
}

// Gutenberg block registration (dynamic via render callback)
add_action('init', function(){
    $dir = GNSA_PATH . 'blocks/chat';
    if (file_exists($dir . '/block.json')) {
        register_block_type($dir, [
            'render_callback' => function($attributes, $content){
                $atts = [
                    'model' => isset($attributes['model']) ? $attributes['model'] : '',
                    'language' => isset($attributes['language']) ? $attributes['language'] : 'auto',
                    'maxchars' => isset($attributes['maxChars']) ? intval($attributes['maxChars']) : 1200,
                    'showsources' => !empty($attributes['showSources']) ? 'true' : 'false',
                ];
                return gnsa_render_widget($atts);
            }
        ]);
    }
});
