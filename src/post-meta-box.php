<?php

/**
 * Meta Box for Regular Posts to Link Bucket List Items
 */

if (!defined('ABSPATH')) {
    exit;
}

class WBL_Post_Meta_Box
{

    public static function init()
    {
        add_action('add_meta_boxes', [__CLASS__, 'add_meta_box']);
        add_action('save_post', [__CLASS__, 'save_meta_box'], 10, 2);
        add_filter('the_content', [__CLASS__, 'append_bucket_details'], 20);
        add_action('wp_head', [__CLASS__, 'add_inline_styles']);
    }

    public static function add_meta_box()
    {
        add_meta_box(
            'wbl_bucket_item_selector',
            __('Bucket List Item', 'wordpress-bucket-list'),
            [__CLASS__, 'render_meta_box'],
            'post', // Regular posts
            'side',
            'default'
        );
    }

    public static function render_meta_box($post)
    {
        wp_nonce_field('wbl_post_bucket_item', 'wbl_post_bucket_item_nonce');

        $selected_item = get_post_meta($post->ID, '_wbl_linked_bucket_item', true);

        // Get all bucket items
        $bucket_items = get_posts([
            'post_type' => 'bucket_item',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'orderby' => 'title',
            'order' => 'ASC',
        ]);

?>
        <div class="components-base-control">
            <div class="components-base-control__field">
                <label class="components-base-control__label" for="wbl_linked_bucket_item" style="display: block; margin-bottom: 8px;">
                    <?php _e('Select Item:', 'wordpress-bucket-list'); ?>
                </label>
                <select name="wbl_linked_bucket_item" id="wbl_linked_bucket_item" class="components-select-control__input" style="width: 100%;">
                    <option value=""><?php _e('-- None --', 'wordpress-bucket-list'); ?></option>
                    <?php foreach ($bucket_items as $item) : ?>
                        <option value="<?php echo esc_attr($item->ID); ?>" <?php selected($selected_item, $item->ID); ?>>
                            <?php echo esc_html($item->post_title); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="description" style="margin: 8px 0 0 0;">
                    <?php _e('Technical details will appear at the end of this post.', 'wordpress-bucket-list'); ?>
                </p>
            </div>
        </div>
    <?php
    }

    public static function save_meta_box($post_id, $post)
    {
        // Security checks
        if (!isset($_POST['wbl_post_bucket_item_nonce']) || !wp_verify_nonce($_POST['wbl_post_bucket_item_nonce'], 'wbl_post_bucket_item')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if ($post->post_type !== 'post') {
            return;
        }

        // Save the linked bucket item
        if (isset($_POST['wbl_linked_bucket_item'])) {
            $bucket_item_id = absint($_POST['wbl_linked_bucket_item']);
            if ($bucket_item_id > 0) {
                update_post_meta($post_id, '_wbl_linked_bucket_item', $bucket_item_id);
            } else {
                delete_post_meta($post_id, '_wbl_linked_bucket_item');
            }
        }
    }

    public static function add_inline_styles()
    {
        if (!is_single() || get_post_type() !== 'post') {
            return;
        }

        $bucket_item_id = get_post_meta(get_the_ID(), '_wbl_linked_bucket_item', true);
        if (!$bucket_item_id) {
            return;
        }

    ?>
        <style>
            .wbl-technical-details {
                margin: 50px auto !important;
                max-width: 1000px !important;
                background: #ffffff !important;
                border-radius: 8px !important;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1) !important;
                overflow: hidden !important;
                border: 1px solid #ddd !important;
                display: flex !important;
                flex-direction: row !important;
            }

            .wbl-details-poster {
                flex-shrink: 0 !important;
                width: 300px !important;
                background: #000 !important;
                position: relative !important;
                overflow: hidden !important;
            }

            .wbl-details-poster img {
                width: 100% !important;
                height: 100% !important;
                object-fit: cover !important;
                object-position: center !important;
                display: block !important;
            }

            .wbl-details-content {
                flex: 1 !important;
                padding: 24px 32px !important;
            }

            .wbl-details-content h3 {
                margin: 0 0 20px 0 !important;
                font-size: 28px !important;
                font-weight: 700 !important;
                color: #111 !important;
            }

            .wbl-details-grid {
                display: grid !important;
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 16px 32px !important;
            }

            .wbl-detail-item {
                margin: 0 !important;
                padding: 0 !important;
            }

            .wbl-detail-label {
                font-size: 12px !important;
                font-weight: 600 !important;
                color: #999 !important;
                text-transform: uppercase !important;
                letter-spacing: 0.5px !important;
                margin-bottom: 4px !important;
                display: block !important;
            }

            .wbl-detail-value {
                font-size: 15px !important;
                color: #111 !important;
                line-height: 1.4 !important;
            }

            .wbl-detail-full {
                grid-column: 1 / -1 !important;
            }

            @media (max-width: 768px) {
                .wbl-technical-details {
                    flex-direction: column !important;
                    margin: 30px 0 !important;
                }

                .wbl-details-poster {
                    width: 100% !important;
                    height: 400px !important;
                }

                .wbl-details-grid {
                    grid-template-columns: 1fr !important;
                }
            }
        </style>
    <?php
    }

    public static function append_bucket_details($content)
    {
        // Only on single posts
        if (!is_single() || get_post_type() !== 'post') {
            return $content;
        }

        $bucket_item_id = get_post_meta(get_the_ID(), '_wbl_linked_bucket_item', true);

        if (!$bucket_item_id) {
            return $content;
        }

        // Get item type and data
        $item_type = WBL_Category_Meta_Fields::get_item_type($bucket_item_id);

        if (!$item_type) {
            return $content;
        }

        $item_data = WBL_Category_Meta_Fields::get_category_data($bucket_item_id, $item_type);

        if (empty($item_data)) {
            return $content;
        }

        // Get field configuration
        $all_fields = WBL_Category_Meta_Fields::get_category_fields();
        $field_config = isset($all_fields[$item_type]) ? $all_fields[$item_type] : null;

        if (!$field_config) {
            return $content;
        }

        // Get bucket item title
        $item_title = get_the_title($bucket_item_id);

        // Build the technical details HTML
        ob_start();
    ?>
        <div class="wbl-technical-details">
            <?php if (!empty($item_data['cover_image'])) :
                $image_url = wp_get_attachment_image_url($item_data['cover_image'], 'large');
                if ($image_url) :
            ?>
                    <div class="wbl-details-poster">
                        <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($item_title); ?>" />
                    </div>
            <?php
                endif;
            endif;
            ?>

            <div class="wbl-details-content">
                <h3><?php echo esc_html($item_title); ?></h3>

                <div class="wbl-details-grid">
                    <?php
                    // Display fields in a grid layout
                    foreach ($item_data as $field_key => $value) :
                        if (!empty($value) && $field_key !== 'cover_image') :
                            $field_info = isset($field_config['fields'][$field_key]) ? $field_config['fields'][$field_key] : null;
                            if (!$field_info) continue;

                            // Full width for textarea fields
                            $is_full = ($field_info['type'] === 'textarea');
                    ?>
                            <div class="wbl-detail-item <?php echo $is_full ? 'wbl-detail-full' : ''; ?>">
                                <span class="wbl-detail-label"><?php echo esc_html($field_info['label']); ?></span>
                                <div class="wbl-detail-value">
                                    <?php
                                    if ($field_info['type'] === 'textarea') {
                                        echo nl2br(esc_html($value));
                                    } elseif ($field_info['type'] === 'date' && !empty($value)) {
                                        echo esc_html(date_i18n(get_option('date_format'), strtotime($value)));
                                    } else {
                                        echo esc_html($value);
                                    }
                                    ?>
                                </div>
                            </div>
                    <?php
                        endif;
                    endforeach;
                    ?>
                </div>
            </div>
        </div>
<?php
        $details_html = ob_get_clean();

        return $content . $details_html;
    }
}
