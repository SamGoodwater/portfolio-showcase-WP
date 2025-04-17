<?php
// WordPress core functions
use function add_action;
use function add_meta_box;
use function get_post_meta;
use function update_post_meta;
use function wp_nonce_field;
use function wp_verify_nonce;
use function sanitize_text_field;
use function sanitize_hex_color;
use function absint;
use function esc_attr;
use function esc_html;
use function esc_url;
use function __;

class Portfolio_Metaboxes {
    private $post_type;

    public function __construct() {
        $this->post_type = 'portfolio_project';
    }

    public function init() {
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_meta_boxes'));
    }

    public function add_meta_boxes() {
        // Carousel Metabox
        add_meta_box(
            'portfolio_carousel',
            __('Project Carousel', 'portfolio-showcase'),
            array($this, 'render_carousel_metabox'),
            $this->post_type,
            'normal',
            'high'
        );

        // Color Palette Metabox
        add_meta_box(
            'portfolio_color_palette',
            __('Color Palette', 'portfolio-showcase'),
            array($this, 'render_color_palette_metabox'),
            $this->post_type,
            'normal',
            'high'
        );

        // Shortcode Metabox
        add_meta_box(
            'portfolio_shortcode',
            __('Display Shortcode', 'portfolio-showcase'),
            array($this, 'render_shortcode_metabox'),
            $this->post_type,
            'side',
            'default'
        );
    }

    public function render_carousel_metabox($post) {
        wp_nonce_field('portfolio_carousel_meta_box', 'portfolio_carousel_meta_box_nonce');
        
        // Get saved values
        $carousel_images = get_post_meta($post->ID, '_portfolio_carousel_images', true);
        $carousel_settings = get_post_meta($post->ID, '_portfolio_carousel_settings', true);
        
        // Get global settings
        $settings_manager = new Portfolio_Settings();
        $global_settings = $settings_manager->get_carousel_settings();
        
        // Default settings
        $default_settings = array(
            'local-carousel-enable-fullscreen' => $global_settings['local-carousel-enable-fullscreen'],
            'local-carousel-position-description' => $global_settings['local-carousel-position-description'],
            'local-carousel-position-title' => $global_settings['local-carousel-position-title'],
            'local-carousel-color-background-fullscreen' => $global_settings['local-carousel-color-background-fullscreen'],
            'local-carousel-color-background' => $global_settings['local-carousel-color-background'],
            'local-carousel-color-title' => $global_settings['local-carousel-color-title'],
            'local-carousel-color-description' => $global_settings['local-carousel-color-description'],
            'local-carousel-opacity-background-fullscreen' => $global_settings['local-carousel-opacity-background-fullscreen'],
            'local-carousel-color-description-fullscreen' => $global_settings['local-carousel-color-description-fullscreen'],
            'local-carousel-color-title-fullscreen' => $global_settings['local-carousel-color-title-fullscreen']
        );
        
        $settings = wp_parse_args($carousel_settings, $default_settings);
        
        // Output template
        ?>
        <div class="portfolio-carousel-container">
            <div class="carousel-images">
                <h4><?php _e('Carousel Images', 'portfolio-showcase'); ?></h4>
                <div id="carousel-image-list">
                    <?php
                    if (!empty($carousel_images)) {
                        foreach ($carousel_images as $index => $image) {
                            $img_url = wp_get_attachment_image_url($image['id'], 'thumbnail');
                            if ($img_url) {
                                ?>
                                <div class="carousel-image-item" data-id="<?php echo esc_attr($image['id']); ?>">
                                    <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($image['title']); ?>">
                                    <div class="image-details">
                                        <input type="text" name="carousel_images[<?php echo $index; ?>][title]" 
                                               value="<?php echo esc_attr($image['title']); ?>" 
                                               placeholder="<?php esc_attr_e('Image title', 'portfolio-showcase'); ?>">
                                        <textarea name="carousel_images[<?php echo $index; ?>][description]" 
                                                  placeholder="<?php esc_attr_e('Image description', 'portfolio-showcase'); ?>"><?php echo esc_textarea($image['description']); ?></textarea>
                                    </div>
                                    <input type="hidden" name="carousel_images[<?php echo $index; ?>][id]" value="<?php echo esc_attr($image['id']); ?>">
                                    <input type="hidden" name="carousel_images[<?php echo $index; ?>][url]" value="<?php echo esc_attr($image['url']); ?>">
                                    <button type="button" class="remove-image">×</button>
                                </div>
                                <?php
                            }
                        }
                    }
                    ?>
                </div>
                <button type="button" class="button" id="add-carousel-images">
                    <?php _e('Add Images', 'portfolio-showcase'); ?>
                </button>
            </div>

            <div class="carousel-settings">
                <h4><?php _e('Carousel Settings', 'portfolio-showcase'); ?></h4>
                
                <p>
                    <label>
                        <input type="checkbox" name="carousel_settings[local-carousel-enable-fullscreen]" 
                               <?php checked($settings['local-carousel-enable-fullscreen'], true); ?>>
                        <?php _e('Enable Fullscreen Mode', 'portfolio-showcase'); ?>
                    </label>
                </p>

                <div class="portfolio-showcase-field">
                    <label for="carousel_settings[local-carousel-position-description]"><?php _e('Position de la description', 'portfolio-showcase'); ?></label>
                    <select name="carousel_settings[local-carousel-position-description]" id="carousel_settings[local-carousel-position-description]">
                        <option value="top" <?php selected($settings['local-carousel-position-description'], 'top'); ?>><?php _e('Haut', 'portfolio-showcase'); ?></option>
                        <option value="bottom" <?php selected($settings['local-carousel-position-description'], 'bottom'); ?>><?php _e('Bas', 'portfolio-showcase'); ?></option>
                    </select>
                </div>

                <div class="portfolio-showcase-field">
                    <label for="carousel_settings[local-carousel-color-description-fullscreen]"><?php _e('Couleur du texte de la description (mode plein écran)', 'portfolio-showcase'); ?></label>
                    <input type="color" name="carousel_settings[local-carousel-color-description-fullscreen]" id="carousel_settings[local-carousel-color-description-fullscreen]" value="<?php echo esc_attr($settings['local-carousel-color-description-fullscreen'] ?? '#ffffff'); ?>" />
                </div>

                <div class="portfolio-showcase-field">
                    <label for="carousel_settings[local-carousel-color-title-fullscreen]"><?php _e('Couleur du texte du titre (mode plein écran)', 'portfolio-showcase'); ?></label>
                    <input type="color" name="carousel_settings[local-carousel-color-title-fullscreen]" id="carousel_settings[local-carousel-color-title-fullscreen]" value="<?php echo esc_attr($settings['local-carousel-color-title-fullscreen'] ?? '#ffffff'); ?>" />
                </div>

                <p>
                    <label><?php _e('Title Position:', 'portfolio-showcase'); ?></label>
                    <select name="carousel_settings[local-carousel-position-title]">
                        <option value="top-left" <?php selected($settings['local-carousel-position-title'], 'top-left'); ?>>
                            <?php _e('Top Left', 'portfolio-showcase'); ?>
                        </option>
                        <option value="top-right" <?php selected($settings['local-carousel-position-title'], 'top-right'); ?>>
                            <?php _e('Top Right', 'portfolio-showcase'); ?>
                        </option>
                        <option value="top-center" <?php selected($settings['local-carousel-position-title'], 'top-center'); ?>>
                            <?php _e('top Center', 'portfolio-showcase'); ?>
                        </option>
                        <option value="bottom-left" <?php selected($settings['local-carousel-position-title'], 'bottom-left'); ?>>
                            <?php _e('Bottom Left', 'portfolio-showcase'); ?>
                        </option>
                        <option value="bottom-center" <?php selected($settings['local-carousel-position-title'], 'bottom-center'); ?>>
                            <?php _e('Bottom Center', 'portfolio-showcase'); ?>
                        </option>
                        <option value="bottom-right" <?php selected($settings['local-carousel-position-title'], 'bottom-right'); ?>>
                            <?php _e('Bottom Right', 'portfolio-showcase'); ?>
                        </option>
                        <option value="center" <?php selected($settings['local-carousel-position-title'], 'center'); ?>>
                            <?php _e('Center', 'portfolio-showcase'); ?>
                        </option>
                    </select>
                </p>

                <div class="portfolio-showcase-field">
                    <label for="carousel_settings[local-carousel-color-background-fullscreen]"><?php _e('Couleur de fond', 'portfolio-showcase'); ?></label>
                    <input type="color" name="carousel_settings[local-carousel-color-background-fullscreen]" id="carousel_settings[local-carousel-color-background-fullscreen]" value="<?php echo esc_attr($settings['local-carousel-color-background-fullscreen'] ?? '#000000'); ?>" />
                </div>

                <p>
                    <label><?php _e('Normal Mode Background Color:', 'portfolio-showcase'); ?></label>
                    <input type="color" name="carousel_settings[local-carousel-color-background]" 
                           value="<?php echo esc_attr(isset($settings['local-carousel-color-background']) ? $settings['local-carousel-color-background'] : '#f5f5f5'); ?>">
                </p>

                <p>
                    <label><?php _e('Title Text Color:', 'portfolio-showcase'); ?></label>
                    <input type="color" name="carousel_settings[local-carousel-color-title]" 
                           value="<?php echo esc_attr(isset($settings['local-carousel-color-title']) ? $settings['local-carousel-color-title'] : '#f5f5f5'); ?>">
                </p>

                <p>
                    <label><?php _e('Description Text Color:', 'portfolio-showcase'); ?></label>
                    <input type="color" name="carousel_settings[local-carousel-color-description]" 
                           value="<?php echo esc_attr(isset($settings['local-carousel-color-description']) ? $settings['local-carousel-color-description'] : '#2e3d38'); ?>">
                </p>

                <p>
                    <label><?php _e('Fullscreen Background Opacity:', 'portfolio-showcase'); ?></label>
                    <input type="range" name="carousel_settings[local-carousel-opacity-background-fullscreen]" 
                           min="0" max="100" value="<?php echo esc_attr(isset($settings['local-carousel-opacity-background-fullscreen']) ? $settings['local-carousel-opacity-background-fullscreen'] : 90); ?>">
                    <span class="opacity-value"><?php echo esc_html(isset($settings['local-carousel-opacity-background-fullscreen']) ? $settings['local-carousel-opacity-background-fullscreen'] : 90); ?>%</span>
                </p>
            </div>
        </div>
        <?php
    }

    public function render_color_palette_metabox($post) {
        wp_nonce_field('portfolio_color_palette_meta_box', 'portfolio_color_palette_meta_box_nonce');
        
        // Get saved values
        $colors = get_post_meta($post->ID, '_portfolio_color_palette', true);
        $palette_settings = get_post_meta($post->ID, '_portfolio_palette_settings', true);
        
        // Get global settings
        $settings_manager = new Portfolio_Settings();
        $global_settings = $settings_manager->get_palette_settings();
        
        // Default settings
        $default_settings = array(
            'local-palette-height-rectangle' => $global_settings['local-palette-height-rectangle'],
            'local-palette-position-comment' => $global_settings['local-palette-position-comment'],
            'local-palette-color-comment' => $global_settings['local-palette-color-comment']
        );
        
        $settings = wp_parse_args($palette_settings, $default_settings);
        
        ?>
        <div class="portfolio-color-palette-container">
            <div class="color-list">
                <div id="color-palette-list">
                    <?php
                    if (!empty($colors) && is_array($colors)) {
                        foreach ($colors as $index => $color) {
                            // Vérifier que la couleur est valide
                            $color_value = isset($color['color']) ? $color['color'] : '#000000';
                            $comment_value = isset($color['comment']) ? $color['comment'] : '';
                            
                            error_log('Rendering color: ' . $color_value . ' with comment: ' . $comment_value);
                            ?>
                            <div class="color-item">
                                <input type="color" name="color_palette[colors][]" 
                                       value="<?php echo esc_attr($color_value); ?>" data-alpha="true">
                                <input type="text" name="color_palette[comments][]" 
                                       value="<?php echo esc_attr($comment_value); ?>" 
                                       placeholder="<?php esc_attr_e('Color comment', 'portfolio-showcase'); ?>">
                                <button type="button" class="remove-color">×</button>
                            </div>
                            <?php
                        }
                    } 
                    ?>
                </div>
                <button type="button" class="button" id="add-color">
                    <?php _e('Add Color', 'portfolio-showcase'); ?>
                </button>
            </div>

            <div class="palette-settings">
                <p>
                    <label><?php _e('Rectangle Height (px):', 'portfolio-showcase'); ?></label>
                    <input type="number" name="palette_settings[local-palette-height-rectangle]" 
                           value="<?php echo esc_attr($settings['local-palette-height-rectangle']); ?>" min="0" max="500">
                </p>

                <p>
                    <label><?php _e('Comments Position:', 'portfolio-showcase'); ?></label>
                    <select name="palette_settings[local-palette-position-comment]">
                        <option value="top" <?php selected($settings['local-palette-position-comment'], 'top'); ?>>
                            <?php _e('Top', 'portfolio-showcase'); ?>
                        </option>
                        <option value="bottom" <?php selected($settings['local-palette-position-comment'], 'bottom'); ?>>
                            <?php _e('Bottom', 'portfolio-showcase'); ?>
                        </option>
                    </select>
                </p>
                
                <p>
                    <label><?php _e('Comment Color:', 'portfolio-showcase'); ?></label>
                    <input type="color" name="palette_settings[local-palette-color-comment]" 
                           value="<?php echo esc_attr($settings['local-palette-color-comment']); ?>">
                </p>
            </div>
        </div>
        <?php
    }

    public function render_shortcode_metabox($post) {
        ?>
        <div class="portfolio-shortcode-container">
            <p><?php _e('Copy and paste this shortcode to display this project:', 'portfolio-showcase'); ?></p>
            <div class="shortcode-display">
                <code>[portfolio_showcase id="<?php echo esc_attr($post->ID); ?>"]</code>
                <button type="button" class="button copy-shortcode" data-shortcode='[portfolio_showcase id="<?php echo esc_attr($post->ID); ?>"]'>
                    <?php _e('Copy', 'portfolio-showcase'); ?>
                </button>
            </div>
            <p class="description">
                <?php _e('You can paste this shortcode in any post or page to display this project.', 'portfolio-showcase'); ?>
            </p>
        </div>
        <style>
            .portfolio-shortcode-container {
                padding: 10px;
            }
            .shortcode-display {
                background: #f0f0f1;
                padding: 10px;
                border-radius: 4px;
                margin: 10px 0;
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .shortcode-display code {
                flex: 1;
                padding: 5px;
                background: #fff;
                border: 1px solid #ddd;
                border-radius: 3px;
            }
            .copy-shortcode {
                white-space: nowrap;
            }
        </style>
        <script>
        jQuery(document).ready(function($) {
            $('.copy-shortcode').on('click', function() {
                var shortcode = $(this).data('shortcode');
                navigator.clipboard.writeText(shortcode).then(function() {
                    var $button = $('.copy-shortcode');
                    var originalText = $button.text();
                    $button.text('<?php _e('Copied!', 'portfolio-showcase'); ?>');
                    setTimeout(function() {
                        $button.text(originalText);
                    }, 2000);
                });
            });
        });
        </script>
        <?php
    }

    public function save_meta_boxes($post_id) {
        // Check if our nonce is set for each metabox
        if (!$this->verify_nonces($post_id)) {
            return;
        }

        // If this is an autosave, our form has not been submitted, so we don't want to do anything
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Check the user's permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Save carousel settings
        if (isset($_POST['carousel_settings'])) {
            $carousel_settings = $this->sanitize_carousel_settings($_POST['carousel_settings']);
            update_post_meta($post_id, '_portfolio_carousel_settings', $carousel_settings);
        }

        // Save carousel images
        if (isset($_POST['carousel_images']) && is_array($_POST['carousel_images'])) {
            $carousel_images = array();
            
            // Nouveau format avec index
            if (isset($_POST['carousel_images'][0]) && is_array($_POST['carousel_images'][0])) {
                foreach ($_POST['carousel_images'] as $index => $image_data) {
                    if (isset($image_data['id'])) {
                        $carousel_images[] = array(
                            'id' => absint($image_data['id']),
                            'title' => sanitize_text_field($image_data['title']),
                            'description' => wp_kses_post($image_data['description']),
                            'url' => esc_url_raw($image_data['url'])
                        );
                    }
                }
            } 
            // Ancien format avec JSON
            else {
                foreach ($_POST['carousel_images'] as $image_data) {
                    if (is_array($image_data)) {
                        $carousel_images[] = array(
                            'id' => absint($image_data['id']),
                            'title' => sanitize_text_field($image_data['title']),
                            'description' => wp_kses_post($image_data['description']),
                            'url' => esc_url_raw($image_data['url'])
                        );
                    } else {
                        $image = json_decode(stripslashes($image_data), true);
                        if ($image && isset($image['id'])) {
                            $carousel_images[] = array(
                                'id' => absint($image['id']),
                                'title' => sanitize_text_field($image['title']),
                                'description' => wp_kses_post($image['description']),
                                'url' => esc_url_raw($image['url'])
                            );
                        }
                    }
                }
            }
            
            update_post_meta($post_id, '_portfolio_carousel_images', $carousel_images);
        }

        // Save color palette - Nouvelle méthode avec champ caché
        if (isset($_POST['color_palette_data'])) {
            error_log('Color palette data found in hidden field: ' . $_POST['color_palette_data']);
            
            $color_data = json_decode(stripslashes($_POST['color_palette_data']), true);
            
            if (is_array($color_data) && !empty($color_data)) {
                error_log('Decoded color data: ' . print_r($color_data, true));
                
                $color_palette = array();
                foreach ($color_data as $item) {
                    if (isset($item['color'])) {
                        $color = $item['color'];
                        $comment = isset($item['comment']) ? $item['comment'] : '';
                        
                        // Gérer les couleurs avec transparence
                        if (strpos($color, 'rgba') === 0) {
                            $sanitized_color = $color;
                        } else {
                            $sanitized_color = sanitize_hex_color($color);
                        }
                        
                        $color_palette[] = array(
                            'color' => $sanitized_color,
                            'comment' => sanitize_text_field($comment)
                        );
                    }
                }
                
                if (!empty($color_palette)) {
                    update_post_meta($post_id, '_portfolio_color_palette', $color_palette);
                    error_log('Color palette saved successfully: ' . print_r($color_palette, true));
                } else {
                    // Si la palette est vide, supprimer les métadonnées
                    delete_post_meta($post_id, '_portfolio_color_palette');
                    error_log('Color palette is empty, deleting metadata');
                }
            } else {
                // Si les données sont vides, supprimer les métadonnées
                delete_post_meta($post_id, '_portfolio_color_palette');
                error_log('Color palette data is empty, deleting metadata');
            }
        }
        // Ancienne méthode avec color_palette
        else if (isset($_POST['color_palette']) && is_array($_POST['color_palette'])) {
            // Débogage temporaire
            error_log('Color palette data: ' . print_r($_POST['color_palette'], true));
            
            $color_palette = array();
            $colors = isset($_POST['color_palette']['colors']) ? $_POST['color_palette']['colors'] : array();
            $comments = isset($_POST['color_palette']['comments']) ? $_POST['color_palette']['comments'] : array();
            
            error_log('Colors: ' . print_r($colors, true));
            error_log('Comments: ' . print_r($comments, true));
            
            // Vérifier si les couleurs sont vides
            if (empty($colors)) {
                error_log('No colors found in the form data');
            }
            
            for ($i = 0; $i < count($colors); $i++) {
                if (isset($colors[$i])) {
                    // Gérer les couleurs avec transparence
                    $color = $colors[$i];
                    error_log('Processing color: ' . $color);
                    
                    // Si la couleur est au format rgba, la conserver telle quelle
                    if (strpos($color, 'rgba') === 0) {
                        $sanitized_color = $color;
                        error_log('Using rgba color: ' . $sanitized_color);
                    } else {
                        // Sinon, utiliser la fonction standard
                        $sanitized_color = sanitize_hex_color($color);
                        error_log('Using hex color: ' . $sanitized_color);
                    }
                    
                    $comment = isset($comments[$i]) ? $comments[$i] : '';
                    error_log('Comment: ' . $comment);
                    
                    $color_palette[] = array(
                        'color' => $sanitized_color,
                        'comment' => sanitize_text_field($comment)
                    );
                }
            }
            
            error_log('Final color palette: ' . print_r($color_palette, true));
            
            // Vérifier si la palette est vide
            if (empty($color_palette)) {
                error_log('Color palette is empty, not saving');
            } else {
                update_post_meta($post_id, '_portfolio_color_palette', $color_palette);
                error_log('Color palette saved successfully');
            }
        } else {
            error_log('No color palette data found in the form');
        }

        // Save palette settings
        if (isset($_POST['palette_settings'])) {
            $palette_settings = $this->sanitize_palette_settings($_POST['palette_settings']);
            update_post_meta($post_id, '_portfolio_palette_settings', $palette_settings);
        }
    }

    private function verify_nonces($post_id) {
        $nonces = array(
            'portfolio_carousel_meta_box_nonce' => 'portfolio_carousel_meta_box',
            'portfolio_color_palette_meta_box_nonce' => 'portfolio_color_palette_meta_box'
        );

        // Vérifier chaque nonce individuellement
        foreach ($nonces as $nonce => $action) {
            // Si le nonce est présent, vérifiez-le
            if (isset($_POST[$nonce])) {
                if (!wp_verify_nonce($_POST[$nonce], $action)) {
                    return false;
                }
            }
            // Si le nonce n'est pas présent, ce n'est pas grave, on continue
        }

        return true;
    }

    private function sanitize_carousel_settings($settings) {
        return array(
            'local-carousel-enable-fullscreen' => isset($settings['local-carousel-enable-fullscreen']),
            'local-carousel-position-description' => !empty($settings['local-carousel-position-description']) ? sanitize_text_field($settings['local-carousel-position-description']) : 'bottom',
            'local-carousel-position-title' => !empty($settings['local-carousel-position-title']) ? sanitize_text_field($settings['local-carousel-position-title']) : 'top-left',
            'local-carousel-color-background-fullscreen' => !empty($settings['local-carousel-color-background-fullscreen']) ? sanitize_hex_color($settings['local-carousel-color-background-fullscreen']) : '#111111',
            'local-carousel-color-background' => isset($settings['local-carousel-color-background']) ? sanitize_hex_color($settings['local-carousel-color-background']) : '#f5f5f5',
            'local-carousel-color-title' => isset($settings['local-carousel-color-title']) ? sanitize_hex_color($settings['local-carousel-color-title']) : '#f5f5f5',
            'local-carousel-color-description' => isset($settings['local-carousel-color-description']) ? sanitize_hex_color($settings['local-carousel-color-description']) : '#2e3d38',
            'local-carousel-opacity-background-fullscreen' => isset($settings['local-carousel-opacity-background-fullscreen']) ? absint($settings['local-carousel-opacity-background-fullscreen']) : 90,
            'local-carousel-color-description-fullscreen' => isset($settings['local-carousel-color-description-fullscreen']) ? sanitize_hex_color($settings['local-carousel-color-description-fullscreen']) : '#f2f7f5',
            'local-carousel-color-title-fullscreen' => isset($settings['local-carousel-color-title-fullscreen']) ? sanitize_hex_color($settings['local-carousel-color-title-fullscreen']) : '#f5f5f5'
        );
    }

    private function sanitize_carousel_images($images) {
        if (!is_array($images)) {
            return array();
        }

        return array_map(function($image) {
            return array(
                'id' => absint($image['id']),
                'title' => sanitize_text_field($image['title']),
                'description' => wp_kses_post($image['description']),
                'is_main' => isset($image['is_main'])
            );
        }, $images);
    }

    private function sanitize_color_palette($colors) {
        if (!is_array($colors)) {
            return array();
        }

        return array_map(function($color) {
            return array(
                'color' => sanitize_hex_color($color['color']),
                'comment' => sanitize_text_field($color['comment'])
            );
        }, $colors);
    }

    private function sanitize_palette_settings($settings) {
        return array(
            'local-palette-height-rectangle' => absint($settings['local-palette-height-rectangle']),
            'local-palette-position-comment' => sanitize_text_field($settings['local-palette-position-comment']),
            'local-palette-color-comment' => sanitize_hex_color($settings['local-palette-color-comment'])
        );
    }
} 