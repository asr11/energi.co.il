<div id="mobilemenu">
<ul>
<?php wp_list_cats(); ?>
</ul>
</div>

<footer class="hub-site-footer">
  <div class="footer-content-wrapper">
    <div class="footer-legal-notice">
      <p class="disclaimer-text">
        <strong>אתר energi.co.il</strong> מציע תוכן ומידע מקצועי בנושאי אנרגיה, התקנות סולאריות ועמדות טעינה. המידע המוצג באתר נועד למטרות לימוד והשוואה בלבד. השימוש במידע הינו באחריות המשתמש.
      </p>
    </div>
    
    <div class="footer-links-row">
      <ul class="legal-links-list">
        <li><a href="<?php echo esc_url(home_url('/accessibility/')); ?>">♿ הצהרת נגישות</a></li>
        <li><a href="<?php echo esc_url(home_url('/privacy-policy/')); ?>">🔒 מדיניות פרטיות ותקנון (תשע"ז-2017)</a></li>
        <li><a href="https://www.facebook.com/profile.php" target="_blank" rel="noopener">🌐 עקבו אחרינו בפייסבוק</a></li>
      </ul>
    </div>

    <div class="footer-copyright-bar">
      © <?php echo date('Y'); ?> <?php bloginfo('name'); ?> — כל הזכויות שמורות. נבנה ומאובטח ע"י HUB האב מערכות מתקדמות בע"מ.
    </div>
  </div>
</footer>
</div>
</div>
<?php wp_footer(); ?>
</body>
</html>