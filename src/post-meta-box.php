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
        <div class="wbl-sidebar-metabox-wrapper">
            <label for="wbl_linked_bucket_item" style="display: block; margin-bottom: 8px; font-weight: 600;">
                <?php _e('Select Item:', 'wordpress-bucket-list'); ?>
            </label>
            <select name="wbl_linked_bucket_item" id="wbl_linked_bucket_item" style="width: 100%;">
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
        <style>
            .wbl-sidebar-metabox-wrapper {
                padding-right: 12px;
                box-sizing: border-box;
            }

            .wbl-sidebar-metabox-wrapper select {
                box-sizing: border-box;
            }
        </style>
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

    /**
     * Translate general text
     */
    private static function translate($text, $lang)
    {
        if ($lang === 'auto') {
            return $text;
        }

        $translations = [
            'es' => [
                'Technical Details' => 'Ficha Técnica',
            ],
        ];

        if ($lang === 'es' && isset($translations['es'][$text])) {
            return $translations['es'][$text];
        }

        return $text;
    }

    /**
     * Translate field labels
     */
    private static function translate_field_label($label, $lang)
    {
        if ($lang === 'auto') {
            return $label;
        }

        $translations = [
            'es' => [
                // Book fields
                'Book Cover' => 'Portada del Libro',
                'Author' => 'Autor',
                'ISBN' => 'ISBN',
                'Number of Pages' => 'Número de Páginas',
                'Publisher' => 'Editorial',
                'Publication Year' => 'Año de Publicación',
                'Genre' => 'Género',
                'Your Rating (1-10)' => 'Mi Calificación',
                'Started Reading' => 'Inicio de Lectura',
                'Finished Reading' => 'Fin de Lectura',
                'Favorite Quote' => 'Cita Favorita',

                // Movie fields
                'Movie Poster' => 'Póster de la Película',
                'Director' => 'Director',
                'Main Cast (comma separated)' => 'Reparto Principal',
                'Release Year' => 'Año de Estreno',
                'Runtime (minutes)' => 'Duración (minutos)',
                'IMDb Rating' => 'Calificación IMDb',
                'Date Watched' => 'Fecha de Visualización',
                'Studio' => 'Estudio',

                // Music Album fields
                'Album Cover' => 'Portada del Álbum',
                'Artist/Band' => 'Artista/Banda',
                'Record Label' => 'Sello Discográfico',
                'Track List (one per line)' => 'Lista de Canciones',
                'Favorite Track' => 'Canción Favorita',
                'Date Listened' => 'Fecha de Escucha',

                // TV Series fields
                'Series Poster' => 'Póster de la Serie',
                'Creator(s)' => 'Creador(es)',
                'Total Seasons' => 'Temporadas Totales',
                'Total Episodes' => 'Episodios Totales',
                'Current Season Watching' => 'Temporada Actual',
                'Current Episode' => 'Episodio Actual',
                'Network/Platform' => 'Cadena/Plataforma',
                'Started Watching' => 'Inicio de Visualización',

                // Video Game fields
                'Game Cover' => 'Portada del Juego',
                'Developer' => 'Desarrollador',
                'Platform(s)' => 'Plataforma(s)',
                'Hours Played' => 'Horas Jugadas',
                'Difficulty Level' => 'Nivel de Dificultad',
                'Started Playing' => 'Inicio de Juego',
                'Completed' => 'Completado',

                // Podcast fields
                'Podcast Cover Art' => 'Arte del Podcast',
                'Host(s)' => 'Presentador(es)',
                'Total Episodes' => 'Episodios Totales',
                'Episodes Listened' => 'Episodios Escuchados',
                'Genre/Category' => 'Género/Categoría',
                'Favorite Episode' => 'Episodio Favorito',
                'Started Listening' => 'Inicio de Escucha',

                // Workout fields
                'Workout Image' => 'Imagen del Entrenamiento',
                'Workout Type' => 'Tipo de Entrenamiento',
                'Duration (minutes)' => 'Duración (minutos)',
                'Frequency (times per week)' => 'Frecuencia (veces por semana)',
                'Goal' => 'Objetivo',
                'Trainer/Program' => 'Entrenador/Programa',
                'Location (Gym, Home, etc.)' => 'Ubicación (Gimnasio, Casa, etc.)',
                'Started' => 'Inicio',
                'Target Completion' => 'Finalización Objetivo',
            ],
        ];

        if ($lang === 'es' && isset($translations['es'][$label])) {
            return $translations['es'][$label];
        }

        return $label;
    }

    /**
     * Format rating value with stars
     */
    private static function format_rating($value)
    {
        $rating = floatval($value);
        $stars = str_repeat('⭐', intval($rating));
        return $stars . ' ' . $rating . '/10';
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

        // Get language setting
        $forced_lang = WBL_Settings::get_frontend_language();

        // Get bucket item title
        $item_title = get_the_title($bucket_item_id);

        // Translate "Technical Details" title
        $tech_details_title = self::translate('Technical Details', $forced_lang);

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
                <div style="margin-bottom: 8px; font-size: 14px; font-weight: 600; color: #999; text-transform: uppercase; letter-spacing: 1px;">
                    <?php echo esc_html($tech_details_title); ?>
                </div>
                <h3><?php echo esc_html($item_title); ?></h3>

                <div class="wbl-details-grid">
                    <?php
                    // Display fields in a grid layout
                    foreach ($item_data as $field_key => $value) :
                        if (!empty($value) && $field_key !== 'cover_image') :
                            $field_info = isset($field_config['fields'][$field_key]) ? $field_config['fields'][$field_key] : null;
                            if (!$field_info) continue;

                            // Translate field label
                            $translated_label = self::translate_field_label($field_info['label'], $forced_lang);

                            // Full width for textarea fields
                            $is_full = ($field_info['type'] === 'textarea');

                            // Check if this is a rating field
                            $is_rating = (stripos($field_key, 'rating') !== false);
                    ?>
                            <div class="wbl-detail-item <?php echo $is_full ? 'wbl-detail-full' : ''; ?>">
                                <span class="wbl-detail-label"><?php echo esc_html($translated_label); ?></span>
                                <div class="wbl-detail-value">
                                    <?php
                                    if ($is_rating) {
                                        echo self::format_rating($value);
                                    } elseif ($field_info['type'] === 'textarea') {
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
