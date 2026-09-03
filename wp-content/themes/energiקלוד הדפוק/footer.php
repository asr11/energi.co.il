</main><!-- #main -->

<!-- Pre-Footer CTA Section -->
<section class="pre-footer-cta">
    <div class="container">
        <div class="cta-box">
            <div class="cta-content">
                <h2>מוכנים להתחיל לחסוך?</h2>
                <p>קבלו הצעת מחיר מותאמת אישית תוך 24 שעות</p>
            </div>
            <div class="cta-actions">
                <a href="<?php echo esc_url(home_url('/calculator')); ?>" class="btn btn-white btn-lg">
                    מחשבון חיסכון מיידי
                </a>
                <a href="tel:1800ENERGI" class="btn btn-outline-white btn-lg">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                    </svg>
                    1-800-ENERGI
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Main Footer -->
<footer id="colophon" class="site-footer">
    
    <!-- Footer Widgets -->
    <div class="footer-widgets">
        <div class="container">
            <div class="footer-grid">
                
                <!-- Column 1: About -->
                <div class="footer-column">
                    <h3>אודות Energi</h3>
                    <p>הפורטל המוביל בישראל לפתרונות אנרגיה ירוקה. מחברים בין צרכנים לספקים מובילים ומספקים מידע מקצועי ואובייקטיבי.</p>
                    <div class="footer-badges">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/badge-1.svg" alt="מאומת ע״י משרד האנרגיה" width="80">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/badge-2.svg" alt="ISO 9001" width="80">
                    </div>
                </div>
                
                <!-- Column 2: Solutions -->
                <div class="footer-column">
                    <h3>פתרונות אנרגיה</h3>
                    <ul class="footer-links">
                        <li><a href="<?php echo home_url('/solar'); ?>">מערכות סולאריות</a></li>
                        <li><a href="<?php echo home_url('/storage'); ?>">אגירת אנרגיה</a></li>
                        <li><a href="<?php echo home_url('/ev-charging'); ?>">עמדות טעינה לרכב חשמלי</a></li>
                        <li><a href="<?php echo home_url('/smart-home'); ?>">בית חכם וחיסכון באנרגיה</a></li>
                        <li><a href="<?php echo home_url('/heat-pump'); ?>">משאבות חום</a></li>
                        <li><a href="<?php echo home_url('/led'); ?>">תאורת LED</a></li>
                    </ul>
                </div>
                
                <!-- Column 3: Resources -->
                <div class="footer-column">
                    <h3>משאבים ומידע</h3>
                    <ul class="footer-links">
                        <li><a href="<?php echo home_url('/calculator'); ?>">מחשבון חיסכון באנרגיה</a></li>
                        <li><a href="<?php echo home_url('/guides'); ?>">מדריכים מקצועיים</a></li>
                        <li><a href="<?php echo home_url('/blog'); ?>">בלוג ועדכונים</a></li>
                        <li><a href="<?php echo home_url('/faq'); ?>">שאלות נפוצות</a></li>
                        <li><a href="<?php echo home_url('/glossary'); ?>">מילון מונחים</a></li>
                        <li><a href="<?php echo home_url('/subsidies'); ?>">מענקים ותמריצים</a></li>
                    </ul>
                </div>
                
                <!-- Column 4: Contact & Newsletter -->
                <div class="footer-column">
                    <h3>צור קשר</h3>
                    <div class="footer-contact">
                        <div class="contact-item">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                            </svg>
                            <a href="tel:1800ENERGI">1-800-ENERGI</a>
                        </div>
                        <div class="contact-item">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                            </svg>
                            <a href="mailto:info@energi.co.il">info@energi.co.il</a>
                        </div>
                    </div>
                    
                    <!-- Newsletter -->
                    <div class="footer-newsletter">
                        <h4>הרשמו לניוזלטר</h4>
                        <p>קבלו טיפים וחדשות ישירות למייל</p>
                        <form class="newsletter-form" action="<?php echo admin_url('admin-ajax.php'); ?>" method="post">
                            <input type="email" name="email" placeholder="כתובת אימייל" required>
                            <input type="hidden" name="action" value="energi_newsletter">
                            <?php wp_nonce_field('energi_newsletter', 'newsletter_nonce'); ?>
                            <button type="submit">הרשמה</button>
                        </form>
                    </div>
                    
                    <!-- Social Links -->
                    <div class="footer-social">
                        <a href="https://facebook.com/energi.il" target="_blank" rel="noopener" aria-label="Facebook">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                        <a href="https://instagram.com/energi.il" target="_blank" rel="noopener" aria-label="Instagram">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zM5.838 12a6.162 6.162 0 1112.324 0 6.162 6.162 0 01-12.324 0zM12 16a4 4 0 110-8 4 4 0 010 8zm4.965-10.405a1.44 1.44 0 112.881.001 1.44 1.44 0 01-2.881-.001z"/>
                            </svg>
                        </a>
                        <a href="https://linkedin.com/company/energi-il" target="_blank" rel="noopener" aria-label="LinkedIn">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                            </svg>
                        </a>
                        <a href="https://youtube.com/@energi-il" target="_blank" rel="noopener" aria-label="YouTube">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                            </svg>
                        </a>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    
    <!-- Trust Indicators -->
    <div class="footer-trust">
        <div class="container">
            <div class="trust-grid">
                <div class="trust-item">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="#00b894">
                        <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"/>
                    </svg>
                    <div>
                        <strong>אתר מאובטח SSL</strong>
                        <span>הגנה מלאה על המידע שלך</span>
                    </div>
                </div>
                <div class="trust-item">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="#00b894">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                    <div>
                        <strong>ספקים מאומתים</strong>
                        <span>רק חברות עם רישיון ומוניטין</span>
                    </div>
                </div>
                <div class="trust-item">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="#00b894">
                        <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"/>
                    </svg>
                    <div>
                        <strong>+50,000 לקוחות מרוצים</strong>
                        <span>מאז 2015</span>
                    </div>
                </div>
                <div class="trust-item">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="#00b894">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    <div>
                        <strong>דירוג 4.8/5</strong>
                        <span>על סמך 3,247 ביקורות</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bottom Footer -->
    <div class="footer-bottom">
        <div class="container">
            <div class="footer-bottom-content">
                <div class="footer-copyright">
                    <p>&copy; <?php echo date('Y'); ?> <a href="<?php echo home_url(); ?>"><?php bloginfo('name'); ?></a>. כל הזכויות שמורות.</p>
                </div>
                <div class="footer-legal">
                    <ul>
                        <li><a href="<?php echo home_url('/terms'); ?>">תנאי שימוש</a></li>
                        <li><a href="<?php echo home_url('/privacy'); ?>">מדיניות פרטיות</a></li>
                        <li><a href="<?php echo home_url('/accessibility'); ?>">הצהרת נגישות</a></li>
                        <li><a href="<?php echo home_url('/cookies'); ?>">מדיניות Cookies</a></li>
                        <li><a href="<?php echo home_url('/sitemap'); ?>">מפת אתר</a></li>
                    </ul>
                </div>
            </div>
            
            <!-- Disclaimer -->
            <div class="footer-disclaimer">
                <p>
                    <small>
                        הערה: המידע באתר נועד למטרות מידע כללי בלבד ואינו מהווה ייעוץ מקצועי. 
                        מומלץ להתייעץ עם מומחה לפני קבלת החלטות. 
                        המחירים והנתונים המוצגים באתר הינם אומדן בלבד ועשויים להשתנות.
                    </small>
                </p>
            </div>
        </div>
    </div>
    
</footer>

<!-- Back to Top -->
<button id="back-to-top" aria-label="חזרה למעלה" style="display:none;">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M18 15l-6-6-6 6"/>
    </svg>
</button>

<!-- WhatsApp Floating Button -->
<a href="https://wa.me/972501234567?text=היי, אני מעוניין בהצעת מחיר לפתרונות אנרגיה" 
   class="whatsapp-float" 
   target="_blank" 
   rel="noopener"
   aria-label="WhatsApp">
    <svg width="30" height="30" viewBox="0 0 24 24" fill="white">
        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
    </svg>
    <span class="whatsapp-float-text">שלח הודעה</span>
</a>

<?php wp_footer(); ?>

<!-- Schema.org Structured Data -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "<?php bloginfo('name'); ?>",
  "url": "<?php echo home_url(); ?>",
  "logo": "<?php echo get_template_directory_uri(); ?>/assets/images/logo.svg",
  "contactPoint": {
    "@type": "ContactPoint",
    "telephone": "+972-1800-ENERGI",
    "contactType": "customer service",
    "availableLanguage": "Hebrew"
  },
  "sameAs": [
    "https://facebook.com/energi.il",
    "https://instagram.com/energi.il",
    "https://linkedin.com/company/energi-il",
    "https://youtube.com/@energi-il"
  ]
}
</script>

</body>
</html>