<?php
if (!defined('ABSPATH')) exit;

class GNSA_Admin {
    public static function init() {
        add_action('admin_menu', [__CLASS__, 'menu']);
        add_action('admin_init', [__CLASS__, 'settings']);
    }

    public static function menu() {
        add_options_page(
            __('GPT-5 Nano Site Assistant Settings', 'gpt5-nano-site-assistant'),
            __('GPT-5 Nano Site Assistant', 'gpt5-nano-site-assistant'),
            'manage_options',
            'gnsa',
            [__CLASS__, 'render']
        );
    }

    public static function settings() {
        register_setting('gnsa_group', 'gnsa_options');

        add_settings_section('gnsa_api', __('API', 'gpt5-nano-site-assistant'), '__return_false', 'gnsa');
        add_settings_field('api_key', __('OpenAI API Key', 'gpt5-nano-site-assistant'), [__CLASS__, 'field_api_key'], 'gnsa', 'gnsa_api');
        add_settings_field('allowed_models', __('Allowed Models (CSV)', 'gpt5-nano-site-assistant'), [__CLASS__, 'field_allowed_models'], 'gnsa', 'gnsa_api');
        add_settings_field('default_model', __('Default Model', 'gpt5-nano-site-assistant'), [__CLASS__, 'field_default_model'], 'gnsa', 'gnsa_api');
        add_settings_field('default_language', __('Default Language', 'gpt5-nano-site-assistant'), [__CLASS__, 'field_default_language'], 'gnsa', 'gnsa_api');

        add_settings_section('gnsa_behavior', __('Behavior', 'gpt5-nano-site-assistant'), '__return_false', 'gnsa');
        add_settings_field('system_prompt', __('System Prompt', 'gpt5-nano-site-assistant'), [__CLASS__, 'field_system_prompt'], 'gnsa', 'gnsa_behavior');
        add_settings_field('max_chars', __('Max Characters (UI)', 'gpt5-nano-site-assistant'), [__CLASS__, 'field_max_chars'], 'gnsa', 'gnsa_behavior');
        add_settings_field('show_sources', __('Show Sources', 'gpt5-nano-site-assistant'), [__CLASS__, 'field_show_sources'], 'gnsa', 'gnsa_behavior');
        add_settings_field('banned_words', __('Banned Words (CSV)', 'gpt5-nano-site-assistant'), [__CLASS__, 'field_banned_words'], 'gnsa', 'gnsa_behavior');

        add_settings_section('gnsa_security', __('Security', 'gpt5-nano-site-assistant'), '__return_false', 'gnsa');
        add_settings_field('rate_limit_count', __('Rate Limit: Requests', 'gpt5-nano-site-assistant'), [__CLASS__, 'field_rate_count'], 'gnsa', 'gnsa_security');
        add_settings_field('rate_limit_window', __('Rate Limit: Window (seconds)', 'gpt5-nano-site-assistant'), [__CLASS__, 'field_rate_window'], 'gnsa', 'gnsa_security');
    }

    public static function render() {
        ?>
        <div class="wrap">
          <h1><?php _e('GPT-5 Nano Site Assistant Settings', 'gpt5-nano-site-assistant'); ?></h1>
          <form method="post" action="options.php">
            <?php settings_fields('gnsa_group'); do_settings_sections('gnsa'); submit_button(); ?>
          </form>
          <p><?php _e('Use the shortcode', 'gpt5-nano-site-assistant'); ?> <code>[gpt5_chat]</code>.</p>
        </div>
        <?php
    }

    // Fields
    public static function field_api_key() {
        $v = esc_attr( gnsa_get_option('api_key') );
        if (defined('GPT5_API_KEY') && GPT5_API_KEY) {
            echo '<input type="text" class="regular-text" value="Constant GPT5_API_KEY is set" disabled />';
            echo '<p class="description">Using GPT5_API_KEY from wp-config.php.</p>';
            return;
        }
        echo '<input type="password" name="gnsa_options[api_key]" value="'.$v.'" class="regular-text" placeholder="sk-..." />';
        echo '<p class="description">You can also set GPT5_API_KEY in wp-config.php (recommended).</p>';
    }
    public static function field_allowed_models() {
        $v = esc_attr( gnsa_get_option('allowed_models', 'gpt-5-nano,gpt-5-mini,gpt-5') );
        echo '<input type="text" name="gnsa_options[allowed_models]" value="'.$v.'" class="regular-text" />';
    }
    public static function field_default_model() {
        $v = esc_attr( gnsa_get_option('default_model', 'gpt-5-nano') );
        echo '<input type="text" name="gnsa_options[default_model]" value="'.$v.'" class="regular-text" />';
    }
    public static function field_default_language() {
        $v = esc_attr( gnsa_get_option('default_language', 'auto') );
        echo '<select name="gnsa_options[default_language]">';
        $opts = ['auto'=>'Auto','tr'=>'Turkish','en'=>'English'];
        foreach($opts as $k=>$label){
            echo '<option value="'.esc_attr($k).'" '.selected($v, $k, false).'>'.esc_html($label).'</option>';
        }
        echo '</select>';
    }
    public static function field_system_prompt() {
        $v = esc_textarea( gnsa_get_option('system_prompt', "You are a site assistant. Answer ONLY based on the provided CONTEXT. If it is not in the context, say you don't know.") );
        echo '<textarea name="gnsa_options[system_prompt]" rows="5" class="large-text code">'.$v.'</textarea>';
    }
    public static function field_max_chars() {
        $v = intval( gnsa_get_option('max_chars', 1200) );
        echo '<input type="number" name="gnsa_options[max_chars]" value="'.$v.'" min="200" max="5000" />';
    }
    public static function field_show_sources() {
        $v = gnsa_get_option('show_sources', '1');
        echo '<label><input type="checkbox" name="gnsa_options[show_sources]" value="1" '.checked($v, '1', false).' /> '.__('Show source links under answers', 'gpt5-nano-site-assistant').'</label>';
    }
    public static function field_banned_words() {
        $v = esc_attr( gnsa_get_option('banned_words', '') );
        echo '<input type="text" name="gnsa_options[banned_words]" value="'.$v.'" class="regular-text" placeholder="comma,separated,words" />';
    }
    public static function field_rate_count() {
        $v = intval( gnsa_get_option('rate_limit_count', 5) );
        echo '<input type="number" name="gnsa_options[rate_limit_count]" value="'.$v.'" min="1" max="50" />';
    }
    public static function field_rate_window() {
        $v = intval( gnsa_get_option('rate_limit_window', 60) );
        echo '<input type="number" name="gnsa_options[rate_limit_window]" value="'.$v.'" min="10" max="600" />';
    }
}
GNSA_Admin::init();
