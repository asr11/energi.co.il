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

// 5. Advanced 2026 Interactive Privacy & Preference Management Center (Regulations 2017 & Granular Selection)
add_action('wp_footer', function() {
    if (is_admin()) return;
    ?>
    <!-- 1. Floating Quick Consent Bar -->
    <div id="hub-privacy-consent-banner" style="position: fixed; bottom: 70px; right: 20px; max-width: 460px; background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(16px); color: #0f172a; padding: 20px 24px; border-radius: 16px; box-shadow: 0 20px 30px -10px rgba(13, 59, 102, 0.15), 0 10px 15px -5px rgba(0, 0, 0, 0.08); border: 1px solid #e2e8f0; z-index: 9998; font-family: 'Assistant', sans-serif; direction: rtl; text-align: right; display: none;">
        <div style="font-weight: 800; font-size: 1.05rem; color: #0d3b66; margin-bottom: 8px; display: flex; align-items: center; justify-content: space-between;">
            <span style="display: flex; align-items: center; gap: 8px;">🛡️ ניהול פרטיות ואבטחת מידע (2026)</span>
            <span style="font-size: 0.75rem; background: #e0f2fe; color: #0369a1; padding: 3px 8px; border-radius: 12px; font-weight: 600;">תקנות תשע"ז-2017</span>
        </div>
        <p style="font-size: 0.88rem; color: #475569; margin: 0 0 16px 0; line-height: 1.6;">
            אתר energi.co.il מגן על פרטיותך עפ"י חוק. באפשרותך לבחור את סוגי העוגיות והשימושים המורשים במידע. למידע נוסף עיין ב<a href="<?php echo esc_url(home_url('/privacy-policy/')); ?>" target="_blank" style="color: #0d3b66; text-decoration: underline; font-weight: 700;">מדיניות הפרטיות</a>.
        </p>
        <div style="display: flex; gap: 10px; flex-wrap: wrap; justify-content: flex-end; align-items: center;">
            <button id="hub-open-privacy-modal-btn" style="background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; padding: 9px 16px; border-radius: 10px; font-weight: 600; font-size: 0.85rem; cursor: pointer; transition: all 0.2s ease;">
                ⚙️ התאמה אישית
            </button>
            <button id="hub-accept-all-privacy-btn" style="background: #10b981; color: #ffffff; border: none; padding: 9px 20px; border-radius: 10px; font-weight: 700; font-size: 0.88rem; cursor: pointer; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25); transition: background 0.2s ease;">
                ✓ מאשר/ת הכל
            </button>
        </div>
    </div>

    <!-- 2. Granular Preference Modal Dialog -->
    <div id="hub-privacy-modal" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(8px); z-index: 99999; display: none; justify-content: center; align-items: center; padding: 20px; box-sizing: border-box; font-family: 'Assistant', sans-serif;">
        <div style="background: #ffffff; max-width: 580px; width: 100%; border-radius: 20px; padding: 28px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); direction: rtl; text-align: right; max-height: 90vh; overflow-y: auto; border: 1px solid #cbd5e1;">
            
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; padding-bottom: 16px; margin-bottom: 20px;">
                <div>
                    <h3 style="margin: 0; color: #0d3b66; font-size: 1.25rem; font-weight: 800;">⚙️ הגדרות העדפות פרטיות</h3>
                    <span style="font-size: 0.82rem; color: #64748b;">בחר את רמת הפרטיות והאיסוף המועדפת עליך</span>
                </div>
                <button id="hub-close-privacy-modal-btn" style="background: #f1f5f9; border: none; font-size: 1.2rem; width: 36px; height: 36px; border-radius: 50%; cursor: pointer; color: #64748b; display: flex; align-items: center; justify-content: center;">✕</button>
            </div>

            <!-- Preference Item 1: Essential -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; margin-bottom: 14px; display: flex; justify-content: space-between; align-items: flex-start;">
                <div style="max-width: 80%;">
                    <strong style="color: #0d3b66; font-size: 0.95rem; display: block; margin-bottom: 4px;">🔒 עוגיות חיוניות ואבטחה (חובה)</strong>
                    <p style="margin: 0; font-size: 0.82rem; color: #64748b; line-height: 1.5;">דרושות לתפעול תקין של האתר, אבטחת מידע עפ"י חוק, FastCGI Caching ומניעת הונאות.</p>
                </div>
                <label style="position: relative; display: inline-block; width: 44px; height: 24px; opacity: 0.7; cursor: not-allowed;">
                    <input type="checkbox" checked disabled style="opacity: 0; width: 0; height: 0;">
                    <span style="position: absolute; cursor: not-allowed; top: 0; left: 0; right: 0; bottom: 0; background-color: #10b981; transition: .3s; border-radius: 24px;"></span>
                </label>
            </div>

            <!-- Preference Item 2: Analytics -->
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; margin-bottom: 14px; display: flex; justify-content: space-between; align-items: flex-start;">
                <div style="max-width: 80%;">
                    <strong style="color: #0d3b66; font-size: 0.95rem; display: block; margin-bottom: 4px;">📊 אנליטיקה ושיפור חווית גלישה</strong>
                    <p style="margin: 0; font-size: 0.82rem; color: #64748b; line-height: 1.5;">מאפשר לנו למדוד תנועת גולשים ולשפר את מהירות הטעינה והתכנים באתר.</p>
                </div>
                <label style="position: relative; display: inline-block; width: 48px; height: 26px;">
                    <input type="checkbox" id="pref-analytics" checked style="display: none;">
                    <span class="hub-toggle-slider" style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #cbd5e1; transition: .3s; border-radius: 26px;"></span>
                </label>
            </div>

            <!-- Preference Item 3: Marketing & Quotes -->
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: flex-start;">
                <div style="max-width: 80%;">
                    <strong style="color: #0d3b66; font-size: 0.95rem; display: block; margin-bottom: 4px;">🎯 התאמת הצעות מחיר סולאריות</strong>
                    <p style="margin: 0; font-size: 0.82rem; color: #64748b; line-height: 1.5;">מאפשר התאמת הצעות מחיר מותאמות אזורית ממתקינים מורשים בלבד.</p>
                </div>
                <label style="position: relative; display: inline-block; width: 48px; height: 26px;">
                    <input type="checkbox" id="pref-marketing" checked style="display: none;">
                    <span class="hub-toggle-slider" style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #cbd5e1; transition: .3s; border-radius: 26px;"></span>
                </label>
            </div>

            <style>
                .hub-toggle-slider:before {
                    position: absolute;
                    content: "";
                    height: 20px;
                    width: 20px;
                    left: 3px;
                    bottom: 3px;
                    background-color: white;
                    transition: .3s;
                    border-radius: 50%;
                }
                input:checked + .hub-toggle-slider {
                    background-color: #10b981 !important;
                }
                input:checked + .hub-toggle-slider:before {
                    transform: translateX(22px);
                }
            </style>

            <div style="display: flex; gap: 12px; justify-content: space-between; align-items: center; border-top: 1px solid #f1f5f9; padding-top: 18px;">
                <a href="<?php echo esc_url(home_url('/privacy-policy/')); ?>" target="_blank" style="font-size: 0.85rem; color: #0d3b66; font-weight: 600; text-decoration: underline;">
                    📜 קרא את המדיניות המלאה
                </a>
                <div style="display: flex; gap: 10px;">
                    <button id="hub-save-preferences-btn" style="background: #0d3b66; color: #ffffff; border: none; padding: 10px 22px; border-radius: 10px; font-weight: 700; font-size: 0.88rem; cursor: pointer;">
                        שמור העדפות
                    </button>
                </div>
            </div>

        </div>
    </div>

    <!-- 3. Dynamic Footer Management Trigger -->
    <script>
    (function() {
        var banner = document.getElementById('hub-privacy-consent-banner');
        var modal = document.getElementById('hub-privacy-modal');
        var savedPrefs = localStorage.getItem('energi_privacy_prefs');

        if (!savedPrefs && banner) {
            banner.style.display = 'block';
        }

        // Accept All Action
        var acceptAllBtn = document.getElementById('hub-accept-all-privacy-btn');
        if (acceptAllBtn) {
            acceptAllBtn.addEventListener('click', function() {
                var prefs = { essential: true, analytics: true, marketing: true, timestamp: new Date().toISOString() };
                localStorage.setItem('energi_privacy_prefs', JSON.stringify(prefs));
                if (banner) banner.style.display = 'none';
                if (modal) modal.style.display = 'none';
            });
        }

        // Open Modal Action
        var openModalBtn = document.getElementById('hub-open-privacy-modal-btn');
        if (openModalBtn) {
            openModalBtn.addEventListener('click', function() {
                if (modal) modal.style.display = 'flex';
            });
        }

        // Close Modal Action
        var closeModalBtn = document.getElementById('hub-close-privacy-modal-btn');
        if (closeModalBtn) {
            closeModalBtn.addEventListener('click', function() {
                if (modal) modal.style.display = 'none';
            });
        }

        // Save Custom Preferences Action
        var saveBtn = document.getElementById('hub-save-preferences-btn');
        if (saveBtn) {
            saveBtn.addEventListener('click', function() {
                var analytics = document.getElementById('pref-analytics').checked;
                var marketing = document.getElementById('pref-marketing').checked;
                var prefs = { essential: true, analytics: analytics, marketing: marketing, timestamp: new Date().toISOString() };
                localStorage.setItem('energi_privacy_prefs', JSON.stringify(prefs));
                if (banner) banner.style.display = 'none';
                if (modal) modal.style.display = 'none';
            });
        }

        // Global Window Function to Reopen Management Modal from Footer/Menu
        window.openEnergiPrivacyCenter = function() {
            if (modal) modal.style.display = 'flex';
        };
    })();
    </script>
    <?php
}, 999);



