<?php
/**
 * Plugin Name: Energi Premium Design
 * Description: Injects a breathtaking, modern, glassmorphic UI overlay for the Energi theme.
 * Version: 1.0.0
 * Author: Antigravity AI
 */

if (!defined('ABSPATH')) {
    exit;
}

function energi_premium_enqueue_assets() {
    // Enqueue Google Fonts
    wp_enqueue_style('energi-google-fonts', 'https://fonts.googleapis.com/css2?family=Heebo:wght@300;400;600;800&family=Outfit:wght@400;700&display=swap', array(), null);

    // Enqueue Premium Styles
    wp_enqueue_style('energi-premium-style', plugin_dir_url(__FILE__) . 'assets/premium-style.css', array(), '1.0.0');

    // Enqueue Premium Scripts
    wp_enqueue_script('energi-premium-script', plugin_dir_url(__FILE__) . 'assets/premium-script.js', array('jquery'), '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'energi_premium_enqueue_assets', 999);
