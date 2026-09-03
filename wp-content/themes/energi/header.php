<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta charset="<?php bloginfo( 'charset' ); ?>">
<title><?php wp_title( '|', true, 'left' ); ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Assistant:wght@400;600;700;800&family=Rubik:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>?v=<?php echo filemtime(get_stylesheet_directory() . '/style.css'); ?>">
<style>
/* Critical Header Layout Safety Safeguard (2026) */
.hub-header-2026 { position: sticky; top: 0; z-index: 1000; background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px); border-bottom: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
.hub-header-container { display: flex !important; justify-content: space-between !important; align-items: center !important; padding: 4px 20px !important; max-width: 1400px !important; margin: 0 auto !important; gap: 16px !important; }
.hub-main-nav { flex: 1 !important; display: flex !important; justify-content: center !important; }
.hub-nav-list { list-style: none !important; margin: 0 !important; padding: 0 !important; display: flex !important; align-items: center !important; gap: 18px !important; }
.hub-nav-list > li { position: relative !important; list-style: none !important; margin: 0 !important; }
.hub-nav-list > li > a { color: #0d3b66 !important; font-family: 'Rubik', sans-serif !important; font-weight: 600 !important; font-size: 1rem !important; text-decoration: none !important; padding: 8px 12px !important; border-radius: 8px !important; display: flex !important; align-items: center !important; gap: 4px !important; }
.hub-main-logo { height: 55px !important; width: auto !important; display: block !important; object-fit: contain !important; }
.dropdown-menu { display: none; position: absolute; top: 100%; right: 0; background: #ffffff; min-width: 230px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); border-radius: 12px; padding: 10px 0; list-style: none !important; margin: 0 !important; z-index: 1001; border: 1px solid #e2e8f0; }
.has-dropdown:hover .dropdown-menu { display: block !important; }
.dropdown-menu li { list-style: none !important; margin: 0 !important; }
.dropdown-menu a { display: block !important; padding: 10px 18px !important; color: #1e293b !important; text-decoration: none !important; font-size: 0.92rem !important; }
.dropdown-menu a:hover { background: #f8fafc !important; color: #10b981 !important; }
</style>
<link rel="icon" href="<?php echo get_stylesheet_directory_uri(); ?>/images/logo.png" type="image/png">
<link rel="shortcut icon" href="<?php echo get_stylesheet_directory_uri(); ?>/images/logo.png" type="image/png">
<?php wp_head(); ?>	
</head>
<body <?php body_class(); ?>>
<div id="site-wrapper">
<div id="inner">
	
<header class="hub-header-2026">
  <div class="hub-header-container">
    
    <!-- Right: Brand Logo -->
    <div class="hub-brand-logo-area">
      <a href="<?php echo esc_url(home_url('/')); ?>" class="hub-logo-link">
        <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/logo.png" alt="<?php echo esc_attr(get_bloginfo('name')); ?>" class="hub-main-logo" />
      </a>
    </div>

    <!-- Center: Main Navigation Menu with Sub-Categories & Blog -->
    <nav class="hub-main-nav">
      <ul class="hub-nav-list">
        
        <li class="has-dropdown">
          <a href="<?php echo esc_url(home_url('/c/solar-system/')); ?>">מערכת סולארית <span class="arrow">▾</span></a>
          <ul class="dropdown-menu">
            <li><a href="<?php echo esc_url(home_url('/solar-roi-calculator/')); ?>">⚡ מחשבון כדאיות סולארית</a></li>
            <li><a href="<?php echo esc_url(home_url('/c/solar-system/')); ?>">🏠 התקנה סולארית לבית פרטי</a></li>
            <li><a href="<?php echo esc_url(home_url('/c/solar-system/')); ?>">🏢 מערכות סולאריות מסחריות</a></li>
          </ul>
        </li>

        <li class="has-dropdown">
          <a href="<?php echo esc_url(home_url('/c/electric-vehicles/')); ?>">רכבים חשמליים <span class="arrow">▾</span></a>
          <ul class="dropdown-menu">
            <li><a href="<?php echo esc_url(home_url('/ev-savings-calculator/')); ?>">🚗 מחשבון חיסכון ברכב חשמלי</a></li>
            <li><a href="<?php echo esc_url(home_url('/c/electric-vehicles/')); ?>">🔌 עמדות טעינה ביתיות</a></li>
            <li><a href="<?php echo esc_url(home_url('/c/electric-vehicles/')); ?>">⚡ עמדות טעינה מהירות DC</a></li>
          </ul>
        </li>

        <li class="has-dropdown">
          <a href="<?php echo esc_url(home_url('/c/renewable-energy/')); ?>">אנרגיה מתחדשת <span class="arrow">▾</span></a>
          <ul class="dropdown-menu">
            <li><a href="<?php echo esc_url(home_url('/c/renewable-energy/')); ?>">🔋 מערכות אגירת חשמל</a></li>
            <li><a href="<?php echo esc_url(home_url('/c/green-technologies/')); ?>">🌱 טכנולוגיות ירוקות</a></li>
            <li><a href="<?php echo esc_url(home_url('/c/energy-saving/')); ?>">💡 ייעוץ חיסכון בחשמל</a></li>
          </ul>
        </li>

        <!-- NEW: Blog Button -->
        <li class="blog-btn-item">
          <a href="<?php echo esc_url(home_url('/blog/')); ?>" class="hub-blog-link">📝 בלוג ומדריכים</a>
        </li>

      </ul>
    </nav>

    <!-- Left: Search Bar -->
    <div class="hub-search-area">
      <?php get_search_form(); ?>
    </div>

  </div>
</header>