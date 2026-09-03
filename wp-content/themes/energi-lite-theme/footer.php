</main>
<footer class="footer container">
  <div class="grid" style="grid-template-columns:1fr; gap:12px;">
    <div>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?> · <?php bloginfo('description'); ?></div>
    <nav>
      <?php wp_nav_menu([
        'theme_location'=>'footer',
        'container'=>false,
        'menu_class'=>'menu menu--footer',
        'fallback_cb'=>false
      ]); ?>
    </nav>
  </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>