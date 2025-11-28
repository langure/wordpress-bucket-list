<?php

/**
 * Plugin Name: WordPress Bucket List
 * Plugin URI: https://yourwebsite.com/wordpress-bucket-list
 * Description: A beautiful, Apple-inspired bucket list plugin to track your life goals with category-specific details like books, movies, music, and more.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://yourwebsite.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wordpress-bucket-list
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
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
