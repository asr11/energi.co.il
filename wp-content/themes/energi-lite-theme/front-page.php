<?php /* Template: Front Page */ get_header(); ?>
<section class="hero">
  <div class="container">
    <h1><?php bloginfo('name'); ?></h1>
    <p><?php bloginfo('description'); ?></p>
    <div class="grid" style="grid-template-columns:1fr; gap:var(--spacing)">
      <a href="#lead" class="btn"><?php _e('קבלו תכנית חיסכון מותאמת', 'energi-lite'); ?></a>
      <a href="<?php echo esc_url( get_post_type_archive_link('post') ); ?>" class="btn btn--ghost"><?php _e('למידע ומאמרים', 'energi-lite'); ?></a>
    </div>
  </div>
</section>

<section id="lead" class="container">
  <?php echo do_shortcode('[energi_lead_form]'); ?>
</section>

<section class="container" style="margin-top:40px">
  <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));">
    <div class="card">
      <h3><?php _e('טעינת רכב חשמלי', 'energi-lite'); ?></h3>
      <p><?php _e('ביתית, בניין משותף, עסקית וציבורית – פתרון מקצה לקצה.', 'energi-lite'); ?></p>
    </div>
    <div class="card">
      <h3><?php _e('סולאר', 'energi-lite'); ?></h3>
      <p><?php _e('מערכות על גגות פרטיים ומסחריים, תכנון והתקנה.', 'energi-lite'); ?></p>
    </div>
    <div class="card">
      <h3><?php _e('אגירת אנרגיה', 'energi-lite'); ?></h3>
      <p><?php _e('ניצול אנרגיה חכם ושקט, לצריכה בזמן שיא.', 'energi-lite'); ?></p>
    </div>
    <div class="card">
      <h3><?php _e('ייעול אנרגטי', 'energi-lite'); ?></h3>
      <p><?php _e('בדיקות, דוחות ופתרונות להפחתת עלויות.', 'energi-lite'); ?></p>
    </div>
  </div>
</section>
<?php get_footer(); ?>