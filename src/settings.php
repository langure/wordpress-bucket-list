<?php

/**
 * Plugin Settings Page
 */

if (!defined('ABSPATH')) {
    exit;
}

class WBL_Settings
{

    public static function init()
    {
        add_action('admin_menu', [__CLASS__, 'add_settings_page']);
        add_action('admin_init', [__CLASS__, 'register_settings']);
        add_action('update_option_wbl_frontend_language', [__CLASS__, 'on_language_change'], 10, 2);
    }

    public static function add_settings_page()
    {
        add_submenu_page(
            'edit.php?post_type=bucket_item',
            __('Bucket List Settings', 'wordpress-bucket-list'),
            __('Settings', 'wordpress-bucket-list'),
            'manage_options',
            'wbl-settings',
            [__CLASS__, 'render_settings_page']
        );
    }

    public static function register_settings()
    {
        register_setting('wbl_settings_group', 'wbl_frontend_language', [
            'type' => 'string',
            'sanitize_callback' => [__CLASS__, 'sanitize_language'],
            'default' => 'auto',
        ]);

        add_settings_section(
            'wbl_general_section',
            __('General Settings', 'wordpress-bucket-list'),
            [__CLASS__, 'general_section_callback'],
            'wbl-settings'
        );

        add_settings_field(
            'wbl_frontend_language',
            __('Frontend Language', 'wordpress-bucket-list'),
            [__CLASS__, 'language_field_callback'],
            'wbl-settings',
            'wbl_general_section'
        );
    }

    public static function sanitize_language($value)
    {
        $allowed = ['auto', 'en', 'es'];
        return in_array($value, $allowed) ? $value : 'auto';
    }

    public static function general_section_callback()
    {
        echo '<p>' . __('Configure how the Bucket List plugin displays on the frontend.', 'wordpress-bucket-list') . '</p>';
    }

    public static function language_field_callback()
    {
        $value = get_option('wbl_frontend_language', 'auto');
?>
        <select name="wbl_frontend_language" id="wbl_frontend_language">
            <option value="auto" <?php selected($value, 'auto'); ?>>
                <?php _e('Auto (Use WordPress Site Language)', 'wordpress-bucket-list'); ?>
            </option>
            <option value="en" <?php selected($value, 'en'); ?>>
                <?php _e('English', 'wordpress-bucket-list'); ?>
            </option>
            <option value="es" <?php selected($value, 'es'); ?>>
                <?php _e('Español (Spanish)', 'wordpress-bucket-list'); ?>
            </option>
        </select>
        <p class="description">
            <?php _e('Force a specific language for the frontend bucket list display, regardless of WordPress site language.', 'wordpress-bucket-list'); ?>
        </p>
    <?php
    }

    public static function on_language_change($old_value, $new_value)
    {
        // Clear any caches when language changes
        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
        }

        // Add admin notice
        set_transient('wbl_language_changed', true, 30);
    }

    public static function render_settings_page()
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        // Show success message
        if (isset($_GET['settings-updated'])) {
            add_settings_error(
                'wbl_messages',
                'wbl_message',
                __('Settings Saved', 'wordpress-bucket-list'),
                'updated'
            );

            // Show cache clear notice
            echo '<div class="notice notice-info is-dismissible">';
            echo '<p><strong>' . __('Important:', 'wordpress-bucket-list') . '</strong> ';
            echo __('Language has been changed. You may need to clear your browser cache or do a hard refresh (Ctrl+F5 or Cmd+Shift+R) to see the changes on the frontend.', 'wordpress-bucket-list');
            echo '</p>';
            echo '</div>';
        }

        settings_errors('wbl_messages');
    ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

            <form action="options.php" method="post">
                <?php
                settings_fields('wbl_settings_group');
                do_settings_sections('wbl-settings');
                submit_button(__('Save Settings', 'wordpress-bucket-list'));
                ?>
            </form>

            <div class="wbl-settings-info" style="margin-top: 40px; padding: 20px; background: #f9f9f9; border-left: 4px solid #2271b1;">
                <h2><?php _e('Shortcode Usage', 'wordpress-bucket-list'); ?></h2>
                <p><?php _e('Use the following shortcode to display your bucket list:', 'wordpress-bucket-list'); ?></p>
                <code style="display: block; padding: 10px; background: white; margin: 10px 0;">[bucket_list]</code>

                <h3><?php _e('Available Attributes:', 'wordpress-bucket-list'); ?></h3>
                <ul style="list-style: disc; margin-left: 20px;">
                    <li><code>category</code> - <?php _e('Filter by category slug (e.g., category="travel")', 'wordpress-bucket-list'); ?></li>
                    <li><code>columns</code> - <?php _e('Number of columns on desktop (1-4, default: 3)', 'wordpress-bucket-list'); ?></li>
                    <li><code>show_filter</code> - <?php _e('Show category filter buttons (yes/no, default: yes)', 'wordpress-bucket-list'); ?></li>
                </ul>

                <h3><?php _e('Examples:', 'wordpress-bucket-list'); ?></h3>
                <code style="display: block; padding: 10px; background: white; margin: 5px 0;">[bucket_list columns="2"]</code>
                <code style="display: block; padding: 10px; background: white; margin: 5px 0;">[bucket_list category="travel" show_filter="no"]</code>

                <h3 style="margin-top: 30px;"><?php _e('Troubleshooting:', 'wordpress-bucket-list'); ?></h3>
                <p><strong><?php _e('Language not changing?', 'wordpress-bucket-list'); ?></strong></p>
                <ul style="list-style: disc; margin-left: 20px;">
                    <li><?php _e('Clear your browser cache', 'wordpress-bucket-list'); ?></li>
                    <li><?php _e('Do a hard refresh: Windows/Linux: Ctrl+F5, Mac: Cmd+Shift+R', 'wordpress-bucket-list'); ?></li>
                    <li><?php _e('If using a caching plugin (WP Super Cache, W3 Total Cache, etc.), clear its cache', 'wordpress-bucket-list'); ?></li>
                </ul>
            </div>
        </div>

        <style>
            .wbl-settings-info h2,
            .wbl-settings-info h3 {
                margin-top: 20px;
                margin-bottom: 10px;
            }

            .wbl-settings-info code {
                background: #f0f0f1;
                padding: 2px 6px;
                border-radius: 3px;
            }
        </style>
<?php
    }

    /**
     * Get the frontend language setting
     */
    public static function get_frontend_language()
    {
        $setting = get_option('wbl_frontend_language', 'auto');

        if ($setting === 'auto') {
            // Use WordPress site language
            $locale = get_locale();
            return strpos($locale, 'es') === 0 ? 'es' : 'en';
        }

        return $setting;
    }
}
