<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="<?php bloginfo('charset'); ?>">
    
    <!-- SEO אופטימיזציה -->
    <title><?php 
        if (is_home()) {
            echo get_bloginfo('name') . ' - ' . get_bloginfo('description');
        } else {
            wp_title('|', true, 'left'); 
            echo ' | ' . get_bloginfo('name');
        }
    ?></title>
    
    <meta name="description" content="<?php 
        if (is_single() || is_page()) {
            echo wp_strip_all_tags(get_the_excerpt());
        } else {
            echo 'פורטל האנרגיה הירוקה המוביל בישראל - מחשבון חיסכון, הצעות מחיר ופתרונות אנרגיה מתקדמים';
        }
    ?>">
    
    <!-- Open Graph למדיה חברתית -->
    <meta property="og:title" content="<?php wp_title(''); ?> | <?php bloginfo('name'); ?>">
    <meta property="og:description" content="פתרונות אנרגיה ירוקה וחיסכון בחשמל">
    <meta property="og:image" content="<?php echo get_template_directory_uri(); ?>/images/logo-social.png">
    <meta property="og:url" content="<?php echo get_permalink(); ?>">
    <meta property="og:type" content="website">
    
    <!-- טעינת עיצובים -->
    <link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>?v=<?php echo filemtime(get_template_directory() . '/style.css'); ?>">
    
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <!-- Google Analytics או Pixel -->
    <?php if (is_page('calculator') || is_page_template('page-calculator.php')): ?>
    <script>
        // Event tracking למחשבון
        gtag('event', 'page_view', {
            'page_title': 'מחשבון חיסכון באנרגיה',
            'page_location': window.location.href
        });
    </script>
    <?php endif; ?>

    <div align="center">
        <div id="inner">
            <header>
                <!-- לוגו עם קישור לעמוד הבית -->
                <div class="header-content">
                    <a href="<?php echo esc_url(home_url('/')); ?>" title="<?php echo esc_attr(get_bloginfo('name')); ?> - עמוד הבית">
                        <img src="<?php echo get_template_directory_uri(); ?>/images/logo.png" 
                             alt="<?php echo esc_attr(get_bloginfo('name')); ?> - פתרונות אנרגיה ירוקה" />
                    </a>
                    
                    <!-- כותרת אתר (נסתרת אבל SEO) -->
                    <h1 style="display:none;"><?php bloginfo('name'); ?> - <?php bloginfo('description'); ?></h1>
                    
                    <!-- תיבת חיפוש -->
                    <div class="search-area">
                        <?php get_search_form(); ?>
                    </div>
                </div>

                <!-- תפריט ניווט ראשי -->
                <nav class="main-navigation">
                    <?php 
                    wp_nav_menu(array(
                        'theme_location' => 'primary',
                        'container' => false,
                        'menu_class' => 'main-menu',
                        'fallback_cb' => 'energi_default_menu'
                    )); 
                    ?>
                </nav>
                
                <!-- CTA ראשי בהדר -->
                <?php if (!is_page('calculator')): ?>
                <div class="header-cta">
                    <a href="<?php echo site_url('/calculator'); ?>" class="energi-cta header-cta-button">
                        🔋 מחשבון חיסכון חינמי
                    </a>
                </div>
                <?php endif; ?>
            </header>

            <!-- תפריט מובייל -->
            <div id="mobilemenu">
                <ul>
                    <li><a href="<?php echo home_url(); ?>">🏠 עמוד הבית</a></li>
                    <li><a href="<?php echo site_url('/calculator'); ?>">🧮 מחשבון חיסכון</a></li>
                    <?php wp_list_categories('title_li='); ?>
                    <li><a href="<?php echo site_url('/contact'); ?>">📞 צור קשר</a></li>
                </ul>
            </div>

            <!-- Breadcrumbs לניווט -->
            <?php if (!is_home() && !is_front_page()): ?>
            <div class="breadcrumbs">
                <a href="<?php echo home_url(); ?>">🏠 עמוד הבית</a>
                <?php
                if (is_category()) {
                    echo ' > ' . single_cat_title('', false);
                } elseif (is_single()) {
                    echo ' > ' . get_the_category_list(', ');
                    echo ' > ' . get_the_title();
                } elseif (is_page()) {
                    echo ' > ' . get_the_title();
                }
                ?>
            </div>
            <?php endif; ?>

<?php
// תפריט ברירת מחדל אם אין תפריט מוגדר
function energi_default_menu() {
    echo '<ul class="main-menu">';
    echo '<li><a href="' . home_url() . '">עמוד הבית</a></li>';
    echo '<li><a href="' . site_url('/calculator') . '">מחשבון חיסכון</a></li>';
    wp_list_categories('title_li=&show_count=1');
    echo '</ul>';
}
?>