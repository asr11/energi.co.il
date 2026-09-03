<?php
/**
 * Plugin Name: HUB Israeli Regulatory Privacy & Security Hardening
 * Description: Enforces Israeli Protection of Privacy Law (2017) & Cyber Security standards.
 * Version: 1.0.0
 * Author: HUB Advanced Systems
 */

if (!defined('ABSPATH')) {
    exit;
}

// 1. Disable XML-RPC completely
add_filter('xmlrpc_enabled', '__return_false');
add_filter('wp_headers', function($headers) {
    unset($headers['X-Pingback']);
    return $headers;
});

// 2. Enforce Security HTTP Headers (Israeli Privacy & Cyber Bureau Guidelines)
add_action('send_headers', function() {
    if (!headers_sent()) {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
    }
});

// 3. Remove WordPress Generator & Version Leakage
remove_action('wp_head', 'wp_generator');
add_filter('the_generator', '__return_empty_string');

// 4. Shortcode [energi_privacy_notice]: Statutory Legal Privacy Component (Section 11 Disclosure)
add_shortcode('energi_privacy_notice', function($atts) {
    ob_start();
    ?>
    <div class="energi-legal-privacy-box" style="background: #f8fafc; border-right: 4px solid #0d3b66; border-radius: 8px; padding: 16px; margin: 15px 0; font-family: 'Assistant', sans-serif; text-align: right; direction: rtl; color: #1e293b;">
        <h4 style="margin: 0 0 8px 0; color: #0d3b66; font-size: 1rem; display: flex; align-items: center; gap: 8px;">
            <span>🛡️</span> הודעה עפ"י סעיף 11 לחוק הגנת הפרטיות (תשע"ז-2017)
        </h4>
        <p style="margin: 0; font-size: 0.88rem; line-height: 1.6; color: #475569;">
            מסירת המידע בטפסי האתר מתבצעת מרצונך החופשי בלבד. המידע שתמסור/תמסרי יישמר במאגר מידע מאובטח של האתר וישמש בלבד לצורך חזרה אליך, מתן ייעוץ והתאמת הצעות מחיר ממתקינים מורשים. הנך זכאי/ת לעיין במידע ולבקש מחיקתו בכל עת בהתאם ל<a href="<?php echo esc_url(home_url('/privacy-policy/')); ?>" target="_blank" style="color: #0d3b66; font-weight: 600; text-decoration: underline;">מדיניות הפרטיות ותקנון האתר</a>.
        </p>
    </div>
    <?php
    return ob_get_clean();
});

// 5. Global Privacy Consent Cookie Banner (Israeli Privacy Protection Regulations 2017)
add_action('wp_footer', function() {
    if (is_admin()) return;
    ?>
    <div id="hub-privacy-consent-banner" style="position: fixed; bottom: 60px; right: 20px; max-width: 420px; background: #ffffff; color: #0f172a; padding: 18px 22px; border-radius: 14px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.15), 0 8px 10px -6px rgba(0,0,0,0.1); border: 1px solid #cbd5e1; z-index: 9998; font-family: 'Assistant', sans-serif; direction: rtl; text-align: right; display: none;">
        <div style="font-weight: 700; font-size: 0.95rem; color: #0d3b66; margin-bottom: 6px; display: flex; align-items: center; gap: 6px;">
            🔒 הגנת פרטיות ואבטחת מידע
        </div>
        <p style="font-size: 0.85rem; color: #475569; margin: 0 0 14px 0; line-height: 1.5;">
            אתר זה פועל בהתאם לתקנות הגנת הפרטיות (אבטחת מידע) תשע"ז-2017. אנו משתמשים במידע לשם מתן השירות המבוקש בלבד. בגלישתך הנך מסכים/ה ל<a href="<?php echo esc_url(home_url('/privacy-policy/')); ?>" target="_blank" style="color: #0d3b66; text-decoration: underline; font-weight: 600;">מדיניות הפרטיות</a>.
        </p>
        <div style="display: flex; gap: 10px; justify-content: flex-end;">
            <button id="hub-accept-privacy-btn" style="background: #0d3b66; color: #ffffff; border: none; padding: 8px 18px; border-radius: 8px; font-weight: 700; font-size: 0.85rem; cursor: pointer; transition: background 0.2s ease;">
                אני מאשר/ת
            </button>
        </div>
    </div>
    <script>
    (function() {
        if (!localStorage.getItem('energi_privacy_accepted')) {
            var banner = document.getElementById('hub-privacy-consent-banner');
            if (banner) banner.style.display = 'block';
        }
        var btn = document.getElementById('hub-accept-privacy-btn');
        if (btn) {
            btn.addEventListener('click', function() {
                localStorage.setItem('energi_privacy_accepted', 'true');
                var banner = document.getElementById('hub-privacy-consent-banner');
                if (banner) banner.style.display = 'none';
            });
        }
    })();
    </script>
    <?php
}, 999);


