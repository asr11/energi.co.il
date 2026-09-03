<div id="mobilemenu">
<ul>
<?php wp_list_categories(array('title_li' => '')); ?>
</ul>
</div>
<footer>
<div id="footer">
  © <?php echo date('Y'); ?> <a href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a> כל הזכויות שמורות לאתר.
  | <a href="<?php echo esc_url(home_url('/privacy-policy/')); ?>" style="color: #38bdf8; text-decoration: underline;">🔒 מדיניות פרטיות ותקנון (תשע"ז-2017)</a>
  | <a href="<?php echo esc_url(home_url('/accessibility/')); ?>" style="color: #38bdf8; text-decoration: underline;">♿ הצהרת נגישות</a>
</div>
<p style="font-size: 0.85rem; line-height: 1.6; max-width: 1000px; margin: 15px auto; color: #94a3b8; text-align: center;">
אתר energi.co.il מציע תוכן ומידע בנושאי אנרגיה ונושאים קשורים. יש להדגיש כי האתר ו/או מנהליו אינם נושאים באחריות לדיוק, אמינות, או שלמות של השירותים, המוצרים, או כל תכנים אחרים המוצגים על ידי גורמים שלישיים המפורסמים באתר זה. השימוש במידע המוצג באתר הינו על אחריותו المלאה של המשתמש.
<br>
המידע המוצג באתר נועד לשמש למטרות מידע בלבד ולא יהווה תחליף לייעוץ מקצועי. מומלץ להתייעץ עם מומחה מתאים לפני נקיטת כל פעולה המבוססת על המידע המוצג באתר זה.
</p>
<ul style="list-style: none; padding: 0; margin: 10px 0; text-align: center;">
	<li style="display: inline-block; margin: 0 10px;"><a href="<?php echo esc_url(home_url('/accessibility/')); ?>">הצהרת נגישות</a></li>
	<li style="display: inline-block; margin: 0 10px;"><a href="<?php echo esc_url(home_url('/privacy-policy/')); ?>">מדיניות פרטיות</a></li>
	<li style="display: inline-block; margin: 0 10px;"><a href="https://www.facebook.com/profile.php?id=61555866276789" target="_blank" rel="noopener">בקרו אותנו בפייסבוק</a></li>
</ul>
</footer>
</div>
</div>
<?php wp_footer(); ?>
</body>
</html>