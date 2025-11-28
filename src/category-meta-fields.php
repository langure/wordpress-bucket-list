<?php

/**
 * Category-Specific Meta Fields Handler
 */

if (!defined('ABSPATH')) {
    exit;
}

class WBL_Category_Meta_Fields
{

    public static function init()
    {
        add_action('add_meta_boxes', [__CLASS__, 'add_category_meta_boxes']);
        // Try multiple hooks to ensure save function runs
        add_action('save_post', [__CLASS__, 'save_category_meta_boxes'], 10, 2);
        add_action('save_post_bucket_item', [__CLASS__, 'save_category_meta_boxes'], 10, 2);
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_admin_scripts']);
    }

    /**
     * Define field configurations for each category
     */
    public static function get_category_fields()
    {
        return [
            'music-album' => [
                'label' => __('Music Album Details', 'wordpress-bucket-list'),
                'fields' => [
                    'cover_image' => ['label' => __('Album Cover', 'wordpress-bucket-list'), 'type' => 'image'],
                    'artist' => ['label' => __('Artist/Band', 'wordpress-bucket-list'), 'type' => 'text'],
                    'release_year' => ['label' => __('Release Year', 'wordpress-bucket-list'), 'type' => 'number'],
                    'genre' => ['label' => __('Genre', 'wordpress-bucket-list'), 'type' => 'text'],
                    'label' => ['label' => __('Record Label', 'wordpress-bucket-list'), 'type' => 'text'],
                    'tracks' => ['label' => __('Track List (one per line)', 'wordpress-bucket-list'), 'type' => 'textarea'],
                    'rating' => ['label' => __('Your Rating (1-10)', 'wordpress-bucket-list'), 'type' => 'number', 'min' => 1, 'max' => 10],
                    'favorite_track' => ['label' => __('Favorite Track', 'wordpress-bucket-list'), 'type' => 'text'],
                    'listening_date' => ['label' => __('Date Listened', 'wordpress-bucket-list'), 'type' => 'date'],
                ],
            ],
            'book' => [
                'label' => __('Book Details', 'wordpress-bucket-list'),
                'fields' => [
                    'cover_image' => ['label' => __('Book Cover', 'wordpress-bucket-list'), 'type' => 'image'],
                    'author' => ['label' => __('Author', 'wordpress-bucket-list'), 'type' => 'text'],
                    'isbn' => ['label' => __('ISBN', 'wordpress-bucket-list'), 'type' => 'text'],
                    'pages' => ['label' => __('Number of Pages', 'wordpress-bucket-list'), 'type' => 'number'],
                    'publisher' => ['label' => __('Publisher', 'wordpress-bucket-list'), 'type' => 'text'],
                    'publication_year' => ['label' => __('Publication Year', 'wordpress-bucket-list'), 'type' => 'number'],
                    'genre' => ['label' => __('Genre', 'wordpress-bucket-list'), 'type' => 'text'],
                    'rating' => ['label' => __('Your Rating (1-10)', 'wordpress-bucket-list'), 'type' => 'number', 'min' => 1, 'max' => 10],
                    'reading_started' => ['label' => __('Started Reading', 'wordpress-bucket-list'), 'type' => 'date'],
                    'reading_finished' => ['label' => __('Finished Reading', 'wordpress-bucket-list'), 'type' => 'date'],
                    'favorite_quote' => ['label' => __('Favorite Quote', 'wordpress-bucket-list'), 'type' => 'textarea'],
                ],
            ],
            'movie' => [
                'label' => __('Movie Details', 'wordpress-bucket-list'),
                'fields' => [
                    'cover_image' => ['label' => __('Movie Poster', 'wordpress-bucket-list'), 'type' => 'image'],
                    'director' => ['label' => __('Director', 'wordpress-bucket-list'), 'type' => 'text'],
                    'cast' => ['label' => __('Main Cast (comma separated)', 'wordpress-bucket-list'), 'type' => 'text'],
                    'release_year' => ['label' => __('Release Year', 'wordpress-bucket-list'), 'type' => 'number'],
                    'runtime' => ['label' => __('Runtime (minutes)', 'wordpress-bucket-list'), 'type' => 'number'],
                    'genre' => ['label' => __('Genre', 'wordpress-bucket-list'), 'type' => 'text'],
                    'rating' => ['label' => __('Your Rating (1-10)', 'wordpress-bucket-list'), 'type' => 'number', 'min' => 1, 'max' => 10],
                    'imdb_rating' => ['label' => __('IMDb Rating', 'wordpress-bucket-list'), 'type' => 'number', 'step' => '0.1', 'min' => 1, 'max' => 10],
                    'watched_date' => ['label' => __('Date Watched', 'wordpress-bucket-list'), 'type' => 'date'],
                    'studio' => ['label' => __('Studio', 'wordpress-bucket-list'), 'type' => 'text'],
                ],
            ],
            'podcast' => [
                'label' => __('Podcast Details', 'wordpress-bucket-list'),
                'fields' => [
                    'cover_image' => ['label' => __('Podcast Cover Art', 'wordpress-bucket-list'), 'type' => 'image'],
                    'host' => ['label' => __('Host(s)', 'wordpress-bucket-list'), 'type' => 'text'],
                    'network' => ['label' => __('Network/Platform', 'wordpress-bucket-list'), 'type' => 'text'],
                    'episodes_total' => ['label' => __('Total Episodes', 'wordpress-bucket-list'), 'type' => 'number'],
                    'episodes_listened' => ['label' => __('Episodes Listened', 'wordpress-bucket-list'), 'type' => 'number'],
                    'genre' => ['label' => __('Genre/Category', 'wordpress-bucket-list'), 'type' => 'text'],
                    'rating' => ['label' => __('Your Rating (1-10)', 'wordpress-bucket-list'), 'type' => 'number', 'min' => 1, 'max' => 10],
                    'favorite_episode' => ['label' => __('Favorite Episode', 'wordpress-bucket-list'), 'type' => 'text'],
                    'started_date' => ['label' => __('Started Listening', 'wordpress-bucket-list'), 'type' => 'date'],
                ],
            ],
            'videogame' => [
                'label' => __('Video Game Details', 'wordpress-bucket-list'),
                'fields' => [
                    'cover_image' => ['label' => __('Game Cover', 'wordpress-bucket-list'), 'type' => 'image'],
                    'developer' => ['label' => __('Developer', 'wordpress-bucket-list'), 'type' => 'text'],
                    'publisher' => ['label' => __('Publisher', 'wordpress-bucket-list'), 'type' => 'text'],
                    'platform' => ['label' => __('Platform(s)', 'wordpress-bucket-list'), 'type' => 'text'],
                    'release_year' => ['label' => __('Release Year', 'wordpress-bucket-list'), 'type' => 'number'],
                    'genre' => ['label' => __('Genre', 'wordpress-bucket-list'), 'type' => 'text'],
                    'rating' => ['label' => __('Your Rating (1-10)', 'wordpress-bucket-list'), 'type' => 'number', 'min' => 1, 'max' => 10],
                    'hours_played' => ['label' => __('Hours Played', 'wordpress-bucket-list'), 'type' => 'number'],
                    'difficulty' => ['label' => __('Difficulty Level', 'wordpress-bucket-list'), 'type' => 'select', 'options' => ['Easy', 'Normal', 'Hard', 'Expert']],
                    'started_date' => ['label' => __('Started Playing', 'wordpress-bucket-list'), 'type' => 'date'],
                    'completed_date' => ['label' => __('Completed', 'wordpress-bucket-list'), 'type' => 'date'],
                ],
            ],
            'tv-serie' => [
                'label' => __('TV Series Details', 'wordpress-bucket-list'),
                'fields' => [
                    'cover_image' => ['label' => __('Series Poster', 'wordpress-bucket-list'), 'type' => 'image'],
                    'creator' => ['label' => __('Creator(s)', 'wordpress-bucket-list'), 'type' => 'text'],
                    'cast' => ['label' => __('Main Cast (comma separated)', 'wordpress-bucket-list'), 'type' => 'text'],
                    'seasons' => ['label' => __('Total Seasons', 'wordpress-bucket-list'), 'type' => 'number'],
                    'episodes' => ['label' => __('Total Episodes', 'wordpress-bucket-list'), 'type' => 'number'],
                    'current_season' => ['label' => __('Current Season Watching', 'wordpress-bucket-list'), 'type' => 'number'],
                    'current_episode' => ['label' => __('Current Episode', 'wordpress-bucket-list'), 'type' => 'number'],
                    'genre' => ['label' => __('Genre', 'wordpress-bucket-list'), 'type' => 'text'],
                    'network' => ['label' => __('Network/Platform', 'wordpress-bucket-list'), 'type' => 'text'],
                    'rating' => ['label' => __('Your Rating (1-10)', 'wordpress-bucket-list'), 'type' => 'number', 'min' => 1, 'max' => 10],
                    'started_date' => ['label' => __('Started Watching', 'wordpress-bucket-list'), 'type' => 'date'],
                ],
            ],
            'workout' => [
                'label' => __('Workout Details', 'wordpress-bucket-list'),
                'fields' => [
                    'cover_image' => ['label' => __('Workout Image', 'wordpress-bucket-list'), 'type' => 'image'],
                    'type' => ['label' => __('Workout Type', 'wordpress-bucket-list'), 'type' => 'text'],
                    'duration' => ['label' => __('Duration (minutes)', 'wordpress-bucket-list'), 'type' => 'number'],
                    'frequency' => ['label' => __('Frequency (times per week)', 'wordpress-bucket-list'), 'type' => 'number'],
                    'difficulty' => ['label' => __('Difficulty Level', 'wordpress-bucket-list'), 'type' => 'select', 'options' => ['Beginner', 'Intermediate', 'Advanced', 'Expert']],
                    'goal' => ['label' => __('Goal', 'wordpress-bucket-list'), 'type' => 'text'],
                    'trainer' => ['label' => __('Trainer/Program', 'wordpress-bucket-list'), 'type' => 'text'],
                    'location' => ['label' => __('Location (Gym, Home, etc.)', 'wordpress-bucket-list'), 'type' => 'text'],
                    'started_date' => ['label' => __('Started', 'wordpress-bucket-list'), 'type' => 'date'],
                    'target_date' => ['label' => __('Target Completion', 'wordpress-bucket-list'), 'type' => 'date'],
                ],
            ],
        ];
    }

    public static function add_category_meta_boxes()
    {
        add_meta_box(
            'wbl_category_details',
            __('Category-Specific Details', 'wordpress-bucket-list'),
            [__CLASS__, 'render_category_meta_box'],
            'bucket_item',
            'normal',
            'high'
        );
    }

    public static function render_category_meta_box($post)
    {
        wp_nonce_field('wbl_save_category_meta', 'wbl_category_meta_nonce');

        // Get current item type
        $current_item_type = get_post_meta($post->ID, '_wbl_item_type', true);
        $category_fields = self::get_category_fields();

?>
        <div class="wbl-category-meta-wrapper">
            <p style="margin-bottom: 20px;">
                <label for="wbl_item_type"><strong><?php _e('Item Type:', 'wordpress-bucket-list'); ?></strong></label><br>
                <select name="wbl_item_type" id="wbl_item_type" style="width: 100%; max-width: 400px;">
                    <option value=""><?php _e('-- Select Item Type --', 'wordpress-bucket-list'); ?></option>
                    <?php foreach ($category_fields as $type_slug => $config) : ?>
                        <option value="<?php echo esc_attr($type_slug); ?>" <?php selected($current_item_type, $type_slug); ?>>
                            <?php echo esc_html($config['label']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>

            <hr style="margin: 20px 0;">

            <?php foreach ($category_fields as $category_slug => $config) : ?>
                <div class="wbl-category-fields" data-category="<?php echo esc_attr($category_slug); ?>">
                    <h3><?php echo esc_html($config['label']); ?></h3>

                    <?php foreach ($config['fields'] as $field_key => $field_config) :
                        $meta_key = "_wbl_{$category_slug}_{$field_key}";
                        $value = get_post_meta($post->ID, $meta_key, true);
                    ?>
                        <p>
                            <label for="<?php echo esc_attr($meta_key); ?>">
                                <strong><?php echo esc_html($field_config['label']); ?>:</strong>
                            </label><br>

                            <?php if ($field_config['type'] === 'image') : ?>
                        <div class="wbl-image-upload">
                            <input
                                type="hidden"
                                id="<?php echo esc_attr($meta_key); ?>"
                                name="<?php echo esc_attr($meta_key); ?>"
                                value="<?php echo esc_attr($value); ?>" />
                            <div class="wbl-image-preview" style="margin-bottom: 10px;">
                                <?php if ($value) : ?>
                                    <img src="<?php echo esc_url(wp_get_attachment_url($value)); ?>" style="max-width: 200px; height: auto; display: block; margin-bottom: 10px;" />
                                <?php endif; ?>
                            </div>
                            <button type="button" class="button wbl-upload-image" data-field="<?php echo esc_attr($meta_key); ?>">
                                <?php _e('Upload Image', 'wordpress-bucket-list'); ?>
                            </button>
                            <?php if ($value) : ?>
                                <button type="button" class="button wbl-remove-image" data-field="<?php echo esc_attr($meta_key); ?>">
                                    <?php _e('Remove Image', 'wordpress-bucket-list'); ?>
                                </button>
                            <?php endif; ?>
                        </div>

                    <?php elseif ($field_config['type'] === 'textarea') : ?>
                        <textarea
                            id="<?php echo esc_attr($meta_key); ?>"
                            name="<?php echo esc_attr($meta_key); ?>"
                            rows="4"
                            class="large-text"><?php echo esc_textarea($value); ?></textarea>

                    <?php elseif ($field_config['type'] === 'select') : ?>
                        <select
                            id="<?php echo esc_attr($meta_key); ?>"
                            name="<?php echo esc_attr($meta_key); ?>">
                            <option value="">-- <?php _e('Select', 'wordpress-bucket-list'); ?> --</option>
                            <?php foreach ($field_config['options'] as $option) : ?>
                                <option value="<?php echo esc_attr($option); ?>" <?php selected($value, $option); ?>>
                                    <?php echo esc_html($option); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                    <?php else : ?>
                        <input
                            type="<?php echo esc_attr($field_config['type']); ?>"
                            id="<?php echo esc_attr($meta_key); ?>"
                            name="<?php echo esc_attr($meta_key); ?>"
                            value="<?php echo esc_attr($value); ?>"
                            <?php if (isset($field_config['step'])) echo 'step="' . esc_attr($field_config['step']) . '"'; ?>
                            class="<?php echo $field_config['type'] === 'text' ? 'large-text' : ''; ?>" />
                    <?php endif; ?>
                    </p>
                <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
<?php
    }

    public static function save_category_meta_boxes($post_id, $post)
    {
        // Only run for bucket_item post type
        if (!isset($post->post_type) || $post->post_type !== 'bucket_item') {
            return;
        }

        // Security checks
        if (!isset($_POST['wbl_category_meta_nonce']) || !wp_verify_nonce($_POST['wbl_category_meta_nonce'], 'wbl_save_category_meta')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Save item type
        $item_type = '';
        if (isset($_POST['wbl_item_type'])) {
            $item_type = sanitize_text_field($_POST['wbl_item_type']);
            update_post_meta($post_id, '_wbl_item_type', $item_type);
        }

        // Only save fields for the selected item type
        if (!empty($item_type)) {
            $category_fields = self::get_category_fields();

            if (isset($category_fields[$item_type])) {
                foreach ($category_fields[$item_type]['fields'] as $field_key => $field_config) {
                    $meta_key = "_wbl_{$item_type}_{$field_key}";

                    if (isset($_POST[$meta_key]) && $_POST[$meta_key] !== '') {
                        if ($field_config['type'] === 'image') {
                            $value = absint($_POST[$meta_key]);
                        } elseif ($field_config['type'] === 'textarea') {
                            $value = sanitize_textarea_field($_POST[$meta_key]);
                        } elseif ($field_config['type'] === 'number') {
                            $value = floatval($_POST[$meta_key]);
                            // Don't save if value is 0 and min is set to 1
                            if ($value == 0 && isset($field_config['min']) && $field_config['min'] > 0) {
                                continue;
                            }
                        } else {
                            $value = sanitize_text_field($_POST[$meta_key]);
                        }
                        update_post_meta($post_id, $meta_key, $value);
                    }
                }
            }
        }

        // Add admin notice that data was saved
        set_transient('wbl_data_saved_' . $post_id, true, 45);
    }

    public static function enqueue_admin_scripts($hook)
    {
        global $post_type, $post;

        if ('bucket_item' !== $post_type || !in_array($hook, ['post.php', 'post-new.php'])) {
            return;
        }

        // Show save confirmation
        if ($post && get_transient('wbl_data_saved_' . $post->ID)) {
            delete_transient('wbl_data_saved_' . $post->ID);
            add_action('admin_notices', function () {
                echo '<div class="notice notice-success is-dismissible"><p><strong>Bucket List Data Saved!</strong> Your category-specific details have been saved successfully.</p></div>';
            });
        }

        wp_enqueue_media();

        wp_add_inline_script('jquery', "
            jQuery(document).ready(function($) {
                
                // Add visual feedback when fields are filled
                $('input, textarea, select').on('change', function() {
                    console.log('Field changed:', $(this).attr('name'), '=', $(this).val());
                });
                
                function toggleCategoryFields() {
                    var selectedType = $('#wbl_item_type').val();
                    
                    // Use visibility instead of display to ensure fields are submitted
                    $('.wbl-category-fields').css({
                        'position': 'absolute',
                        'left': '-9999px',
                        'visibility': 'hidden'
                    });
                    
                    if (selectedType) {
                        $('.wbl-category-fields[data-category=\"' + selectedType + '\"]').css({
                            'position': 'static',
                            'left': 'auto',
                            'visibility': 'visible'
                        });
                    }
                }
                
                toggleCategoryFields();
                $('#wbl_item_type').on('change', toggleCategoryFields);
                
                // Image upload
                $(document).on('click', '.wbl-upload-image', function(e) {
                    e.preventDefault();
                    var button = $(this);
                    var fieldId = button.data('field');
                    var wp_media_frame = wp.media({
                        title: 'Select Image',
                        button: { text: 'Use This Image' },
                        multiple: false
                    });
                    
                    wp_media_frame.on('select', function() {
                        var attachment = wp_media_frame.state().get('selection').first().toJSON();
                        $('#' + fieldId).val(attachment.id);
                        button.closest('.wbl-image-upload').find('.wbl-image-preview').html(
                            '<img src=\"' + attachment.url + '\" style=\"max-width: 200px; height: auto; display: block; margin-bottom: 10px;\" />'
                        );
                        if (!button.next('.wbl-remove-image').length) {
                            button.after('<button type=\"button\" class=\"button wbl-remove-image\" data-field=\"' + fieldId + '\">Remove Image</button>');
                        }
                    });
                    
                    wp_media_frame.open();
                });
                
                // Image remove
                $(document).on('click', '.wbl-remove-image', function(e) {
                    e.preventDefault();
                    var fieldId = $(this).data('field');
                    $('#' + fieldId).val('');
                    $(this).closest('.wbl-image-upload').find('.wbl-image-preview').html('');
                    $(this).remove();
                });
            });
        ");
    }

    /**
     * Get the item type for a post
     */
    public static function get_item_type($post_id)
    {
        return get_post_meta($post_id, '_wbl_item_type', true);
    }

    /**
     * Get category-specific data for a post
     */
    public static function get_category_data($post_id, $category_slug)
    {
        $category_fields = self::get_category_fields();

        if (!isset($category_fields[$category_slug])) {
            return [];
        }

        $data = [];
        foreach ($category_fields[$category_slug]['fields'] as $field_key => $field_config) {
            $meta_key = "_wbl_{$category_slug}_{$field_key}";
            $value = get_post_meta($post_id, $meta_key, true);

            // Only add to array if value exists (not empty)
            if (!empty($value)) {
                $data[$field_key] = $value;
            }
        }

        return $data;
    }
}
