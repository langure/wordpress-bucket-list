<?php

/**
 * Meta Boxes for Bucket List Items
 */

if (!defined('ABSPATH')) {
    exit;
}

class WBL_Meta_Boxes
{

    public static function init()
    {
        add_action('add_meta_boxes', [__CLASS__, 'add_meta_boxes']);
        add_action('save_post_bucket_item', [__CLASS__, 'save_meta_boxes'], 10, 2);
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_admin_scripts']);
    }

    public static function add_meta_boxes()
    {
        add_meta_box(
            'wbl_details',
            __('Bucket List Details', 'wordpress-bucket-list'),
            [__CLASS__, 'render_details_meta_box'],
            'bucket_item',
            'normal',
            'high'
        );
    }

    public static function render_details_meta_box($post)
    {
        // Add nonce for security
        wp_nonce_field('wbl_save_meta_box', 'wbl_meta_box_nonce');

        // Get current values
        $description = get_post_meta($post->ID, '_wbl_description', true);
        $completion = get_post_meta($post->ID, '_wbl_completion_percentage', true);
        $related_post = get_post_meta($post->ID, '_wbl_related_post_link', true);
        $external_link = get_post_meta($post->ID, '_wbl_external_resource_link', true);

?>
        <div class="wbl-meta-box-wrapper">
            <p>
                <label for="wbl_description"><strong><?php _e('Description:', 'wordpress-bucket-list'); ?></strong></label><br>
                <textarea
                    id="wbl_description"
                    name="wbl_description"
                    rows="5"
                    style="width: 100%;"
                    class="large-text"><?php echo esc_textarea($description); ?></textarea>
            </p>

            <p>
                <label for="wbl_completion_percentage"><strong><?php _e('Completion Percentage:', 'wordpress-bucket-list'); ?></strong></label><br>
                <input
                    type="number"
                    id="wbl_completion_percentage"
                    name="wbl_completion_percentage"
                    value="<?php echo esc_attr($completion); ?>"
                    min="0"
                    max="100"
                    step="1"
                    style="width: 100px;" /> %
                <span class="description"><?php _e('Enter a value between 0 and 100', 'wordpress-bucket-list'); ?></span>
            </p>

            <p>
                <label for="wbl_related_post_link"><strong><?php _e('Related Post:', 'wordpress-bucket-list'); ?></strong></label><br>
                <select name="wbl_related_post_link" id="wbl_related_post_link" style="width: 100%; max-width: 400px;">
                    <option value=""><?php _e('-- Select a Post --', 'wordpress-bucket-list'); ?></option>
                    <?php
                    $posts = get_posts([
                        'post_type' => 'post',
                        'posts_per_page' => -1,
                        'orderby' => 'title',
                        'order' => 'ASC',
                        'post_status' => 'publish',
                    ]);

                    foreach ($posts as $p) {
                        printf(
                            '<option value="%d" %s>%s</option>',
                            $p->ID,
                            selected($related_post, $p->ID, false),
                            esc_html($p->post_title)
                        );
                    }
                    ?>
                </select>
                <span class="description"><?php _e('Link to a related blog post', 'wordpress-bucket-list'); ?></span>
            </p>

            <p>
                <label for="wbl_external_resource_link"><strong><?php _e('External Resource Link:', 'wordpress-bucket-list'); ?></strong></label><br>
                <input
                    type="url"
                    id="wbl_external_resource_link"
                    name="wbl_external_resource_link"
                    value="<?php echo esc_url($external_link); ?>"
                    class="large-text"
                    placeholder="https://example.com" />
                <span class="description"><?php _e('Enter a full URL with http:// or https://', 'wordpress-bucket-list'); ?></span>
            </p>
        </div>

        <style>
            .wbl-meta-box-wrapper p {
                margin-bottom: 20px;
            }

            .wbl-meta-box-wrapper .description {
                display: block;
                margin-top: 5px;
                font-style: italic;
                color: #666;
            }
        </style>
<?php
    }

    public static function save_meta_boxes($post_id, $post)
    {
        // Security checks
        if (!isset($_POST['wbl_meta_box_nonce']) || !wp_verify_nonce($_POST['wbl_meta_box_nonce'], 'wbl_save_meta_box')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Save description
        if (isset($_POST['wbl_description'])) {
            update_post_meta($post_id, '_wbl_description', sanitize_textarea_field($_POST['wbl_description']));
        }

        // Save completion percentage
        if (isset($_POST['wbl_completion_percentage'])) {
            $completion = intval($_POST['wbl_completion_percentage']);
            $completion = max(0, min(100, $completion)); // Ensure 0-100 range
            update_post_meta($post_id, '_wbl_completion_percentage', $completion);
        }

        // Save related post link
        if (isset($_POST['wbl_related_post_link'])) {
            $related_post = absint($_POST['wbl_related_post_link']);
            update_post_meta($post_id, '_wbl_related_post_link', $related_post);
        }

        // Save external resource link
        if (isset($_POST['wbl_external_resource_link'])) {
            update_post_meta($post_id, '_wbl_external_resource_link', esc_url_raw($_POST['wbl_external_resource_link']));
        }
    }

    public static function enqueue_admin_scripts($hook)
    {
        global $post_type;

        if ('bucket_item' !== $post_type) {
            return;
        }

        // Enqueue media uploader for featured images
        wp_enqueue_media();

        // Add confirmation dialogs for destructive actions
        if (in_array($hook, ['post.php', 'edit.php'])) {
            wp_add_inline_script('jquery', "
                jQuery(document).ready(function($) {
                    // Confirm delete on edit page
                    $('.submitdelete').on('click', function(e) {
                        if (!confirm('" . esc_js(__('Are you sure you want to delete this bucket list item?', 'wordpress-bucket-list')) . "')) {
                            e.preventDefault();
                            return false;
                        }
                    });
                    
                    // Confirm bulk delete
                    $('select[name=\"action\"], select[name=\"action2\"]').on('change', function() {
                        if ($(this).val() === 'trash') {
                            $(this).closest('form').on('submit', function(e) {
                                var checkedItems = $('input[name=\"post[]\"]:checked').length;
                                if (checkedItems > 0) {
                                    if (!confirm('" . esc_js(__('Are you sure you want to move the selected items to trash?', 'wordpress-bucket-list')) . "')) {
                                        e.preventDefault();
                                        return false;
                                    }
                                }
                            });
                        }
                    });
                });
            ");
        }
    }
}
