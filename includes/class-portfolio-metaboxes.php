<?php
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

        // Style Options Metabox
        add_meta_box(
            'portfolio_style_options',
            __('Style Options', 'portfolio-showcase'),
            array($this, 'render_style_options_metabox'),
            $this->post_type,
            'side',
            'default'
        );

        // Shortcode Metabox
        add_meta_box(
            'portfolio_shortcode',
            __('Display Shortcode', 'portfolio-showcase'),
            array($this, 'render_shortcode_metabox'),
            $this->post_type,
            'side',
            'high'
        );
    }

    public function render_carousel_metabox($post) {
        wp_nonce_field('portfolio_carousel_meta_box', 'portfolio_carousel_meta_box_nonce');
        
        // Get saved values
        $carousel_images = get_post_meta($post->ID, '_portfolio_carousel_images', true);
        $carousel_settings = get_post_meta($post->ID, '_portfolio_carousel_settings', true);
        
        // Default settings
        $default_settings = array(
            'enable_fullscreen' => true,
            'description_position' => 'bottom',
            'title_position' => 'top-left',
            'background_color' => '#000000'
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
                        <input type="checkbox" name="carousel_settings[enable_fullscreen]" 
                               <?php checked($settings['enable_fullscreen'], true); ?>>
                        <?php _e('Enable Fullscreen Mode', 'portfolio-showcase'); ?>
                    </label>
                </p>

                <p>
                    <label><?php _e('Description Position:', 'portfolio-showcase'); ?></label>
                    <select name="carousel_settings[description_position]">
                        <option value="top" <?php selected($settings['description_position'], 'top'); ?>>
                            <?php _e('Top', 'portfolio-showcase'); ?>
                        </option>
                        <option value="bottom" <?php selected($settings['description_position'], 'bottom'); ?>>
                            <?php _e('Bottom', 'portfolio-showcase'); ?>
                        </option>
                    </select>
                </p>

                <p>
                    <label><?php _e('Title Position:', 'portfolio-showcase'); ?></label>
                    <select name="carousel_settings[title_position]">
                        <option value="top-left" <?php selected($settings['title_position'], 'top-left'); ?>>
                            <?php _e('Top Left', 'portfolio-showcase'); ?>
                        </option>
                        <option value="top-right" <?php selected($settings['title_position'], 'top-right'); ?>>
                            <?php _e('Top Right', 'portfolio-showcase'); ?>
                        </option>
                        <option value="bottom-left" <?php selected($settings['title_position'], 'bottom-left'); ?>>
                            <?php _e('Bottom Left', 'portfolio-showcase'); ?>
                        </option>
                        <option value="bottom-right" <?php selected($settings['title_position'], 'bottom-right'); ?>>
                            <?php _e('Bottom Right', 'portfolio-showcase'); ?>
                        </option>
                        <option value="center" <?php selected($settings['title_position'], 'center'); ?>>
                            <?php _e('Center', 'portfolio-showcase'); ?>
                        </option>
                    </select>
                </p>

                <p>
                    <label><?php _e('Fullscreen Background Color:', 'portfolio-showcase'); ?></label>
                    <input type="color" name="carousel_settings[background_color]" 
                           value="<?php echo esc_attr($settings['background_color']); ?>">
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
        
        // Débogage temporaire
        error_log('Rendering color palette metabox');
        error_log('Saved colors: ' . print_r($colors, true));
        
        // Default settings
        $default_settings = array(
            'height' => 29,
            'comment_position' => 'bottom'
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
                    } else {
                        error_log('No colors found or colors is not an array');
                        // Ajouter un élément de couleur par défaut si aucun n'existe
                        ?>
                        <div class="color-item">
                            <input type="color" name="color_palette[colors][]" value="#000000" data-alpha="true">
                            <input type="text" name="color_palette[comments][]" 
                                   placeholder="<?php esc_attr_e('Color comment', 'portfolio-showcase'); ?>">
                            <button type="button" class="remove-color">×</button>
                        </div>
                        <?php
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
                    <input type="number" name="palette_settings[height]" 
                           value="<?php echo esc_attr($settings['height']); ?>" min="20" max="500">
                </p>

                <p>
                    <label><?php _e('Comments Position:', 'portfolio-showcase'); ?></label>
                    <select name="palette_settings[comment_position]">
                        <option value="top" <?php selected($settings['comment_position'], 'top'); ?>>
                            <?php _e('Top', 'portfolio-showcase'); ?>
                        </option>
                        <option value="bottom" <?php selected($settings['comment_position'], 'bottom'); ?>>
                            <?php _e('Bottom', 'portfolio-showcase'); ?>
                        </option>
                    </select>
                </p>
            </div>
        </div>
        <?php
    }

    public function render_style_options_metabox($post) {
        wp_nonce_field('portfolio_style_options_meta_box', 'portfolio_style_options_meta_box_nonce');
        
        // Get saved values
        $style_options = get_post_meta($post->ID, '_portfolio_style_options', true);
        
        // Default settings
        $default_options = array(
            'padding' => '',
            'margin' => '',
            'custom_css' => ''
        );
        
        $options = wp_parse_args($style_options, $default_options);
        
        ?>
        <div class="portfolio-style-options">
            <p>
                <label><?php _e('Padding:', 'portfolio-showcase'); ?></label>
                <input type="text" name="style_options[padding]" 
                       value="<?php echo esc_attr($options['padding']); ?>" 
                       placeholder="e.g., 10px or 10px 20px">
            </p>

            <p>
                <label><?php _e('Margin:', 'portfolio-showcase'); ?></label>
                <input type="text" name="style_options[margin]" 
                       value="<?php echo esc_attr($options['margin']); ?>"
                       placeholder="e.g., 10px or 10px 20px">
            </p>

            <p>
                <label><?php _e('Custom CSS:', 'portfolio-showcase'); ?></label>
                <textarea name="style_options[custom_css]" rows="4"
                          placeholder=".portfolio-item { /* your css */ }"
                ><?php echo esc_textarea($options['custom_css']); ?></textarea>
            </p>
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
                }
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

        // Save style options
        if (isset($_POST['style_options'])) {
            $style_options = $this->sanitize_style_options($_POST['style_options']);
            update_post_meta($post_id, '_portfolio_style_options', $style_options);
        }
    }

    private function verify_nonces($post_id) {
        $nonces = array(
            'portfolio_carousel_meta_box_nonce' => 'portfolio_carousel_meta_box',
            'portfolio_color_palette_meta_box_nonce' => 'portfolio_color_palette_meta_box',
            'portfolio_style_options_meta_box_nonce' => 'portfolio_style_options_meta_box'
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
            'enable_fullscreen' => isset($settings['enable_fullscreen']),
            'description_position' => sanitize_text_field($settings['description_position']),
            'title_position' => sanitize_text_field($settings['title_position']),
            'background_color' => sanitize_hex_color($settings['background_color'])
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
            'height' => absint($settings['height']),
            'comment_position' => sanitize_text_field($settings['comment_position'])
        );
    }

    private function sanitize_style_options($options) {
        return array(
            'padding' => sanitize_text_field($options['padding']),
            'margin' => sanitize_text_field($options['margin']),
            'custom_css' => wp_strip_all_tags($options['custom_css'])
        );
    }
} 