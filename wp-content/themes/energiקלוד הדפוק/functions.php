<?php
/**
 * Energi Theme Core Functions
 * 
 * @package Energi
 * @version 3.0.0
 */

defined('ABSPATH') || exit;

// Core Constants
define('ENERGI_VERSION', '3.0.0');
define('ENERGI_PATH', get_template_directory());
define('ENERGI_URL', get_template_directory_uri());
define('ENERGI_INC', ENERGI_PATH . '/inc');

/**
 * Energi Theme Setup
 */
class Energi_Theme {
    
    public function __construct() {
        add_action('after_setup_theme', [$this, 'setup']);
        add_action('wp_enqueue_scripts', [$this, 'scripts']);
        $this->load_modules();
    }
    
    /**
     * Theme Setup
     */
    public function setup() {
        add_theme_support('post-thumbnails');
        add_theme_support('title-tag');
        add_theme_support('html5', ['search-form', 'comment-form']);
        
        register_nav_menus([
            'primary' => __('Primary Menu', 'energi'),
            'footer' => __('Footer Menu', 'energi')
        ]);
    }
    
    /**
     * Load Scripts & Styles
     */
    public function scripts() {
        wp_enqueue_style('energi-style', get_stylesheet_uri(), [], ENERGI_VERSION);
        wp_enqueue_script('energi-main', ENERGI_URL . '/assets/js/main.js', ['jquery'], ENERGI_VERSION, true);
    }
    
    /**
     * Load Core Modules
     */
    private function load_modules() {
        require_once ENERGI_INC . '/class-energi-core.php';
        require_once ENERGI_INC . '/class-energi-leads.php';
        require_once ENERGI_INC . '/class-energi-cta.php';
        require_once ENERGI_INC . '/class-energi-admin.php';
        
        // Initialize
        new Energi_Core();
        new Energi_Leads();
        new Energi_CTA();
        new Energi_Admin();
    }
}

// Start the engine
new Energi_Theme();