<?php

/**
 * Assets Management
 */

if (!defined('ABSPATH')) {
    exit;
}

class WBL_Assets
{

    public static function init()
    {
        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_frontend_assets']);
    }

    public static function enqueue_frontend_assets()
    {
        // Only enqueue on pages that have the shortcode
        global $post;
        if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'bucket_list')) {

            // Get file modification times for cache busting
            $css_file = WBL_PLUGIN_DIR . 'assets/css/frontend.css';
            $js_file = WBL_PLUGIN_DIR . 'assets/js/frontend.js';

            $css_version = file_exists($css_file) ? filemtime($css_file) : WBL_VERSION;
            $js_version = file_exists($js_file) ? filemtime($js_file) : WBL_VERSION;

            wp_enqueue_style(
                'wbl-frontend',
                WBL_PLUGIN_URL . 'assets/css/frontend.css',
                [],
                $css_version
            );

            wp_enqueue_script(
                'wbl-frontend',
                WBL_PLUGIN_URL . 'assets/js/frontend.js',
                ['jquery'],
                $js_version,
                true
            );

            // Add language setting to JavaScript
            $frontend_lang = WBL_Settings::get_frontend_language();

            wp_localize_script('wbl-frontend', 'wblData', [
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('wbl_frontend_nonce'),
                'language' => $frontend_lang,
            ]);
        }
    }
}
