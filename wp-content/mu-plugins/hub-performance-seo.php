<?php
/**
 * Plugin Name: HUB Performance & AEO Schema Booster (2026)
 * Description: Automatic Schema.org JSON-LD structured data & AEO optimization.
 * Version: 1.0.0
 * Author: HUB Advanced Systems
 */

if (!defined('ABSPATH')) {
    exit;
}

// Inject Schema.org JSON-LD for Organization & Energy Service (AEO / GEO 2026 Optimization)
add_action('wp_head', function() {
    if (is_front_page() || is_home()) {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => 'אנרגי - פורטל האנרגיה והתקנות סולאריות בישראל',
            'url' => home_url('/'),
            'logo' => get_stylesheet_directory_uri() . '/images/logo.png',
            'description' => 'הפורטל המוביל בישראל להשוואת מחירי התקנות סולאריות, עמדות טעינה וייעוץ חיסכון בחשמל.',
            'address' => [
                '@type' => 'PostalAddress',
                'addressCountry' => 'IL'
            ],
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'contactType' => 'customer service',
                'areaServed' => 'IL',
                'availableLanguage' => 'Hebrew'
            ]
        ];
        echo '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
    }
});
