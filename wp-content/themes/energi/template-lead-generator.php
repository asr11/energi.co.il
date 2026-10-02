<?php
/*
Template Name: מחשבון כדאיות ואנרגיה 2026
*/

if (!defined('ABSPATH')) exit;

// Handle Direct Fallback Form Submission
$lead_success = false;
$lead_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['energi_calc_lead'])) {
    $full_name = sanitize_text_field($_POST['full_name'] ?? '');
    $phone = sanitize_text_field($_POST['phone'] ?? '');
    $city = sanitize_text_field($_POST['city'] ?? '');
    $email = sanitize_email($_POST['email'] ?? '');
    $calc_type = sanitize_text_field($_POST['calc_type'] ?? 'solar');
    $estimated_savings = sanitize_text_field($_POST['estimated_savings'] ?? '');
    $property_size = sanitize_text_field($_POST['property_size'] ?? '');
    $monthly_bill = sanitize_text_field($_POST['monthly_bill'] ?? '');
    $privacy_consent = !empty($_POST['privacy_consent']);

    if (!empty($full_name) && !empty($phone) && $privacy_consent) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'energi_leads';

        // Ensure table exists
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            full_name varchar(100) NOT NULL,
            phone varchar(20) NOT NULL,
            email varchar(100),
            property_type varchar(50),
            solutions text,
            property_size varchar(20),
            monthly_bill varchar(20),
            city varchar(50),
            contact_time varchar(20),
            notes text,
            estimated_savings varchar(20),
            submission_date datetime DEFAULT CURRENT_TIMESTAMP,
            ip_address varchar(45),
            user_agent text,
            status varchar(20) DEFAULT 'new',
            PRIMARY KEY (id)
        ) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        $solutions_label = ($calc_type === 'ev') ? 'רכב חשמלי ועמדת טעינה' : 'מערכת סולארית ביתית';

        $wpdb->insert(
            $table_name,
            array(
                'full_name' => $full_name,
                'phone' => $phone,
                'email' => $email,
                'property_type' => 'בית פרטי / עסק',
                'solutions' => json_encode([$solutions_label]),
                'property_size' => $property_size,
                'monthly_bill' => $monthly_bill,
                'city' => $city,
                'contact_time' => 'הקדם האפשרי',
                'notes' => 'נשלח ממחשבון 2026 Bento UI. סוג: ' . $solutions_label,
                'estimated_savings' => $estimated_savings,
                'submission_date' => current_time('mysql'),
                'ip_address' => sanitize_text_field($_SERVER['REMOTE_ADDR'] ?? ''),
                'user_agent' => sanitize_text_field($_SERVER['HTTP_USER_AGENT'] ?? '')
            )
        );

        $admin_email = get_option('admin_email');
        if (!empty($admin_email)) {
            @wp_mail(
                $admin_email,
                '⚡ ליד חדש ממחשבון אנרגי: ' . $full_name,
                "התקבל ליד חדש:\nשם: $full_name\nטלפון: $phone\nעיר: $city\nסוג: $solutions_label\nחיסכון צפוי: $estimated_savings"
            );
        }
        $lead_success = true;
    } else {
        $lead_error = 'אנא מלא את כל שדות החובה ואשר את תנאי מדיניות הפרטיות.';
    }
}

get_header();
?>

<style>
/* 2026 Bento Grid & Interactive Calculators CSS */
.energi-bento-wrapper {
    max-width: 1400px;
    margin: 30px auto 60px;
    padding: 0 20px;
    direction: rtl;
    font-family: 'Assistant', 'Rubik', sans-serif;
    color: #1e293b;
}

.bento-hero-header {
    text-align: center;
    margin-bottom: 35px;
}
.bento-hero-header h1 {
    font-family: 'Rubik', sans-serif;
    font-size: 2.8rem;
    font-weight: 800;
    color: #0d3b66;
    margin-bottom: 10px;
    letter-spacing: -0.5px;
}
.bento-hero-header p {
    font-size: 1.25rem;
    color: #64748b;
    max-width: 750px;
    margin: 0 auto;
}

/* Tab Switcher */
.calc-tab-nav {
    display: flex;
    justify-content: center;
    gap: 12px;
    margin-bottom: 35px;
}
.calc-tab-btn {
    background: #ffffff;
    border: 2px solid #e2e8f0;
    color: #0d3b66;
    padding: 14px 28px;
    border-radius: 50px;
    font-family: 'Rubik', sans-serif;
    font-size: 1.15rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.25s ease;
    display: flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.03);
}
.calc-tab-btn:hover {
    border-color: #10b981;
    transform: translateY(-2px);
}
.calc-tab-btn.active {
    background: #0d3b66;
    border-color: #0d3b66;
    color: #ffffff;
    box-shadow: 0 6px 16px rgba(13, 59, 102, 0.25);
}

/* Bento Master Grid */
.bento-grid-container {
    display: grid;
    grid-template-columns: repeat(12, 1fr);
    gap: 24px;
}

/* Cards */
.bento-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.04);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.bento-card:hover {
    box-shadow: 0 8px 24px rgba(0,0,0,0.06);
}

.card-controls {
    grid-column: span 7;
}
.card-lead-capture {
    grid-column: span 5;
    background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    border: 1px solid #cbd5e1;
}
.card-kpis-solar, .card-kpis-ev {
    grid-column: span 12;
}

/* KPI Sub-Grid */
.kpi-bento-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-top: 10px;
}
.kpi-tile {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 22px 18px;
    text-align: center;
    border-top: 4px solid #10b981;
}
.kpi-tile-accent {
    border-top-color: #0d3b66;
}
.kpi-label {
    font-size: 0.95rem;
    color: #64748b;
    font-weight: 600;
    margin-bottom: 8px;
}
.kpi-val {
    font-family: 'Rubik', sans-serif;
    font-size: 2rem;
    font-weight: 800;
    color: #0d3b66;
    line-height: 1.1;
}
.kpi-val.green {
    color: #10b981;
}
.kpi-sub {
    font-size: 0.85rem;
    color: #94a3b8;
    margin-top: 6px;
}

/* Slider Controls */
.slider-group {
    margin-bottom: 28px;
}
.slider-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}
.slider-title {
    font-size: 1.15rem;
    font-weight: 700;
    color: #1e293b;
}
.slider-badge {
    background: #e6f4ea;
    color: #065f46;
    font-family: 'Rubik', sans-serif;
    font-size: 1.15rem;
    font-weight: 700;
    padding: 4px 14px;
    border-radius: 30px;
}

input[type=range].bento-slider {
    -webkit-appearance: none;
    width: 100%;
    height: 10px;
    border-radius: 8px;
    background: #e2e8f0;
    outline: none;
    transition: background 0.2s;
}
input[type=range].bento-slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 26px;
    height: 26px;
    border-radius: 50%;
    background: #10b981;
    cursor: pointer;
    border: 3px solid #ffffff;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    transition: transform 0.15s ease;
}
input[type=range].bento-slider::-webkit-slider-thumb:hover {
    transform: scale(1.15);
}

/* Form Styles */
.lead-form-inner {
    display: flex;
    flex-direction: column;
    gap: 16px;
}
.lead-input {
    width: 100%;
    padding: 13px 16px;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    font-size: 1rem;
    font-family: inherit;
    box-sizing: border-box;
}
.lead-input:focus {
    outline: none;
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
}
.lead-submit-btn {
    background: #10b981;
    color: #ffffff;
    border: none;
    border-radius: 10px;
    padding: 15px;
    font-family: 'Rubik', sans-serif;
    font-size: 1.2rem;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.2s ease, transform 0.1s ease;
}
.lead-submit-btn:hover {
    background: #059669;
    transform: translateY(-2px);
}
.instant-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.85rem;
    color: #0d3b66;
    background: #e0f2fe;
    padding: 4px 10px;
    border-radius: 20px;
    margin-bottom: 10px;
    font-weight: 600;
}

/* Success Banner */
.lead-success-banner {
    display: none;
    background: #d1fae5;
    border: 2px solid #10b981;
    border-radius: 12px;
    padding: 24px;
    text-align: center;
    color: #065f46;
}
.lead-success-banner h3 {
    margin-top: 0;
    font-size: 1.5rem;
    font-family: 'Rubik', sans-serif;
}

/* Responsive */
@media (max-width: 992px) {
    .card-controls, .card-lead-capture {
        grid-column: span 12;
    }
    .kpi-bento-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
@media (max-width: 600px) {
    .kpi-bento-grid {
        grid-template-columns: 1fr;
    }
    .bento-hero-header h1 {
        font-size: 2.1rem;
    }
}
</style>

<div class="energi-bento-wrapper">
    <div class="bento-hero-header">
        <h1>מחשבוני אנרגיה חכמים 2026 ⚡</h1>
        <p>חשב בזמן אמת את פוטנציאל החיסכון וההכנסה ממערכת סולארית ביתית או ממעבר לרכב חשמלי</p>
    </div>

    <!-- Switcher -->
    <div class="calc-tab-nav">
        <button type="button" class="calc-tab-btn active" id="btn-solar-tab" onclick="switchBentoTab('solar')">
            ☀️ מחשבון סולארי (ROI)
        </button>
        <button type="button" class="calc-tab-btn" id="btn-ev-tab" onclick="switchBentoTab('ev')">
            🚗 חיסכון רכב חשמלי (EV)
        </button>
    </div>

    <!-- Solar Bento Grid -->
    <div id="solar-bento-panel" class="bento-grid-container">
        <!-- Controls -->
        <div class="bento-card card-controls">
            <h2 style="font-family: 'Rubik', sans-serif; color: #0d3b66; margin-top: 0; margin-bottom: 24px;">הגדרת נתוני הגג והחשמל</h2>
            
            <div class="slider-group">
                <div class="slider-header">
                    <span class="slider-title">שטח גג זמין להצבה</span>
                    <span class="slider-badge" id="solarRoofBadge">100 מ"ר</span>
                </div>
                <input type="range" class="bento-slider" id="sliderRoofArea" min="30" max="300" step="5" value="100" oninput="calculateSolar()">
                <div style="display:flex; justify-content:space-between; font-size:0.85rem; color:#94a3b8; margin-top:6px;">
                    <span>30 מ"ר</span>
                    <span>150 מ"ר</span>
                    <span>300 מ"ר</span>
                </div>
            </div>

            <div class="slider-group">
                <div class="slider-header">
                    <span class="slider-title">חשבון חשמל חודשי ממוצע</span>
                    <span class="slider-badge" id="solarBillBadge">1,000 ₪</span>
                </div>
                <input type="range" class="bento-slider" id="sliderMonthlyBill" min="300" max="3500" step="50" value="1000" oninput="calculateSolar()">
                <div style="display:flex; justify-content:space-between; font-size:0.85rem; color:#94a3b8; margin-top:6px;">
                    <span>300 ₪</span>
                    <span>1,800 ₪</span>
                    <span>3,500 ₪</span>
                </div>
            </div>

            <div style="background:#f1f5f9; padding:15px; border-radius:12px; font-size:0.9rem; color:#475569; display:flex; gap:10px; align-items:center;">
                <span style="font-size:1.4rem;">💡</span>
                <span><strong>מדדי רשות החשמל 2026:</strong> תעריף הזרמה מובטח <strong>0.48 ₪ לקוט"ש</strong> ל-25 שנה, מקדם קרינה שנתית ממוצעת <strong>1,750 קוט"ש לכל kWp</strong>.</span>
            </div>
        </div>

        <!-- Lead Capture -->
        <div class="bento-card card-lead-capture">
            <div class="instant-badge">⚡ לכידה מיידית • 0ms השהייה</div>
            <h3 style="font-family:'Rubik', sans-serif; color:#0d3b66; margin-top:4px; margin-bottom:8px;">קבל 3 הצעות מחיר ממתקינים</h3>
            <p style="font-size:0.95rem; color:#64748b; margin-top:0; margin-bottom:20px;">השאר פרטים ונציג מוסמך יחזור אליך עם הצעה מותאמת לגג שלך:</p>
            
            <div id="leadSuccessSolar" class="lead-success-banner" style="<?php echo $lead_success ? 'display:block;' : ''; ?>">
                <h3>🎉 פנייתך התקבלה בהצלחה!</h3>
                <p>3 מתקינים מורשים מאזורך יצרו איתך קשר בהקדם עם הצעות מחיר מדויקות.</p>
            </div>

            <form id="leadFormSolar" class="lead-form-inner" method="POST" action="" onsubmit="handleLeadSubmit(event, 'solar')" style="<?php echo $lead_success ? 'display:none;' : ''; ?>">
                <input type="hidden" name="energi_calc_lead" value="1">
                <input type="hidden" name="calc_type" value="solar">
                <input type="hidden" name="property_size" id="hiddenSolarSize" value="100">
                <input type="hidden" name="monthly_bill" id="hiddenSolarBill" value="1000">
                <input type="hidden" name="estimated_savings" id="hiddenSolarSavings" value="12,000 ₪ / שנה">

                <div>
                    <input type="text" name="full_name" class="lead-input" required placeholder="שם מלא *">
                </div>
                <div>
                    <input type="tel" name="phone" class="lead-input" required placeholder="מספר טלפון *">
                </div>
                <div>
                    <input type="text" name="city" class="lead-input" placeholder="עיר / יישוב מגורים">
                </div>
                <div>
                    <input type="email" name="email" class="lead-input" placeholder="כתובת אימייל (רשות)">
                </div>

                <div style="display:flex; align-items:flex-start; gap:8px;">
                    <input type="checkbox" name="privacy_consent" id="privacyConsentSolar" required checked style="margin-top:4px; cursor:pointer;">
                    <label for="privacyConsentSolar" style="font-size:0.85rem; color:#475569; cursor:pointer;">
                        אני מאשר/ת את <a href="<?php echo esc_url(home_url('/privacy-policy/')); ?>" target="_blank" style="color:#0d3b66; text-decoration:underline;">מדיניות הפרטיות ותקנון השירות</a> (תשע"ז-2017) *
                    </label>
                </div>

                <button type="submit" class="lead-submit-btn">
                    לקבלת 3 הצעות מחיר ⟵
                </button>
            </form>
        </div>

        <!-- Solar KPIs -->
        <div class="bento-card card-kpis-solar">
            <h3 style="font-family:'Rubik', sans-serif; color:#0d3b66; margin-top:0;">תוצאות חישוב כדאיות סולארית (תחזית 2026)</h3>
            <div class="kpi-bento-grid">
                <div class="kpi-tile">
                    <div class="kpi-label">גודל מערכת מומלץ</div>
                    <div class="kpi-val" id="kpiSystemSize">14.3 kWp</div>
                    <div class="kpi-sub">לפי שטח הגג הזמין</div>
                </div>
                <div class="kpi-tile">
                    <div class="kpi-label">ייצור חשמל שנתי צפוי</div>
                    <div class="kpi-val" id="kpiAnnualKwh">25,025 קוט"ש</div>
                    <div class="kpi-sub">קרינה 1,750 kWh/kWp</div>
                </div>
                <div class="kpi-tile kpi-tile-accent">
                    <div class="kpi-label">הכנסה וחיסכון שנתי</div>
                    <div class="kpi-val green" id="kpiAnnualRev">₪12,012</div>
                    <div class="kpi-sub">הזרמה ב-0.48 ₪ לקוט"ש</div>
                </div>
                <div class="kpi-tile">
                    <div class="kpi-label">זמן החזר השקעה (ROI)</div>
                    <div class="kpi-val green" id="kpiPayback">5.5 שנים</div>
                    <div class="kpi-sub">רווח נקי 25 שנה: <strong id="kpiProfit25">₪262,400</strong></div>
                </div>
            </div>
        </div>
    </div>

    <!-- EV Bento Grid -->
    <div id="ev-bento-panel" class="bento-grid-container" style="display:none;">
        <!-- Controls -->
        <div class="bento-card card-controls">
            <h2 style="font-family: 'Rubik', sans-serif; color:#0d3b66; margin-top:0; margin-bottom:24px;">נתוני נסועה וצריכת דלק</h2>
            
            <div class="slider-group">
                <div class="slider-header">
                    <span class="slider-title">קילומטראז' חודשי ממוצע</span>
                    <span class="slider-badge" id="evKmBadge">1,800 ק"מ</span>
                </div>
                <input type="range" class="bento-slider" id="sliderEvKm" min="500" max="4000" step="50" value="1800" oninput="calculateEV()">
                <div style="display:flex; justify-content:space-between; font-size:0.85rem; color:#94a3b8; margin-top:6px;">
                    <span>500 ק"מ</span>
                    <span>2,000 ק"מ</span>
                    <span>4,000 ק"מ</span>
                </div>
            </div>

            <div class="slider-group">
                <div class="slider-header">
                    <span class="slider-title">צריכת דלק רכב בנזין קיים</span>
                    <span class="slider-badge" id="evGasBadge">13 ק"מ / ליטר</span>
                </div>
                <input type="range" class="bento-slider" id="sliderEvGas" min="8" max="18" step="1" value="13" oninput="calculateEV()">
                <div style="display:flex; justify-content:space-between; font-size:0.85rem; color:#94a3b8; margin-top:6px;">
                    <span>8 ק"מ/ל'</span>
                    <span>13 ק"מ/ל'</span>
                    <span>18 ק"מ/ל'</span>
                </div>
            </div>

            <div style="background:#f1f5f9; padding:15px; border-radius:12px; font-size:0.9rem; color:#475569; display:flex; gap:10px; align-items:center;">
                <span style="font-size:1.4rem;">🚗</span>
                <span><strong>בסיס ההשוואה:</strong> מחיר בנזין 95 רשמי <strong>7.50 ₪ לליטר</strong> לעומת טעינה ביתית ב-<strong>0.60 ₪ לקוט"ש</strong> (0.18 קוט"ש לק"מ).</span>
            </div>
        </div>

        <!-- Lead Capture -->
        <div class="bento-card card-lead-capture">
            <div class="instant-badge">⚡ השוואת מתקינים • עמדת טעינה</div>
            <h3 style="font-family:'Rubik', sans-serif; color:#0d3b66; margin-top:4px; margin-bottom:8px;">הצעת מחיר לעמדת טעינה</h3>
            <p style="font-size:0.95rem; color:#64748b; margin-top:0; margin-bottom:20px;">קבל עד 3 הצעות ממתקיני עמדות טעינה מוסמכים כולל אישור חשמלאי:</p>

            <div id="leadSuccessEV" class="lead-success-banner" style="<?php echo $lead_success ? 'display:block;' : ''; ?>">
                <h3>🎉 פנייתך התקבלה בהצלחה!</h3>
                <p>מתקיני עמדות טעינה מורשים יצרו איתך קשר בהקדם עם הצעת מחיר מותאמת.</p>
            </div>

            <form id="leadFormEV" class="lead-form-inner" method="POST" action="" onsubmit="handleLeadSubmit(event, 'ev')" style="<?php echo $lead_success ? 'display:none;' : ''; ?>">
                <input type="hidden" name="energi_calc_lead" value="1">
                <input type="hidden" name="calc_type" value="ev">
                <input type="hidden" name="property_size" id="hiddenEvKm" value="1800">
                <input type="hidden" name="monthly_bill" id="hiddenEvBill" value="1038">
                <input type="hidden" name="estimated_savings" id="hiddenEvSavings" value="10,128 ₪ / שנה">

                <div>
                    <input type="text" name="full_name" class="lead-input" required placeholder="שם מלא *">
                </div>
                <div>
                    <input type="tel" name="phone" class="lead-input" required placeholder="מספר טלפון *">
                </div>
                <div>
                    <input type="text" name="city" class="lead-input" placeholder="עיר / יישוב מגורים">
                </div>
                <div>
                    <input type="email" name="email" class="lead-input" placeholder="כתובת אימייל (רשות)">
                </div>

                <div style="display:flex; align-items:flex-start; gap:8px;">
                    <input type="checkbox" name="privacy_consent" id="privacyConsentEV" required checked style="margin-top:4px; cursor:pointer;">
                    <label for="privacyConsentEV" style="font-size:0.85rem; color:#475569; cursor:pointer;">
                        אני מאשר/ת את <a href="<?php echo esc_url(home_url('/privacy-policy/')); ?>" target="_blank" style="color:#0d3b66; text-decoration:underline;">מדיניות הפרטיות ותקנון השירות</a> (תשע"ז-2017) *
                    </label>
                </div>

                <button type="submit" class="lead-submit-btn">
                    קבל הצעות לעמדת טעינה ⟵
                </button>
            </form>
        </div>

        <!-- EV KPIs -->
        <div class="bento-card card-kpis-ev">
            <h3 style="font-family:'Rubik', sans-serif; color:#0d3b66; margin-top:0;">תוצאות השוואת עלויות רכב חשמלי מול בנזין</h3>
            <div class="kpi-bento-grid">
                <div class="kpi-tile">
                    <div class="kpi-label">עלות דלק חודשית (בנזין)</div>
                    <div class="kpi-val" id="kpiGasCost">₪1,038</div>
                    <div class="kpi-sub">לפי 7.50 ₪ לליטר</div>
                </div>
                <div class="kpi-tile">
                    <div class="kpi-label">עלות טעינה חודשית (חשמל)</div>
                    <div class="kpi-val" id="kpiEvCost">₪194</div>
                    <div class="kpi-sub">לפי 0.60 ₪ לקוט"ש</div>
                </div>
                <div class="kpi-tile kpi-tile-accent">
                    <div class="kpi-label">חיסכון שנתי נקי</div>
                    <div class="kpi-val green" id="kpiEvAnnualSavings">₪10,128</div>
                    <div class="kpi-sub">חיסכון חודשי: <strong id="kpiEvMonthlySavings">₪844</strong></div>
                </div>
                <div class="kpi-tile">
                    <div class="kpi-label">אחוז הפחתת ההוצאות</div>
                    <div class="kpi-val green" id="kpiEvPercent">81%</div>
                    <div class="kpi-sub">חיסכון ל-5 שנים: <strong id="kpiEvFiveYear">₪50,640</strong></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// --- Bento Calculators Reactive Logic ---
function switchBentoTab(tab) {
    const solarPanel = document.getElementById('solar-bento-panel');
    const evPanel = document.getElementById('ev-bento-panel');
    const solarBtn = document.getElementById('btn-solar-tab');
    const evBtn = document.getElementById('btn-ev-tab');

    if (tab === 'solar') {
        solarPanel.style.display = 'grid';
        evPanel.style.display = 'none';
        solarBtn.classList.add('active');
        evBtn.classList.remove('active');
    } else {
        solarPanel.style.display = 'none';
        evPanel.style.display = 'grid';
        solarBtn.classList.remove('active');
        evBtn.classList.add('active');
    }
}

// 1. Solar Calculation: 0.48 NIS/kWh injection rate, 1750 kWh/kWp radiation, 5-6 yrs payback
function calculateSolar() {
    const roof = parseFloat(document.getElementById('sliderRoofArea').value);
    const bill = parseFloat(document.getElementById('sliderMonthlyBill').value);

    document.getElementById('solarRoofBadge').textContent = roof + ' מ"ר';
    document.getElementById('solarBillBadge').textContent = bill.toLocaleString() + ' ₪';

    // 1 kWp requires ~7 m²
    const sysSize = roof / 7;
    // Radiation constant: 1750 kWh/kWp
    const annualKwh = Math.round(sysSize * 1750);
    // Injection rate: 0.48 NIS/kWh
    const annualRev = Math.round(annualKwh * 0.48);
    // Cost ~2,650 NIS per kWp installed
    const cost = Math.round(sysSize * 2650);
    // Payback period
    const payback = (cost / annualRev).toFixed(1);
    // 25-yr net profit
    const profit25 = Math.round((annualRev * 25) - cost);

    document.getElementById('kpiSystemSize').textContent = sysSize.toFixed(1) + ' kWp';
    document.getElementById('kpiAnnualKwh').textContent = annualKwh.toLocaleString() + ' קוט"ש';
    document.getElementById('kpiAnnualRev').textContent = '₪' + annualRev.toLocaleString();
    document.getElementById('kpiPayback').textContent = payback + ' שנים';
    document.getElementById('kpiProfit25').textContent = '₪' + profit25.toLocaleString();

    document.getElementById('hiddenSolarSize').value = roof;
    document.getElementById('hiddenSolarBill').value = bill;
    document.getElementById('hiddenSolarSavings').value = '₪' + annualRev.toLocaleString() + ' / שנה';
}

// 2. EV Calculation: 0.60 NIS/kWh vs 7.50 NIS/L petrol
function calculateEV() {
    const km = parseFloat(document.getElementById('sliderEvKm').value);
    const kmPerL = parseFloat(document.getElementById('sliderEvGas').value);

    document.getElementById('evKmBadge').textContent = km.toLocaleString() + ' ק"מ';
    document.getElementById('evGasBadge').textContent = kmPerL + ' ק"מ / ליטר';

    // Petrol @ 7.50 NIS/L
    const gasMonthly = Math.round((km / kmPerL) * 7.50);
    // EV @ 0.60 NIS/kWh and 0.18 kWh/km
    const evMonthly = Math.round(km * 0.18 * 0.60);
    const monthlySavings = gasMonthly - evMonthly;
    const annualSavings = monthlySavings * 12;
    const pctSavings = Math.round((monthlySavings / gasMonthly) * 100);
    const fiveYearSavings = annualSavings * 5;

    document.getElementById('kpiGasCost').textContent = '₪' + gasMonthly.toLocaleString();
    document.getElementById('kpiEvCost').textContent = '₪' + evMonthly.toLocaleString();
    document.getElementById('kpiEvMonthlySavings').textContent = '₪' + monthlySavings.toLocaleString();
    document.getElementById('kpiEvAnnualSavings').textContent = '₪' + annualSavings.toLocaleString();
    document.getElementById('kpiEvPercent').textContent = pctSavings + '%';
    document.getElementById('kpiEvFiveYear').textContent = '₪' + fiveYearSavings.toLocaleString();

    document.getElementById('hiddenEvKm').value = km;
    document.getElementById('hiddenEvBill').value = gasMonthly;
    document.getElementById('hiddenEvSavings').value = '₪' + annualSavings.toLocaleString() + ' / שנה';
}

// 3. Instant Lead Capture AJAX (0ms Friction)
async function handleLeadSubmit(event, type) {
    event.preventDefault();
    const form = (type === 'solar') ? document.getElementById('leadFormSolar') : document.getElementById('leadFormEV');
    const successBanner = (type === 'solar') ? document.getElementById('leadSuccessSolar') : document.getElementById('leadSuccessEV');
    const submitBtn = form.querySelector('.lead-submit-btn');

    submitBtn.disabled = true;
    submitBtn.textContent = 'שולח...';

    const formData = new FormData(form);
    const payload = {
        full_name: formData.get('full_name'),
        phone: formData.get('phone'),
        city: formData.get('city'),
        email: formData.get('email'),
        privacy_consent: formData.get('privacy_consent') ? 1 : 0
    };

    try {
        const response = await fetch('/wp-json/energi/v1/submit-lead', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        if (response.ok) {
            form.style.display = 'none';
            successBanner.style.display = 'block';
        } else {
            form.submit();
        }
    } catch (e) {
        form.submit();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    calculateSolar();
    calculateEV();
});
</script>

<?php get_footer(); ?>