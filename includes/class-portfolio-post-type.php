<?php
class Portfolio_Post_Type {
    private $post_type = 'portfolio_project';

    public function init() {
        add_action('init', array($this, 'register_post_type'));
    }

    public function register_post_type() {
        $labels = array(
            'name'                  => _x('Portfolio Projects', 'Post Type General Name', 'portfolio-showcase'),
            'singular_name'         => _x('Portfolio Project', 'Post Type Singular Name', 'portfolio-showcase'),
            'menu_name'             => __('Portfolio', 'portfolio-showcase'),
            'name_admin_bar'        => __('Portfolio Project', 'portfolio-showcase'),
            'archives'              => __('Project Archives', 'portfolio-showcase'),
            'attributes'            => __('Project Attributes', 'portfolio-showcase'),
            'parent_item_colon'     => __('Parent Project:', 'portfolio-showcase'),
            'all_items'             => __('All Projects', 'portfolio-showcase'),
            'add_new_item'          => __('Add New Project', 'portfolio-showcase'),
            'add_new'               => __('Add New', 'portfolio-showcase'),
            'new_item'              => __('New Project', 'portfolio-showcase'),
            'edit_item'             => __('Edit Project', 'portfolio-showcase'),
            'update_item'           => __('Update Project', 'portfolio-showcase'),
            'view_item'             => __('View Project', 'portfolio-showcase'),
            'view_items'            => __('View Projects', 'portfolio-showcase'),
            'search_items'          => __('Search Project', 'portfolio-showcase'),
            'not_found'             => __('Not found', 'portfolio-showcase'),
            'not_found_in_trash'    => __('Not found in Trash', 'portfolio-showcase'),
            'featured_image'        => __('Project Image', 'portfolio-showcase'),
            'set_featured_image'    => __('Set project image', 'portfolio-showcase'),
            'remove_featured_image' => __('Remove project image', 'portfolio-showcase'),
            'use_featured_image'    => __('Use as project image', 'portfolio-showcase'),
        );

        $args = array(
            'label'                 => __('Portfolio Project', 'portfolio-showcase'),
            'labels'                => $labels,
            'supports'              => array('title', 'editor', 'thumbnail'),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'              => true,
            'show_in_menu'         => true,
            'menu_position'        => 5,
            'menu_icon'            => 'dashicons-portfolio',
            'show_in_admin_bar'    => true,
            'show_in_nav_menus'    => true,
            'can_export'           => true,
            'has_archive'          => true,
            'exclude_from_search'  => false,
            'publicly_queryable'   => true,
            'capability_type'      => 'post',
            'show_in_rest'         => true,
            'rewrite'              => array('slug' => 'portfolio')
        );

        register_post_type($this->post_type, $args);
    }

    public function get_post_type() {
        return $this->post_type;
    }
} 