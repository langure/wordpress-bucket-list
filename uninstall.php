<?php

/**
 * Uninstall Script
 * Cleans up all plugin data when deleted
 */

// If uninstall not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete all bucket_item posts
$bucket_items = get_posts([
    'post_type' => 'bucket_item',
    'numberposts' => -1,
    'post_status' => 'any',
]);

foreach ($bucket_items as $item) {
    wp_delete_post($item->ID, true);
}

// Delete taxonomy terms
$terms = get_terms([
    'taxonomy' => 'bucket_category',
    'hide_empty' => false,
]);

foreach ($terms as $term) {
    wp_delete_term($term->term_id, 'bucket_category');
}

// Delete options
delete_option('wbl_frontend_language');
delete_option('wbl_default_columns');
delete_option('wbl_default_per_page');

// Delete transients
global $wpdb;
$wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_wbl_%'");
$wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_timeout_wbl_%'");
