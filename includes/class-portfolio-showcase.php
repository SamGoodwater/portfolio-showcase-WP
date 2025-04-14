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

        // Localize script
        wp_localize_script('portfolio-showcase', 'portfolioShowcaseSettings', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('portfolio_showcase_nonce')
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
        $style_options = get_post_meta($project->ID, '_portfolio_style_options', true);

        // Start output buffering
        ob_start();

        // Project container with custom styles
        $custom_style = '';
        if (!empty($style_options['padding'])) {
            $custom_style .= "padding: {$style_options['padding']};";
        }
        if (!empty($style_options['margin'])) {
            $custom_style .= "margin: {$style_options['margin']};";
        }
        
        echo '<div class="portfolio-showcase" style="' . esc_attr($custom_style) . '">';

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
            $this->render_color_palette($color_palette, $palette_settings);
        }

        // Add custom CSS if any
        if (!empty($style_options['custom_css'])) {
            echo '<style type="text/css">' . esc_html($style_options['custom_css']) . '</style>';
        }

        echo '</div>';

        // Return the buffered content
        return ob_get_clean();
    }

    private function render_carousel($images, $settings) {
        // Default settings
        $settings = wp_parse_args($settings, array(
            'enable_fullscreen' => true,
            'description_position' => 'bottom',
            'title_position' => 'top-left',
            'background_color' => '#000000'
        ));

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
        echo '<div class="portfolio-carousel" data-enable-fullscreen="' . 
             esc_attr($settings['enable_fullscreen']) . '" data-background="' . 
             esc_attr($settings['background_color']) . '">';

        // Carousel container
        echo '<div class="carousel-container">';
        
        // Render slides
        foreach ($images as $index => $image) {
            $img_src = wp_get_attachment_image_src($image['id'], 'large');
            if (!$img_src) continue;

            $is_active = ($image === $main_image) ? ' active' : '';
            echo '<div class="carousel-slide' . $is_active . '" data-index="' . esc_attr($index) . '">';
            echo '<img src="' . esc_url($img_src[0]) . '" alt="' . esc_attr($image['title']) . '">';
            
            if (!empty($image['title']) || !empty($image['description'])) {
                echo '<div class="carousel-text">';
                if (!empty($image['title'])) {
                    echo '<h3 class="carousel-title ' . esc_attr($settings['title_position']) . '">' . 
                         esc_html($image['title']) . '</h3>';
                }
                if (!empty($image['description'])) {
                    echo '<div class="carousel-description ' . esc_attr($settings['description_position']) . '">' . 
                         wp_kses_post($image['description']) . '</div>';
                }
                echo '</div>';
            }
            
            echo '</div>';
        }
        
        echo '</div>'; // End carousel-container

        // Navigation buttons are now added by JavaScript
        
        // Thumbnails are now added by JavaScript

        echo '</div>'; // End portfolio-carousel
    }

    private function render_color_palette($colors, $settings) {
        // Default settings
        $settings = wp_parse_args($settings, array(
            'height' => 20,
            'comment_position' => 'bottom'
        ));

        echo '<div class="portfolio-color-palette">';
        
        foreach ($colors as $color) {
            $style = 'height: ' . esc_attr($settings['height']) . 'px; background-color: ' . 
                    esc_attr($color['color']) . ';';
            
            echo '<div class="color-rectangle" data-color="' . esc_attr($color['color']) . 
                 '" style="' . $style . '">';
            
            if (!empty($color['comment'])) {
                echo '<div class="color-comment ' . esc_attr($settings['comment_position']) . '">' . 
                     esc_html($color['comment']) . '</div>';
            }
            
            echo '</div>';
        }
        
        echo '</div>';
    }
} 