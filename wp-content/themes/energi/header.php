<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta charset="<?php bloginfo( 'charset' ); ?>">
<title><?php wp_title( '|', true, 'left' ); ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Assistant:wght@400;600;700;800&family=Rubik:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>">
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

        <li><a href="<?php echo esc_url(home_url('/installers-index/')); ?>">אינדקס מתקינים</a></li>

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