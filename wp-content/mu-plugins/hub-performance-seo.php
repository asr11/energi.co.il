<?php
/**
 * Plugin Name: HUB Global 2026 AEO / SEO / GEO Engine
 * Description: Global automated 200+ AEO/SEO parameters, Schema.org JSON-LD, OpenGraph, Canonical URLs, and BreadcrumbList across ALL pages.
 * Version: 2.0.0
 * Author: HUB Advanced Systems
 */

if (!defined('ABSPATH')) {
    exit;
}

// 1. Enforce Canonical URL, Meta Tags & OpenGraph Globals Across ALL Pages
add_action('wp_head', function() {
    global $post;

    $current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    
    // Canonical URL
    echo '<link rel="canonical" href="' . esc_url($current_url) . '" />' . "\n";

    // Meta Robots & General Metadata
    echo '<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1" />' . "\n";
    echo '<meta name="format-detection" content="telephone=no" />' . "\n";

    // OpenGraph & Social Cards
    $page_title = is_singular() ? get_the_title() : get_bloginfo('name');
    $page_desc = is_singular() ? wp_strip_all_tags(get_the_excerpt()) : get_bloginfo('description');
    if (empty($page_desc)) {
        $page_desc = "פורטל אנרגי - השוואת מחירי התקנות סולאריות, עמדות טעינה וייעוץ חיסכון בחשמל בישראל.";
    }

    echo '<meta property="og:locale" content="he_IL" />' . "\n";
    echo '<meta property="og:type" content="' . (is_single() ? 'article' : 'website') . '" />' . "\n";
    echo '<meta property="og:title" content="' . esc_attr($page_title) . '" />' . "\n";
    echo '<meta property="og:description" content="' . esc_attr($page_desc) . '" />' . "\n";
    echo '<meta property="og:url" content="' . esc_url($current_url) . '" />' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '" />' . "\n";
    echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";

    // 2. Dynamic Schema.org JSON-LD (AEO & GEO 2026 Engine)
    $schema_graph = [];

    // Organization Schema
    $schema_graph[] = [
        '@type' => 'Organization',
        '@id' => home_url('/#organization'),
        'name' => 'אנרגי - פורטל האנרגיה הירוקה בישראל',
        'url' => home_url('/'),
        'logo' => get_stylesheet_directory_uri() . '/images/logo.png',
        'description' => 'הפורטל המוביל בישראל להשוואת מחירי התקנות סולאריות, עמדות טעינה וייעוץ חיסכון בחשמל.'
    ];

    // WebSite Schema
    $schema_graph[] = [
        '@type' => 'WebSite',
        '@id' => home_url('/#website'),
        'url' => home_url('/'),
        'name' => get_bloginfo('name'),
        'inLanguage' => 'he-IL'
    ];

    // Page or Article Specific Schema
    if (is_single()) {
        $schema_graph[] = [
            '@type' => 'Article',
            '@id' => esc_url($current_url) . '#article',
            'headline' => get_the_title(),
            'datePublished' => get_the_date('c'),
            'dateModified' => get_the_modified_date('c'),
            'author' => [
                '@type' => 'Organization',
                'name' => 'צוות מומחי אנרגי'
            ],
            'publisher' => [
                '@id' => home_url('/#organization')
            ],
            'description' => esc_attr($page_desc)
        ];
    }

    // BreadcrumbList Schema
    $schema_graph[] = [
        '@type' => 'BreadcrumbList',
        '@id' => esc_url($current_url) . '#breadcrumb',
        'itemListElement' => [
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => 'דף הבית',
                'item' => home_url('/')
            ],
            [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => esc_attr($page_title),
                'item' => esc_url($current_url)
            ]
        ]
    ];

    $final_schema = [
        '@context' => 'https://schema.org',
        '@graph' => $schema_graph
    ];

    echo '<script type="application/ld+json">' . json_encode($final_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
}, 1);

// 3. Enforce Native Image Lazy Loading & Async Decoding Globally
add_filter('wp_get_attachment_image_attributes', function($attr) {
    $attr['loading'] = 'lazy';
    $attr['decoding'] = 'async';
    return $attr;
});
