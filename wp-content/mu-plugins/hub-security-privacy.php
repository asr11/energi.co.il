<?php
/**
 * Plugin Name: HUB Israeli Regulatory Privacy & Security Hardening
 * Description: Enforces Israeli Protection of Privacy Law (2017) & Cyber Security standards.
 * Version: 1.0.0
 * Author: HUB Advanced Systems
 */

if (!defined('ABSPATH')) {
    exit;
}

// 1. Disable XML-RPC completely
add_filter('xmlrpc_enabled', '__return_false');
add_filter('wp_headers', function($headers) {
    unset($headers['X-Pingback']);
    return $headers;
});

// 2. Enforce Security HTTP Headers (Israeli Privacy & Cyber Bureau Guidelines)
add_action('send_headers', function() {
    if (!headers_sent()) {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
    }
});

// 3. Remove WordPress Generator & Version Leakage
remove_action('wp_head', 'wp_generator');
add_filter('the_generator', '__return_empty_string');
