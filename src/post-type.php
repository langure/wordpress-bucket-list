<?php

/**
 * Custom Post Type Registration
 */

if (!defined('ABSPATH')) {
    exit;
}

class WBL_Post_Type
{

    public static function init()
    {
        add_action('init', [__CLASS__, 'register']);
    }

    public static function register()
    {
        // Register Bucket Item Custom Post Type
        $labels = [
            'name'                  => _x('Bucket List Items', 'Post Type General Name', 'wordpress-bucket-list'),
            'singular_name'         => _x('Bucket List Item', 'Post Type Singular Name', 'wordpress-bucket-list'),
            'menu_name'             => __('Bucket List', 'wordpress-bucket-list'),
            'name_admin_bar'        => __('Bucket Item', 'wordpress-bucket-list'),
            'archives'              => __('Item Archives', 'wordpress-bucket-list'),
            'attributes'            => __('Item Attributes', 'wordpress-bucket-list'),
            'parent_item_colon'     => __('Parent Item:', 'wordpress-bucket-list'),
            'all_items'             => __('All Items', 'wordpress-bucket-list'),
            'add_new_item'          => __('Add New Item', 'wordpress-bucket-list'),
            'add_new'               => __('Add New', 'wordpress-bucket-list'),
            'new_item'              => __('New Item', 'wordpress-bucket-list'),
            'edit_item'             => __('Edit Item', 'wordpress-bucket-list'),
            'update_item'           => __('Update Item', 'wordpress-bucket-list'),
            'view_item'             => __('View Item', 'wordpress-bucket-list'),
            'view_items'            => __('View Items', 'wordpress-bucket-list'),
            'search_items'          => __('Search Item', 'wordpress-bucket-list'),
            'not_found'             => __('Not found', 'wordpress-bucket-list'),
            'not_found_in_trash'    => __('Not found in Trash', 'wordpress-bucket-list'),
        ];

        $args = [
            'label'                 => __('Bucket List Item', 'wordpress-bucket-list'),
            'description'           => __('Personal goals and bucket list items', 'wordpress-bucket-list'),
            'labels'                => $labels,
            'supports'              => ['title', 'thumbnail'],
            'taxonomies'            => ['bucket_category'],
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 20,
            'menu_icon'             => 'dashicons-list-view',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'post',
            'show_in_rest'          => true,
        ];

        register_post_type('bucket_item', $args);

        // Register Bucket Category Taxonomy
        $tax_labels = [
            'name'                       => _x('Categories', 'Taxonomy General Name', 'wordpress-bucket-list'),
            'singular_name'              => _x('Category', 'Taxonomy Singular Name', 'wordpress-bucket-list'),
            'menu_name'                  => __('Categories', 'wordpress-bucket-list'),
            'all_items'                  => __('All Categories', 'wordpress-bucket-list'),
            'parent_item'                => __('Parent Category', 'wordpress-bucket-list'),
            'parent_item_colon'          => __('Parent Category:', 'wordpress-bucket-list'),
            'new_item_name'              => __('New Category Name', 'wordpress-bucket-list'),
            'add_new_item'               => __('Add New Category', 'wordpress-bucket-list'),
            'edit_item'                  => __('Edit Category', 'wordpress-bucket-list'),
            'update_item'                => __('Update Category', 'wordpress-bucket-list'),
            'view_item'                  => __('View Category', 'wordpress-bucket-list'),
            'separate_items_with_commas' => __('Separate categories with commas', 'wordpress-bucket-list'),
            'add_or_remove_items'        => __('Add or remove categories', 'wordpress-bucket-list'),
            'choose_from_most_used'      => __('Choose from the most used', 'wordpress-bucket-list'),
            'popular_items'              => __('Popular Categories', 'wordpress-bucket-list'),
            'search_items'               => __('Search Categories', 'wordpress-bucket-list'),
            'not_found'                  => __('Not Found', 'wordpress-bucket-list'),
        ];

        $tax_args = [
            'labels'                     => $tax_labels,
            'hierarchical'               => true,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
            'show_in_rest'               => true,
        ];

        register_taxonomy('bucket_category', ['bucket_item'], $tax_args);

        // Add default categories on first activation
        self::add_default_categories();
    }

    private static function add_default_categories()
    {
        // English categories
        $default_categories = [
            'Books' => __('Books', 'wordpress-bucket-list'),
            'Travel' => __('Travel', 'wordpress-bucket-list'),
            'Skills' => __('Skills', 'wordpress-bucket-list'),
            'Projects' => __('Projects', 'wordpress-bucket-list'),
            'Experiences' => __('Experiences', 'wordpress-bucket-list'),
            'Health & Fitness' => __('Health & Fitness', 'wordpress-bucket-list')
        ];

        foreach ($default_categories as $slug => $name) {
            if (!term_exists($slug, 'bucket_category')) {
                wp_insert_term($name, 'bucket_category', ['slug' => sanitize_title($slug)]);
            }
        }
    }
}
