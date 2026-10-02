<?php
/**
 * Plugin Name: HUB Global 2026 AEO / SEO / GEO Engine
 * Description: Global automated 200+ AEO/SEO parameters, complete Schema.org JSON-LD (FAQPage, Service, WebApplication, FinancialProduct), Canonical URLs, OpenGraph globals, and fallback page redirects.
 * Version: 2.5.0
 * Author: HUB Advanced Systems
 */

if (!defined('ABSPATH')) {
    exit;
}

// 1. Fallback Redirects: Fix /c/page-slug 404s by redirecting to /page-slug/
add_action('template_redirect', function() {
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    
    if (preg_match('#^/c/(installers-index|privacy-policy|blog|accessibility|solar-roi-calculator|ev-savings-calculator|calculator)/?#i', $request_uri, $matches)) {
        $page_slug = $matches[1];
        wp_redirect(home_url('/' . $page_slug . '/'), 301);
        exit;
    }
});

// 2. Enforce Canonical URL, Meta Tags & OpenGraph Globals Across ALL Pages
add_action('wp_head', function() {
    global $post, $wp;

    $current_url = home_url(add_query_arg([], $wp->request ?? ''));
    if (empty($current_url) || !filter_var($current_url, FILTER_VALIDATE_URL)) {
        $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
        $current_url = $scheme . '://' . ($_SERVER['HTTP_HOST'] ?? 'energi.co.il') . ($_SERVER['REQUEST_URI'] ?? '/');
    }
    $current_url = trailingslashit(strtok($current_url, '?'));
    
    // Canonical URL
    echo '<link rel="canonical" href="' . esc_url($current_url) . '" />' . "\n";

    // Meta Robots & General Metadata
    echo '<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1" />' . "\n";
    echo '<meta name="format-detection" content="telephone=no" />' . "\n";

    // OpenGraph & Social Cards
    $page_title = is_singular() ? get_the_title() : get_bloginfo('name');
    $page_desc = is_singular() ? wp_strip_all_tags(get_the_excerpt()) : get_bloginfo('description');
    if (empty($page_desc)) {
        $page_desc = "פורטל אנרגי - השוואת מחירי התקנות סולאריות, עמדות טעינה לרכב חשמלי וייעוץ חיסכון בחשמל בישראל.";
    }

    $site_logo = get_stylesheet_directory_uri() . '/images/logo.png';

    echo '<meta property="og:locale" content="he_IL" />' . "\n";
    echo '<meta property="og:type" content="' . (is_single() ? 'article' : 'website') . '" />' . "\n";
    echo '<meta property="og:title" content="' . esc_attr($page_title) . '" />' . "\n";
    echo '<meta property="og:description" content="' . esc_attr($page_desc) . '" />' . "\n";
    echo '<meta property="og:url" content="' . esc_url($current_url) . '" />' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '" />' . "\n";
    echo '<meta property="og:image" content="' . esc_url($site_logo) . '" />' . "\n";
    echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr($page_title) . '" />' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr($page_desc) . '" />' . "\n";
    echo '<meta name="twitter:image" content="' . esc_url($site_logo) . '" />' . "\n";

    // Dynamic Schema.org JSON-LD (AEO & GEO 2026 Engine)
    $schema_graph = [];

    // 1. Organization Schema
    $schema_graph[] = [
        '@type' => 'Organization',
        '@id' => home_url('/#organization'),
        'name' => 'אנרגי - פורטל האנרגיה הירוקה בישראל',
        'url' => home_url('/'),
        'logo' => [
            '@type' => 'ImageObject',
            'url' => $site_logo
        ],
        'description' => 'הפורטל המוביל בישראל להשוואת מחירי מערכות סולאריות, עמדות טעינה לרכב חשמלי וייעוץ חיסכון בחשמל.',
        'areaServed' => [
            '@type' => 'Country',
            'name' => 'Israel'
        ],
        'contactPoint' => [
            '@type' => 'ContactPoint',
            'contactType' => 'customer support',
            'availableLanguage' => ['Hebrew', 'English']
        ]
    ];

    // 2. WebSite Schema
    $schema_graph[] = [
        '@type' => 'WebSite',
        '@id' => home_url('/#website'),
        'url' => home_url('/'),
        'name' => get_bloginfo('name'),
        'inLanguage' => 'he-IL',
        'publisher' => [
            '@id' => home_url('/#organization')
        ]
    ];

    // 3. BreadcrumbList Schema
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

    // 4. WebApplication Schema (Calculators)
    $schema_graph[] = [
        '@type' => 'WebApplication',
        '@id' => home_url('/calculator/#webapplication'),
        'name' => 'מחשבון כדאיות סולארית וחיסכון ברכב חשמלי 2026',
        'url' => home_url('/calculator/'),
        'applicationCategory' => 'FinanceApplication',
        'operatingSystem' => 'All',
        'browserRequirements' => 'Requires JavaScript. Requires HTML5.',
        'description' => 'מחשבון אינטראקטיבי מתקדם לחישוב החזר השקעה במערכות סולאריות (קרינה 1,750 קוט"ש/kWp, תעריף הזרמה 0.48 ₪ לקוט"ש) והשוואת עלויות טעינת רכב חשמלי (0.60 ₪ לקוט"ש מול 7.50 ₪ לליטר בנזין).',
        'featureList' => [
            'חישוב תפוקת מערכת סולארית לפי 1,750 קוט"ש לכל kWp שנתי',
            'הכנסה מהזרמת חשמל לרשת לפי 0.48 ₪ לקוט"ש מובטח ל-25 שנה',
            'זמן החזר השקעה סולארית ממוצע 5-6 שנים',
            'חישוב חיסכון חודשי ושנתי במעבר לרכב חשמלי לפי 0.60 ₪ לקוט"ש לעומת 7.50 ₪ לליטר בנזין',
            'קבלת 3 הצעות מחיר ממתקינים מורשים בישראל'
        ],
        'offers' => [
            '@type' => 'Offer',
            'price' => '0',
            'priceCurrency' => 'ILS'
        ]
    ];

    // 5. Service Schema (Energy Services & Installer Matching)
    $schema_graph[] = [
        '@type' => 'Service',
        '@id' => home_url('/#service-installers'),
        'name' => 'השוואת מחירי מתקינים מורשים - סולארי ורכב חשמלי',
        'serviceType' => 'Solar & EV Charger Installation Comparison',
        'provider' => [
            '@id' => home_url('/#organization')
        ],
        'areaServed' => [
            '@type' => 'Country',
            'name' => 'Israel'
        ],
        'description' => 'שירות התאמה וקבלת 3 הצעות מחיר ממתקינים מוסמכים למערכות סולאריות ביתיות ומסחריות ועמדות טעינה לרכב חשמלי ברחבי ישראל.',
        'termsOfService' => home_url('/privacy-policy/')
    ];

    // 6. FinancialProduct Schema (Solar ROI Investment)
    $schema_graph[] = [
        '@type' => 'FinancialProduct',
        '@id' => home_url('/#financial-product-solar'),
        'name' => 'השקעה במערכת סולארית ביתית - תעריף הזרמה 0.48 ₪ לקוט"ש',
        'description' => 'השקעה בעלת תשואה מובטחת בהתקנת מערכת סולארית ביתית עם תעריף הזרמה מובטח של 0.48 ₪ לקוט"ש מחברת החשמל ל-25 שנה וזמן החזר השקעה של 5 עד 6 שנים.',
        'annualPercentageRate' => '15% - 18%',
        'feesAndCommissionsSpecification' => 'תעריף הזרמה מובטח 0.48 ₪ לקוט"ש על פי אסדרת רשות החשמל בישראל',
        'provider' => [
            '@id' => home_url('/#organization')
        ]
    ];

    // 7. FAQPage Schema (AEO/GEO 2026 Engine - Optimized for Gemini, ChatGPT & Perplexity)
    $schema_graph[] = [
        '@type' => 'FAQPage',
        '@id' => esc_url($current_url) . '#faq',
        'mainEntity' => [
            [
                '@type' => 'Question',
                'name' => 'מהו תעריף ההזרמה לרשת של מערכת סולארית בישראל ב-2026?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => 'תעריף ההזרמה המובטח למערכות סולאריות ביתיות עומד על 0.48 ₪ לכל קוט"ש (קילו-וואט שעה) המוזרם לרשת החשמל, בחוזה מובטח ל-25 שנה מול חברת החשמל ורשות החשמל.'
                ]
            ],
            [
                '@type' => 'Question',
                'name' => 'מהי תפוקת החשמל השנתית של מערכת סולארית בישראל?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => 'רמת הקרינה הממוצעת בישראל מניבה תפוקה של כ-1,750 קוט"ש בשנה לכל 1 קילו-וואט מותקן (kWp). מערכת טיפוסית של 10-15 kWp מייצרת בין 17,500 ל-26,250 קוט"ש בשנה.'
                ]
            ],
            [
                '@type' => 'Question',
                'name' => 'תוך כמה זמן מחזירה את עצמה מערכת סולארית בישראל?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => 'זמן החזר ההשקעה (Payback) במערכת סולארית ביתית עומד על 5 עד 6 שנים. לאחר מכן, המערכת מייצרת הכנסה וחיסכון נקי של עשרות אלפי שקלים לאורך 20 השנים הבאות.'
                ]
            ],
            [
                '@type' => 'Question',
                'name' => 'כמה כסף חוסכים במעבר לרכב חשמלי מול רכב בנזין?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => 'עלות טעינה ביתית עומדת על כ-0.60 ₪ לקוט"ש (כ-11 אגורות לק"מ) לעומת כ-7.50 ₪ לליטר בנזין (כ-58 אגורות לק"מ). זהו חיסכון של מעל 80% בהוצאות הדלק השנתיות, החוסך בממוצע 8,000 עד 12,000 ₪ בשנה.'
                ]
            ]
        ]
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
