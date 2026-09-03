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
	
<header class="hub-main-header">
  <div class="header-container">
    <div class="site-logo-area">
      <a href="<?php echo esc_url(home_url('/')); ?>" class="logo-link">
        <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/logo.png" alt="<?php echo esc_attr(get_bloginfo('name')); ?>" class="main-logo" />
      </a>
    </div>
    <div class="header-search-area">
      <?php get_search_form(); ?>
    </div>
  </div>
</header>