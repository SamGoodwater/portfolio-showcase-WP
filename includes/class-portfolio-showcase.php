<?php
class Portfolio_Showcase {
    private $version;

    public function __construct() {
        $this->version = PORTFOLIO_SHOWCASE_VERSION;
        $this->load_dependencies();
    }

    private function load_dependencies() {
        // Initialize Post Type
        $post_type = new Portfolio_Post_Type();
        $post_type->init();

        // Initialize Metaboxes
        $metaboxes = new Portfolio_Metaboxes();
        $metaboxes->init();
        
        // Initialize Settings
        $settings = new Portfolio_Settings();
        $settings->init();
    }

    public function run() {
        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        
        // Add shortcode
        add_shortcode('portfolio_showcase', array($this, 'render_portfolio'));
    }

    public function enqueue_frontend_assets() {
        // Enqueue frontend CSS
        wp_enqueue_style(
            'portfolio-showcase',
            PORTFOLIO_SHOWCASE_URL . 'assets/css/portfolio-showcase.css',
            array(),
            $this->version
        );

        // Enqueue frontend JavaScript
        wp_enqueue_script(
            'portfolio-showcase',
            PORTFOLIO_SHOWCASE_URL . 'assets/js/portfolio-showcase.js',
            array('jquery'),
            $this->version,
            true
        );

        // Get global settings
        $settings_manager = new Portfolio_Settings();
        $global_carousel_settings = $settings_manager->get_carousel_settings();
        $global_palette_settings = $settings_manager->get_palette_settings();

        // Localize script
        wp_localize_script('portfolio-showcase', 'portfolioShowcaseSettings', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('portfolio_showcase_nonce'),
            'defaultSettings' => array(
                'carousel' => $global_carousel_settings,
                'palette' => $global_palette_settings
            )
        ));
    }

    public function enqueue_admin_assets($hook) {
        // Only load on portfolio post type pages
        if ('post.php' !== $hook && 'post-new.php' !== $hook) {
            return;
        }

        // Enqueue admin CSS
        wp_enqueue_style(
            'portfolio-showcase-admin',
            PORTFOLIO_SHOWCASE_URL . 'assets/css/portfolio-showcase-admin.css',
            array(),
            $this->version
        );

        // Enqueue admin JavaScript
        wp_enqueue_script(
            'portfolio-showcase-admin',
            PORTFOLIO_SHOWCASE_URL . 'assets/js/portfolio-showcase-admin.js',
            array('jquery'),
            $this->version,
            true
        );
    }

    public function render_portfolio($atts) {
        // Parse attributes
        $atts = shortcode_atts(array(
            'id' => 0
        ), $atts);

        // Get project
        $project = get_post($atts['id']);
        if (!$project || $project->post_type !== 'portfolio_project') {
            return '';
        }

        // Get project data
        $carousel_images = get_post_meta($project->ID, '_portfolio_carousel_images', true);
        $carousel_settings = get_post_meta($project->ID, '_portfolio_carousel_settings', true);
        $color_palette = get_post_meta($project->ID, '_portfolio_color_palette', true);
        $palette_settings = get_post_meta($project->ID, '_portfolio_palette_settings', true);

        // Start output buffering
        ob_start();

        // Project container
        echo '<div class="portfolio-showcase">';

        // Project title
        if (!empty($project->post_title)) {
            echo '<h2 class="portfolio-title">' . esc_html($project->post_title) . '</h2>';
        }

        // Project description
        if (!empty($project->post_content)) {
            echo '<div class="portfolio-description">' . wp_kses_post($project->post_content) . '</div>';
        }

        // Render carousel if images exist
        if (!empty($carousel_images)) {
            $this->render_carousel($carousel_images, $carousel_settings);
        }

        // Render color palette if colors exist
        if (!empty($color_palette)) {
            $this->render_color_palette($project->ID);
        }

        echo '</div>';

        // Return the buffered content
        return ob_get_clean();
    }

    private function render_carousel($images, $settings) {
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
        
        $settings = wp_parse_args($settings, $default_settings);
        
        // Find main image
        $main_image = null;
        foreach ($images as $image) {
            if (!empty($image['is_main'])) {
                $main_image = $image;
                break;
            }
        }

        // If no main image is set, use the first one
        if (!$main_image && !empty($images)) {
            $main_image = reset($images);
        }

        // Start carousel container
        echo '<div class="portfolio-carousel" data-settings=\'' . esc_attr(json_encode($settings)) . '\'>';
        echo '<div class="carousel-container">';
        
        // Définir la position de la description en dehors de la boucle
        $description_position = isset($settings['local-carousel-position-description']) ? $settings['local-carousel-position-description'] : 'bottom';
        
        // Render slides
        foreach ($images as $index => $image) {
            $img_src = wp_get_attachment_image_src($image['id'], 'large');
            if (!$img_src) continue;

            $is_active = ($image === $main_image) ? ' active' : '';
            echo '<div class="carousel-slide' . $is_active . '" data-index="' . esc_attr($index) . '">';

            // Placer la description en haut si la position est "top"
            if (!empty($image['description']) && $description_position === 'top') {
                echo '<div class="carousel-slide-description top" data-description-index="' . esc_attr($index) . '" data-description-position="' . esc_attr($description_position) . '">' . wp_kses_post($image['description']) . '</div>';
            }
            
            // Créer un conteneur pour l'image et le titre
            echo '<div class="carousel-image-container">';
            
            // Déterminer si le titre doit être placé avant ou après l'image
            $title_position = isset($settings['local-carousel-position-title']) ? $settings['local-carousel-position-title'] : 'top-left';
            $is_top_position = strpos($title_position, 'top') === 0;
            
            // Placer le titre avant l'image si la position est "top"
            if ($is_top_position && !empty($image['title'])) {
                echo '<div class="carousel-title-container ' . esc_attr($title_position) . '">';
                echo '<h3 class="carousel-title">' . esc_html($image['title']) . '</h3>';
                echo '</div>';
            }
            
            // Image
            echo '<img src="' . esc_url($img_src[0]) . '" alt="' . esc_attr($image['title']) . '">';
            
            // Placer le titre après l'image si la position est "bottom"
            if (!$is_top_position && !empty($image['title'])) {
                echo '<div class="carousel-title-container ' . esc_attr($title_position) . '">';
                echo '<h3 class="carousel-title">' . esc_html($image['title']) . '</h3>';
                echo '</div>';
            }
            
            // Fermer le conteneur d'image
            echo '</div>';

            // Placer la description en bas si la position est "bottom"
            if (!empty($image['description']) && $description_position === 'bottom') {
                echo '<div class="carousel-slide-description bottom" data-description-index="' . esc_attr($index) . '" data-description-position="' . esc_attr($description_position) . '">' . wp_kses_post($image['description']) . '</div>';
            }
            
            echo '</div>';
        }
        
        echo '</div>'; // End carousel-container
        
        // Navigation buttons
        
        // Thumbnails are now added by JavaScript

        echo '</div>'; // End portfolio-carousel
    }

    private function render_color_palette($post_id) {
        $colors = get_post_meta($post_id, '_portfolio_color_palette', true);
        $palette_settings = get_post_meta($post_id, '_portfolio_palette_settings', true);
        
        if (empty($colors) || !is_array($colors)) {
            return;
        }
        
        // Get global settings
        $settings_manager = new Portfolio_Settings();
        $global_settings = $settings_manager->get_palette_settings();
        
        // Default settings
        $default_settings = array(
            'local-palette-height-rectangle' => $global_settings['local-palette-height-rectangle'],
            'local-palette-position-comment' => $global_settings['local-palette-position-comment'],
            'local-palette-color-comment' => $global_settings['local-palette-color-comment']
        );
        
        // Merge with local settings
        $settings = wp_parse_args($palette_settings, $default_settings);

        $comment_position = $settings['local-palette-position-comment'];
        $rectangle_height = $settings['local-palette-height-rectangle'];
        
        // Start color palette container
        echo '<div class="portfolio-color-palette" data-settings=\'' . esc_attr(json_encode($settings)) . '\'>';
        
        foreach ($colors as $color) {
            if (isset($color['color'])) {
                echo '<div class="color-item">';
                echo '<div title="' . esc_attr(htmlspecialchars($color['color'])) . '" class="color-rectangle" style="background-color: ' . esc_attr($color['color']) . '; height: ' . esc_attr($rectangle_height) . 'px;"></div>';
                if (!empty($color['comment'])) {
                    echo '<div title="' . esc_attr(htmlspecialchars($color['comment'])) . '" class="color-comment ' . esc_attr($comment_position) . '">' . esc_html($color['comment']) . '</div>';
                }
                echo '</div>';

            }
        }
        
        echo '</div>';
    }
} 