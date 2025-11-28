<?php

/**
 * Plugin Name: WordPress Bucket List
 * Description: A plugin to manage bucket list items with custom fields, progress tracking, and frontend display
 * Version: 1.0.0
 * Author: Alex
 * GitHub Plugin URI: langure/wordpress-bucket-list
 * Requires at least: 5.0
 * Requires PHP: 7.2
 * Text Domain: wordpress-bucket-list
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('WBL_VERSION', '1.0.0');
define('WBL_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WBL_PLUGIN_URL', plugin_dir_url(__FILE__));

// Autoload classes
spl_autoload_register(function ($class) {
    $prefix = 'WBL_';
    $base_dir = WBL_PLUGIN_DIR . 'src/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('_', '-', strtolower($relative_class)) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Plugin activation
register_activation_hook(__FILE__, 'wbl_activate');
function wbl_activate()
{
    WBL_Post_Type::register();
    flush_rewrite_rules();
}

// Plugin deactivation
register_deactivation_hook(__FILE__, 'wbl_deactivate');
function wbl_deactivate()
{
    flush_rewrite_rules();
}

// Initialize plugin
add_action('plugins_loaded', 'wbl_init');
function wbl_init()
{
    // Load text domain for translations
    load_plugin_textdomain('wordpress-bucket-list', false, dirname(plugin_basename(__FILE__)) . '/languages');

    // Initialize components
    WBL_Post_Type::init();
    WBL_Meta_Boxes::init();
    WBL_Category_Meta_Fields::init();
    WBL_Post_Meta_Box::init();
    WBL_Shortcode::init();
    WBL_Assets::init();
    WBL_Settings::init();
}
