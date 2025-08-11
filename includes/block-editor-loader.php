<?php
if (!defined('ABSPATH')) exit;

add_action('enqueue_block_editor_assets', function(){
    wp_register_script('gnsa-block-edit', plugins_url('blocks/chat/edit.js', __DIR__), ['wp-blocks','wp-element','wp-components','wp-i18n','wp-editor','wp-blockEditor'], GNSA_VER, true);
    wp_register_style('gnsa-block-style', plugins_url('blocks/chat/style.css', __DIR__), [], GNSA_VER);
});
