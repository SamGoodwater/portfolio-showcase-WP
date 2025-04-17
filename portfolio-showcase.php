<?php
/*
* Plugin Name: Portfolio Showcase
* Plugin URI: 
* Description: A modern portfolio showcase plugin with carousel and color palette features
* Version: 1.0.2
* Author: Goodwater
* Text Domain: portfolio-showcase
* Domain Path: /languages
* Requires at least: 6.0
* Requires PHP: 7.4
*/

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin constants
define('PORTFOLIO_SHOWCASE_VERSION', '1.0.0');
define('PORTFOLIO_SHOWCASE_PATH', plugin_dir_path(__FILE__));
define('PORTFOLIO_SHOWCASE_URL', plugin_dir_url(__FILE__));

// Load plugin text domain for translations
function portfolio_showcase_load_textdomain() {
    load_plugin_textdomain(
        'portfolio-showcase',
        false,
        dirname(plugin_basename(__FILE__)) . '/languages'
    );
}
add_action('plugins_loaded', 'portfolio_showcase_load_textdomain');

// Include required files
require_once PORTFOLIO_SHOWCASE_PATH . 'includes/class-portfolio-showcase.php';
require_once PORTFOLIO_SHOWCASE_PATH . 'includes/class-portfolio-post-type.php';
require_once PORTFOLIO_SHOWCASE_PATH . 'includes/class-portfolio-metaboxes.php';
require_once PORTFOLIO_SHOWCASE_PATH . 'includes/class-portfolio-settings.php';

// Initialize the plugin
function run_portfolio_showcase() {
    $plugin = new Portfolio_Showcase();
    $plugin->run();
}

// Hook into WordPress
add_action('plugins_loaded', 'run_portfolio_showcase');

// Activation hook
register_activation_hook(__FILE__, function() {
    // Flush rewrite rules on activation
    flush_rewrite_rules();
});

// Deactivation hook
register_deactivation_hook(__FILE__, function() {
    // Clean up if necessary
    flush_rewrite_rules();
}); 