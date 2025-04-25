<?php
class Portfolio_Settings {
    private $option_name = 'portfolio_showcase_global_settings';
    private $default_settings;

    public function __construct() {
        $this->default_settings = array(
            // Carousel settings
            'global-carousel-color-title' => '#f5f5f5',
            'global-carousel-color-title-fullscreen' => '#f5f5f5',
            'global-carousel-color-description' => '#2e3d38',
            'global-carousel-color-description-fullscreen' => '#f2f7f5',
            'global-carousel-enable-fullscreen' => true,
            'global-carousel-color-background' => '#f5f5f5',
            'global-carousel-color-background-fullscreen' => '#121212',
            'global-carousel-opacity-background-fullscreen' => 90,
            'global-carousel-position-title' => 'top-left',
            'global-carousel-position-description' => 'bottom',
            'global-carousel-width' => '100%',
            'global-carousel-height' => '500px',
            
            // Palette settings
            'global-palette-height-rectangle' => 15,
            'global-palette-position-comment' => 'bottom',
            'global-palette-color-comment' => '#2e3d38'
        );
    }

    public function init() {
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function add_settings_page() {
        add_submenu_page(
            'edit.php?post_type=portfolio_project',
            __('Portfolio Showcase Settings', 'portfolio-showcase'),
            __('Settings', 'portfolio-showcase'),
            'manage_options',
            'portfolio-showcase-settings',
            array($this, 'render_settings_page')
        );
    }

    public function register_settings() {
        register_setting(
            'portfolio_showcase_settings',
            $this->option_name,
            array($this, 'sanitize_settings')
        );

        // Carousel Settings Section
        add_settings_section(
            'portfolio_showcase_carousel_settings',
            __('Carousel Settings', 'portfolio-showcase'),
            array($this, 'render_carousel_section'),
            'portfolio-showcase-settings'
        );

        // Palette Settings Section
        add_settings_section(
            'portfolio_showcase_palette_settings',
            __('Color Palette Settings', 'portfolio-showcase'),
            array($this, 'render_palette_section'),
            'portfolio-showcase-settings'
        );

        // Carousel Fields
        $this->add_carousel_fields();

        // Palette Fields
        $this->add_palette_fields();
    }

    private function add_carousel_fields() {
        // Title Color
        add_settings_field(
            'global-carousel-color-title',
            __('Title Color', 'portfolio-showcase'),
            array($this, 'render_color_field'),
            'portfolio-showcase-settings',
            'portfolio_showcase_carousel_settings',
            array('field' => 'global-carousel-color-title')
        );

        // Title Color Fullscreen
        add_settings_field(
            'global-carousel-color-title-fullscreen',
            __('Title Color (Fullscreen)', 'portfolio-showcase'),
            array($this, 'render_color_field'),
            'portfolio-showcase-settings',
            'portfolio_showcase_carousel_settings',
            array('field' => 'global-carousel-color-title-fullscreen')
        );

        // Description Color
        add_settings_field(
            'global-carousel-color-description',
            __('Description Color', 'portfolio-showcase'),
            array($this, 'render_color_field'),
            'portfolio-showcase-settings',
            'portfolio_showcase_carousel_settings',
            array('field' => 'global-carousel-color-description')
        );

        // Description Color Fullscreen
        add_settings_field(
            'global-carousel-color-description-fullscreen',
            __('Description Color (Fullscreen)', 'portfolio-showcase'),
            array($this, 'render_color_field'),
            'portfolio-showcase-settings',
            'portfolio_showcase_carousel_settings',
            array('field' => 'global-carousel-color-description-fullscreen')
        );

        // Enable Fullscreen
        add_settings_field(
            'global-carousel-enable-fullscreen',
            __('Enable Fullscreen', 'portfolio-showcase'),
            array($this, 'render_checkbox_field'),
            'portfolio-showcase-settings',
            'portfolio_showcase_carousel_settings',
            array('field' => 'global-carousel-enable-fullscreen')
        );

        // Background Color
        add_settings_field(
            'global-carousel-color-background',
            __('Background Color', 'portfolio-showcase'),
            array($this, 'render_color_field'),
            'portfolio-showcase-settings',
            'portfolio_showcase_carousel_settings',
            array('field' => 'global-carousel-color-background')
        );

        // Background Color Fullscreen
        add_settings_field(
            'global-carousel-color-background-fullscreen',
            __('Background Color (Fullscreen)', 'portfolio-showcase'),
            array($this, 'render_color_field'),
            'portfolio-showcase-settings',
            'portfolio_showcase_carousel_settings',
            array('field' => 'global-carousel-color-background-fullscreen')
        );

        // Background Opacity Fullscreen
        add_settings_field(
            'global-carousel-opacity-background-fullscreen',
            __('Background Opacity (Fullscreen)', 'portfolio-showcase'),
            array($this, 'render_range_field'),
            'portfolio-showcase-settings',
            'portfolio_showcase_carousel_settings',
            array('field' => 'global-carousel-opacity-background-fullscreen', 'min' => 0, 'max' => 100)
        );

        // Title Position
        add_settings_field(
            'global-carousel-position-title',
            __('Title Position', 'portfolio-showcase'),
            array($this, 'render_select_field'),
            'portfolio-showcase-settings',
            'portfolio_showcase_carousel_settings',
            array(
                'field' => 'global-carousel-position-title',
                'options' => array(
                    'top-left' => __('Top Left', 'portfolio-showcase'),
                    'top-right' => __('Top Right', 'portfolio-showcase'),
                    'top-center' => __('Top Center', 'portfolio-showcase'),
                    'bottom-left' => __('Bottom Left', 'portfolio-showcase'),
                    'bottom-right' => __('Bottom Right', 'portfolio-showcase'),
                    'bottom-center' => __('Bottom Center', 'portfolio-showcase'),
                    'center' => __('Center', 'portfolio-showcase')
                )
            )
        );

        // Description Position
        add_settings_field(
            'global-carousel-position-description',
            __('Description Position', 'portfolio-showcase'),
            array($this, 'render_select_field'),
            'portfolio-showcase-settings',
            'portfolio_showcase_carousel_settings',
            array(
                'field' => 'global-carousel-position-description',
                'options' => array(
                    'top' => __('Top', 'portfolio-showcase'),
                    'bottom' => __('Bottom', 'portfolio-showcase')
                )
            )
        );

        // Carousel Width
        add_settings_field(
            'global-carousel-width',
            __('Carousel Width', 'portfolio-showcase'),
            array($this, 'render_size_field'),
            'portfolio-showcase-settings',
            'portfolio_showcase_carousel_settings',
            array('field' => 'global-carousel-width')
        );

        // Carousel Height
        add_settings_field(
            'global-carousel-height',
            __('Carousel Height', 'portfolio-showcase'),
            array($this, 'render_size_field'),
            'portfolio-showcase-settings',
            'portfolio_showcase_carousel_settings',
            array('field' => 'global-carousel-height')
        );
    }

    private function add_palette_fields() {
        // Rectangle Size
        add_settings_field(
            'global-palette-height-rectangle',
            __('Rectangle Size (px)', 'portfolio-showcase'),
            array($this, 'render_number_field'),
            'portfolio-showcase-settings',
            'portfolio_showcase_palette_settings',
            array('field' => 'global-palette-height-rectangle', 'min' => 5, 'max' => 100)
        );

        // Comment Position
        add_settings_field(
            'global-palette-position-comment',
            __('Comment Position', 'portfolio-showcase'),
            array($this, 'render_select_field'),
            'portfolio-showcase-settings',
            'portfolio_showcase_palette_settings',
            array(
                'field' => 'global-palette-position-comment',
                'options' => array(
                    'top' => __('Top', 'portfolio-showcase'),
                    'bottom' => __('Bottom', 'portfolio-showcase')
                )
            )
        );
        
        // Comment Color
        add_settings_field(
            'global-palette-color-comment',
            __('Comment Color', 'portfolio-showcase'),
            array($this, 'render_color_field'),
            'portfolio-showcase-settings',
            'portfolio_showcase_palette_settings',
            array('field' => 'global-palette-color-comment')
        );
    }

    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields('portfolio_showcase_settings');
                do_settings_sections('portfolio-showcase-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function render_carousel_section() {
        echo '<p>' . __('Configure the default carousel settings for all portfolio projects.', 'portfolio-showcase') . '</p>';
    }

    public function render_palette_section() {
        echo '<p>' . __('Configure the default color palette settings for all portfolio projects.', 'portfolio-showcase') . '</p>';
    }

    public function render_color_field($args) {
        $field = $args['field'];
        $settings = $this->get_settings();
        $value = isset($settings[$field]) ? $settings[$field] : $this->default_settings[$field];
        ?>
        <input type="color" name="<?php echo esc_attr($this->option_name . '[' . $field . ']'); ?>" 
               value="<?php echo esc_attr($value); ?>">
        <?php
    }

    public function render_checkbox_field($args) {
        $field = $args['field'];
        $settings = $this->get_settings();
        $value = isset($settings[$field]) ? $settings[$field] : $this->default_settings[$field];
        ?>
        <input type="checkbox" name="<?php echo esc_attr($this->option_name . '[' . $field . ']'); ?>" 
               value="1" <?php checked(1, $value); ?>>
        <?php
    }

    public function render_range_field($args) {
        $field = $args['field'];
        $min = isset($args['min']) ? $args['min'] : 0;
        $max = isset($args['max']) ? $args['max'] : 100;
        $settings = $this->get_settings();
        $value = isset($settings[$field]) ? $settings[$field] : $this->default_settings[$field];
        ?>
        <input type="range" name="<?php echo esc_attr($this->option_name . '[' . $field . ']'); ?>" 
               min="<?php echo esc_attr($min); ?>" max="<?php echo esc_attr($max); ?>" 
               value="<?php echo esc_attr($value); ?>">
        <span class="range-value"><?php echo esc_html($value); ?></span>
        <?php
    }

    public function render_number_field($args) {
        $field = $args['field'];
        $min = isset($args['min']) ? $args['min'] : 0;
        $max = isset($args['max']) ? $args['max'] : 100;
        $settings = $this->get_settings();
        $value = isset($settings[$field]) ? $settings[$field] : $this->default_settings[$field];
        ?>
        <input type="number" name="<?php echo esc_attr($this->option_name . '[' . $field . ']'); ?>" 
               min="<?php echo esc_attr($min); ?>" max="<?php echo esc_attr($max); ?>" 
               value="<?php echo esc_attr($value); ?>">
        <?php
    }

    public function render_select_field($args) {
        $field = $args['field'];
        $options = $args['options'];
        $settings = $this->get_settings();
        $value = isset($settings[$field]) ? $settings[$field] : $this->default_settings[$field];
        ?>
        <select name="<?php echo esc_attr($this->option_name . '[' . $field . ']'); ?>">
            <?php foreach ($options as $option_value => $option_label) : ?>
                <option value="<?php echo esc_attr($option_value); ?>" <?php selected($option_value, $value); ?>>
                    <?php echo esc_html($option_label); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php
    }

    public function render_size_field($args) {
        $settings = $this->get_settings();
        $field = $args['field'];
        $value = isset($settings[$field]) ? $settings[$field] : $this->default_settings[$field];
        ?>
        <input type="text" 
               name="<?php echo esc_attr($this->option_name . '[' . $field . ']'); ?>" 
               value="<?php echo esc_attr($value); ?>"
               class="portfolio-showcase-size-field"
               placeholder="e.g. 100%, 500px, 20rem">
        <p class="description"><?php _e('Enter a valid CSS size value (e.g. 100%, 500px, 20rem)', 'portfolio-showcase'); ?></p>
        <?php
    }

    public function sanitize_settings($input) {
        $sanitized = array();
        
        // Carousel settings
        $sanitized['global-carousel-color-title'] = sanitize_hex_color($input['global-carousel-color-title']);
        $sanitized['global-carousel-color-title-fullscreen'] = sanitize_hex_color($input['global-carousel-color-title-fullscreen']);
        $sanitized['global-carousel-color-description'] = sanitize_hex_color($input['global-carousel-color-description']);
        $sanitized['global-carousel-color-description-fullscreen'] = sanitize_hex_color($input['global-carousel-color-description-fullscreen']);
        $sanitized['global-carousel-enable-fullscreen'] = isset($input['global-carousel-enable-fullscreen']);
        $sanitized['global-carousel-color-background'] = sanitize_hex_color($input['global-carousel-color-background']);
        $sanitized['global-carousel-color-background-fullscreen'] = sanitize_hex_color($input['global-carousel-color-background-fullscreen']);
        $sanitized['global-carousel-opacity-background-fullscreen'] = absint($input['global-carousel-opacity-background-fullscreen']);
        $sanitized['global-carousel-position-title'] = sanitize_text_field($input['global-carousel-position-title']);
        $sanitized['global-carousel-position-description'] = sanitize_text_field($input['global-carousel-position-description']);
        
        // Palette settings
        $sanitized['global-palette-height-rectangle'] = absint($input['global-palette-height-rectangle']);
        $sanitized['global-palette-position-comment'] = sanitize_text_field($input['global-palette-position-comment']);
        $sanitized['global-palette-color-comment'] = sanitize_hex_color($input['global-palette-color-comment']);
        
        // Sanitize size fields
        if (isset($input['global-carousel-width'])) {
            $sanitized['global-carousel-width'] = $this->sanitize_size_value($input['global-carousel-width']);
        }
        if (isset($input['global-carousel-height'])) {
            $sanitized['global-carousel-height'] = $this->sanitize_size_value($input['global-carousel-height']);
        }

        return $sanitized;
    }

    private function sanitize_size_value($value) {
        // Regex pattern for CSS size values
        $pattern = '/^(\d+(?:\.\d+)?)(px|%|em|rem|vh|vw)$/';
        if (preg_match($pattern, $value)) {
            return $value;
        }
        return $this->default_settings['global-carousel-width']; // Return default if invalid
    }

    public function get_settings() {
        $settings = get_option($this->option_name, array());
        return wp_parse_args($settings, $this->default_settings);
    }

    public function get_carousel_settings() {
        $settings = $this->get_settings();
        return array(
            'local-carousel-enable-fullscreen' => $settings['global-carousel-enable-fullscreen'],
            'local-carousel-position-description' => $settings['global-carousel-position-description'],
            'local-carousel-position-title' => $settings['global-carousel-position-title'],
            'local-carousel-color-background-fullscreen' => $settings['global-carousel-color-background-fullscreen'],
            'local-carousel-color-background' => $settings['global-carousel-color-background'],
            'local-carousel-color-title' => $settings['global-carousel-color-title'],
            'local-carousel-color-description' => $settings['global-carousel-color-description'],
            'local-carousel-opacity-background-fullscreen' => $settings['global-carousel-opacity-background-fullscreen'],
            'local-carousel-color-description-fullscreen' => $settings['global-carousel-color-description-fullscreen'],
            'local-carousel-color-title-fullscreen' => $settings['global-carousel-color-title-fullscreen'],
            'local-carousel-width' => $settings['global-carousel-width'],
            'local-carousel-height' => $settings['global-carousel-height']
        );
    }

    public function get_palette_settings() {
        $settings = $this->get_settings();
        return array(
            'local-palette-height-rectangle' => $settings['global-palette-height-rectangle'],
            'local-palette-position-comment' => $settings['global-palette-position-comment'],
            'local-palette-color-comment' => $settings['global-palette-color-comment']
        );
    }
} 