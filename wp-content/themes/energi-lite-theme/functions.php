<?php
/**
 * Energi Lite Theme functions
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Setup
add_action('after_setup_theme', function(){
  load_theme_textdomain('energi-lite', get_template_directory() . '/languages');
  add_theme_support('title-tag');
  add_theme_support('post-thumbnails');
  add_theme_support('html5', ['search-form','comment-form','comment-list','gallery','caption','style','script']);
  add_theme_support('automatic-feed-links');
  add_theme_support('customize-selective-refresh-widgets');
  register_nav_menus([
    'primary' => __('תפריט ראשי', 'energi-lite'),
    'footer'  => __('תפריט פוטר', 'energi-lite'),
  ]);
});

// Enqueue
add_action('wp_enqueue_scripts', function(){
  $ver = wp_get_environment_type() === 'production' ? '1.0.0' : time();
  wp_enqueue_style('energi-lite-style', get_stylesheet_uri(), [], $ver);
  wp_enqueue_style('energi-lite-main', get_template_directory_uri().'/assets/css/main.css', [], $ver);
  wp_enqueue_script('energi-lite-main', get_template_directory_uri().'/assets/js/main.js', [], $ver, true);
});

// Widgets
add_action('widgets_init', function(){
  register_sidebar([
    'name'          => __('סיידבר', 'energi-lite'),
    'id'            => 'sidebar-1',
    'description'   => __('אזור וידג׳טים צדדי.', 'energi-lite'),
    'before_widget' => '<section id="%1$s" class="widget %2$s card">',
    'after_widget'  => '</section>',
    'before_title'  => '<h2 class="widget-title">',
    'after_title'   => '</h2>',
  ]);
});

// Helper: JSON-LD basic Site schema
add_action('wp_head', function(){
  $data = [
    "@context" => "https://schema.org",
    "@type" => "WebSite",
    "name" => get_bloginfo('name'),
    "url" => home_url('/'),
    "potentialAction" => [
      "@type" => "SearchAction",
      "target" => home_url('/?s={search_term_string}'),
      "query-input" => "required name=search_term_string"
    ]
  ];
  echo '<script type="application/ld+json">'.wp_json_encode($data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES).'</script>';
}, 20);

// Clean head
remove_action('wp_head','wp_generator');

// Allow SVG (optional, secure properly if used)
add_filter('upload_mimes', function($mimes){
  $mimes['svg'] = 'image/svg+xml';
  return $mimes;
});

// Shortcode fallback for Energi lead form (replace if plugin provides one)
add_shortcode('energi_lead_form', function($atts){
  ob_start(); ?>
  <form class="card" method="post" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>">
    <h3><?php _e('בקשת הצעת חיסכון מותאמת', 'energi-lite'); ?></h3>
    <div class="row">
      <div>
        <label><?php _e('שם מלא', 'energi-lite'); ?></label>
        <input type="text" name="full_name" required>
      </div>
      <div>
        <label><?php _e('טלפון', 'energi-lite'); ?></label>
        <input type="tel" name="phone" required>
      </div>
    </div>
    <div class="row">
      <div>
        <label><?php _e('אימייל', 'energi-lite'); ?></label>
        <input type="email" name="email">
      </div>
      <div>
        <label><?php _e('סוג אנרגיה', 'energi-lite'); ?></label>
        <select name="energy_type" required>
          <option value="ev"><?php _e('טעינת רכב חשמלי', 'energi-lite'); ?></option>
          <option value="solar"><?php _e('סולאר', 'energi-lite'); ?></option>
          <option value="storage"><?php _e('אגירה', 'energi-lite'); ?></option>
          <option value="efficiency"><?php _e('ייעול אנרגטי', 'energi-lite'); ?></option>
        </select>
      </div>
    </div>
    <div>
      <label><?php _e('הערות / צרכים ייחודיים', 'energi-lite'); ?></label>
      <textarea name="notes" rows="4"></textarea>
    </div>
    <button class="btn" type="submit"><?php _e('שליחת בקשה', 'energi-lite'); ?></button>
    <input type="hidden" name="action" value="energi_lite_lead">
    <?php wp_nonce_field('energi_lite_lead','energi_lite_nonce'); ?>
  </form>
  <?php
  return ob_get_clean();
});

// AJAX handler (placeholder - logs only; integrate with Energi Leads Manager)
add_action('wp_ajax_nopriv_energi_lite_lead', 'energi_lite_lead_handler');
add_action('wp_ajax_energi_lite_lead', 'energi_lite_lead_handler');
function energi_lite_lead_handler(){
  if( empty($_POST['energi_lite_nonce']) || ! wp_verify_nonce($_POST['energi_lite_nonce'], 'energi_lite_lead') ){
    wp_send_json_error(['message'=>'Nonce failed'], 400);
  }
  $payload = [
    'full_name'   => sanitize_text_field($_POST['full_name'] ?? ''),
    'phone'       => sanitize_text_field($_POST['phone'] ?? ''),
    'email'       => sanitize_email($_POST['email'] ?? ''),
    'energy_type' => sanitize_text_field($_POST['energy_type'] ?? ''),
    'notes'       => sanitize_textarea_field($_POST['notes'] ?? ''),
    'ip'          => $_SERVER['REMOTE_ADDR'] ?? '',
    'ts'          => current_time('mysql'),
  ];
  // TODO: integrate with Energi Leads Manager plugin or custom DB table.
  error_log('Energi Lite Lead: '. wp_json_encode($payload, JSON_UNESCAPED_UNICODE));
  wp_send_json_success(['message'=>__('תודה! קיבלנו את הבקשה ונחזור אליך בהקדם.', 'energi-lite')]);
}