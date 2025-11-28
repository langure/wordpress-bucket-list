<?php

/**
 * Shortcode Handler
 */

if (!defined('ABSPATH')) {
    exit;
}

class WBL_Shortcode
{

    private static $original_locale = null;

    public static function init()
    {
        add_shortcode('bucket_list', [__CLASS__, 'render_shortcode']);
    }

    public static function render_shortcode($atts)
    {
        // Force language if set
        self::maybe_switch_locale();

        $atts = shortcode_atts([
            'category' => '',
            'columns' => WBL_Settings::get_default_columns(),
            'show_filter' => 'yes',
            'per_page' => WBL_Settings::get_default_per_page(),
            'pagination' => 'yes',
        ], $atts, 'bucket_list');

        ob_start();

        // Get current page
        $paged = get_query_var('paged') ? get_query_var('paged') : 1;

        // Get all bucket items with pagination
        $args = [
            'post_type' => 'bucket_item',
            'posts_per_page' => intval($atts['per_page']),
            'paged' => $paged,
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC',
        ];

        if (!empty($atts['category'])) {
            $args['tax_query'] = [
                [
                    'taxonomy' => 'bucket_category',
                    'field' => 'slug',
                    'terms' => sanitize_text_field($atts['category']),
                ],
            ];
        }

        $query = new WP_Query($args);

        // Calculate statistics (need all items for accurate stats)
        $stats_args = array_merge($args, ['posts_per_page' => -1, 'paged' => 1]);
        $stats_query = new WP_Query($stats_args);

        $total_items = $stats_query->post_count;
        $completed_items = 0;
        $total_completion = 0;

        if ($stats_query->have_posts()) {
            while ($stats_query->have_posts()) {
                $stats_query->the_post();
                $completion = get_post_meta(get_the_ID(), '_wbl_completion_percentage', true);
                $completion = $completion ? intval($completion) : 0;

                if ($completion == 100) {
                    $completed_items++;
                }
                $total_completion += $completion;
            }
            wp_reset_postdata();
        }

        $average_completion = $total_items > 0 ? round($total_completion / $total_items) : 0;

?>
        <div class="wbl-container" data-columns="<?php echo esc_attr($atts['columns']); ?>" data-per-page="<?php echo esc_attr($atts['per_page']); ?>">

            <!-- Statistics Section -->
            <div class="wbl-stats">
                <div class="wbl-stat-item">
                    <div class="wbl-stat-label"><?php echo self::translate('Achievement Progress'); ?></div>
                    <div class="wbl-stat-value"><?php echo esc_html($completed_items); ?> / <?php echo esc_html($total_items); ?></div>
                    <div class="wbl-stat-description"><?php echo self::translate('Items Completed'); ?></div>
                </div>

                <div class="wbl-stat-item">
                    <div class="wbl-stat-label"><?php echo self::translate('Overall Progress'); ?></div>
                    <div class="wbl-progress-circle">
                        <svg width="100" height="100">
                            <circle cx="50" cy="50" r="45" fill="none" stroke="#e0e0e0" stroke-width="8"></circle>
                            <circle
                                cx="50"
                                cy="50"
                                r="45"
                                fill="none"
                                stroke="#4CAF50"
                                stroke-width="8"
                                stroke-dasharray="<?php echo 2 * 3.14159 * 45; ?>"
                                stroke-dashoffset="<?php echo 2 * 3.14159 * 45 * (1 - $average_completion / 100); ?>"
                                transform="rotate(-90 50 50)"
                                stroke-linecap="round"></circle>
                            <text x="50" y="50" text-anchor="middle" dy="7" class="wbl-progress-text"><?php echo esc_html($average_completion); ?>%</text>
                        </svg>
                    </div>
                </div>
            </div>

            <?php if ($atts['show_filter'] === 'yes') : ?>
                <!-- Category Filter -->
                <div class="wbl-filter">
                    <button class="wbl-filter-btn active" data-category="all">
                        <?php echo self::translate('All'); ?>
                    </button>
                    <?php
                    $categories = get_terms([
                        'taxonomy' => 'bucket_category',
                        'hide_empty' => true,
                    ]);

                    if (!is_wp_error($categories) && !empty($categories)) {
                        foreach ($categories as $category) {
                            echo '<button class="wbl-filter-btn" data-category="' . esc_attr($category->slug) . '">';
                            echo esc_html($category->name);
                            echo '</button>';
                        }
                    }
                    ?>
                </div>
            <?php endif; ?>

            <!-- Items Grid -->
            <div class="wbl-grid wbl-grid-cols-<?php echo esc_attr($atts['columns']); ?>">
                <?php
                if ($query->have_posts()) {
                    while ($query->have_posts()) {
                        $query->the_post();
                        self::render_bucket_item(get_the_ID());
                    }
                    wp_reset_postdata();
                } else {
                    echo '<p class="wbl-no-items">' . self::translate('No bucket list items found.') . '</p>';
                }
                ?>
            </div>

            <?php if ($atts['pagination'] === 'yes' && $query->max_num_pages > 1) : ?>
                <!-- Pagination -->
                <div class="wbl-pagination">
                    <?php
                    echo paginate_links([
                        'total' => $query->max_num_pages,
                        'current' => $paged,
                        'prev_text' => '← ' . self::translate('Previous'),
                        'next_text' => self::translate('Next') . ' →',
                        'type' => 'list',
                        'mid_size' => 2,
                    ]);
                    ?>
                </div>
            <?php endif; ?>

            <?php if ($atts['pagination'] === 'yes') : ?>
                <!-- Load More Button (Alternative to pagination) -->
                <div class="wbl-load-more-container" style="display: none;">
                    <?php if ($query->max_num_pages > 1) : ?>
                        <button class="wbl-load-more" data-page="1" data-max-pages="<?php echo esc_attr($query->max_num_pages); ?>">
                            <?php echo self::translate('Load More'); ?>
                        </button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php

        $output = ob_get_clean();

        // Restore original locale
        self::restore_locale();

        return $output;
    }

    private static function render_bucket_item($post_id)
    {
        $description = get_post_meta($post_id, '_wbl_description', true);
        $completion = get_post_meta($post_id, '_wbl_completion_percentage', true);
        $completion = $completion ? intval($completion) : 0;

        // Ensure completion is exactly 100 for completed items
        if ($completion >= 100) {
            $completion = 100;
        }

        $related_post = get_post_meta($post_id, '_wbl_related_post_link', true);
        $external_link = get_post_meta($post_id, '_wbl_external_resource_link', true);

        // Get item type and specific data (for cover image only)
        $item_type = WBL_Category_Meta_Fields::get_item_type($post_id);
        $item_data = [];
        if ($item_type) {
            $item_data = WBL_Category_Meta_Fields::get_category_data($post_id, $item_type);
        }

        $categories = get_the_terms($post_id, 'bucket_category');
        $category_classes = '';
        if ($categories && !is_wp_error($categories)) {
            $category_slugs = array_map(function ($cat) {
                return 'wbl-cat-' . $cat->slug;
            }, $categories);
            $category_classes = implode(' ', $category_slugs);
        }

        $status_class = $completion == 100 ? 'wbl-completed' : '';

        // Calculate circle progress
        $radius = 60;
        $circumference = 2 * pi() * $radius;
        $progress_offset = $circumference * (1 - $completion / 100);

        // Get cover image - prioritize item-specific cover, fallback to featured image
        $cover_image_id = !empty($item_data['cover_image']) ? $item_data['cover_image'] : get_post_thumbnail_id($post_id);

    ?>
        <div class="wbl-card <?php echo esc_attr($category_classes . ' ' . $status_class); ?>" data-completion="<?php echo esc_attr($completion); ?>">
            <?php if ($cover_image_id) : ?>
                <div class="wbl-card-image">
                    <?php echo wp_get_attachment_image($cover_image_id, 'large'); ?>
                    <?php if ($completion == 100) : ?>
                        <div class="wbl-badge"><?php echo self::translate('Completed!'); ?></div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="wbl-card-content">
                <?php if ($categories && !is_wp_error($categories)) : ?>
                    <div class="wbl-card-categories">
                        <?php foreach ($categories as $category) : ?>
                            <span class="wbl-category-badge"><?php echo esc_html($category->name); ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <h3 class="wbl-card-title"><?php echo esc_html(get_the_title($post_id)); ?></h3>

                <div class="wbl-progress-bar-container">
                    <div class="wbl-progress-label">
                        <span><?php echo self::translate('Progress'); ?></span>
                        <span><?php echo esc_html($completion); ?>%</span>
                    </div>
                    <div class="wbl-progress-bar">
                        <div class="wbl-progress-fill" style="width: <?php echo esc_attr($completion); ?>%;"></div>
                    </div>
                </div>
            </div>

            <!-- Circular Progress on Hover -->
            <div class="wbl-progress-circle-center">
                <svg width="140" height="140">
                    <circle
                        class="wbl-circle-bg"
                        cx="70"
                        cy="70"
                        r="<?php echo $radius; ?>"></circle>
                    <circle
                        class="wbl-circle-progress"
                        cx="70"
                        cy="70"
                        r="<?php echo $radius; ?>"
                        stroke-dasharray="<?php echo $circumference; ?>"
                        stroke-dashoffset="<?php echo $progress_offset; ?>"
                        transform="rotate(-90 70 70)"></circle>
                    <text x="70" y="65" class="wbl-circle-text"><?php echo esc_html($completion); ?>%</text>
                    <text x="70" y="85" class="wbl-circle-label"><?php echo self::translate('Progress'); ?></text>
                </svg>
            </div>

            <?php if ($related_post || $external_link) : ?>
                <div class="wbl-card-links">
                    <?php if ($related_post) : ?>
                        <a href="<?php echo esc_url(get_permalink($related_post)); ?>" class="wbl-link" aria-label="<?php echo esc_attr(self::translate('Read More')); ?>">
                            <span class="dashicons dashicons-book-alt"></span>
                            <span class="wbl-link-text"><?php echo self::translate('Read More'); ?></span>
                        </a>
                    <?php endif; ?>

                    <?php if ($external_link) : ?>
                        <a href="<?php echo esc_url($external_link); ?>" class="wbl-link" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr(self::translate('Check it Out')); ?>">
                            <span class="dashicons dashicons-external"></span>
                            <span class="wbl-link-text"><?php echo self::translate('Check it Out'); ?></span>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
<?php
    }

    /**
     * Switch locale based on settings
     */
    private static function maybe_switch_locale()
    {
        $forced_lang = WBL_Settings::get_frontend_language();
        $current_locale = determine_locale();

        if ($forced_lang === 'es') {
            self::$original_locale = $current_locale;
            switch_to_locale('es_ES');
        } elseif ($forced_lang === 'en') {
            self::$original_locale = $current_locale;
            switch_to_locale('en_US');
        }
    }

    /**
     * Restore original locale
     */
    private static function restore_locale()
    {
        if (self::$original_locale !== null) {
            restore_previous_locale();
            self::$original_locale = null;
        }
    }

    /**
     * Translate text based on current locale
     */
    private static function translate($text)
    {
        $forced_lang = WBL_Settings::get_frontend_language();

        // Simple translation array
        $translations = [
            'es' => [
                'Achievement Progress' => 'Progreso de Logros',
                'Items Completed' => 'Elementos Completados',
                'Overall Progress' => 'Progreso General',
                'All' => 'Todos',
                'No bucket list items found.' => 'No se encontraron elementos en la lista de deseos.',
                'Completed!' => '¡Completado!',
                'Progress' => 'Progreso',
                'Read More' => 'Leer Más',
                'Check it Out' => 'Chécalo',
                'Previous' => 'Anterior',
                'Next' => 'Siguiente',
                'Load More' => 'Cargar Más',
                'By' => 'Por',
                'Director' => 'Director',
                'Artist' => 'Artista',
                'Platform' => 'Plataforma',
                'Network' => 'Red',
                'Host' => 'Presentador',
                'Rating' => 'Calificación',
            ],
        ];

        if ($forced_lang === 'es' && isset($translations['es'][$text])) {
            return $translations['es'][$text];
        }

        return $text;
    }
}
