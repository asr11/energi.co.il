<?php if ( ! defined( 'ABSPATH' ) ) exit; ?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?> <?php echo is_rtl() ? 'dir="rtl"' : ''; ?>>
<header class="container" style="padding:18px 0; display:flex; gap:12px; align-items:center; justify-content:space-between">
  <a href="<?php echo esc_url(home_url('/')); ?>" class="brand" style="font-weight:800; font-size:1.25rem;">
    <?php bloginfo('name'); ?>
  </a>
  <nav aria-label="<?php esc_attr_e('ראשי', 'energi-lite'); ?>">
    <?php wp_nav_menu([
      'theme_location'=>'primary',
      'container'=>false,
      'menu_class'=>'menu',
      'fallback_cb'=>false
    ]); ?>
  </nav>
</header>
<main class="container">