<?php
/*
Template Name: מחשבון לידים
*/

// Block direct access
if (!defined('ABSPATH')) exit;

// Handle form submission
if ($_POST && isset($_POST['energi_lead_form'])) {
    $lead_data = array(
        'full_name' => sanitize_text_field($_POST['fullName']),
        'phone' => sanitize_text_field($_POST['phone']),
        'email' => sanitize_email($_POST['email']),
        'property_type' => sanitize_text_field($_POST['propertyType']),
        'solutions' => isset($_POST['solutions']) ? array_map('sanitize_text_field', $_POST['solutions']) : array(),
        'property_size' => sanitize_text_field($_POST['propertySize']),
        'monthly_bill' => sanitize_text_field($_POST['monthlyBill']),
        'city' => sanitize_text_field($_POST['city']),
        'contact_time' => sanitize_text_field($_POST['contactTime']),
        'notes' => sanitize_textarea_field($_POST['notes']),
        'estimated_savings' => sanitize_text_field($_POST['estimatedSavings']),
        'submission_date' => current_time('mysql'),
        'ip_address' => $_SERVER['REMOTE_ADDR'],
        'user_agent' => $_SERVER['HTTP_USER_AGENT']
    );
    
    // Save to WordPress database
    global $wpdb;
    $table_name = $wpdb->prefix . 'energi_leads';
    
    // Create table if not exists
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
    
    // Insert lead
    $solutions_json = json_encode($lead_data['solutions']);
    $wpdb->insert(
        $table_name,
        array(
            'full_name' => $lead_data['full_name'],
            'phone' => $lead_data['phone'],
            'email' => $lead_data['email'],
            'property_type' => $lead_data['property_type'],
            'solutions' => $solutions_json,
            'property_size' => $lead_data['property_size'],
            'monthly_bill' => $lead_data['monthly_bill'],
            'city' => $lead_data['city'],
            'contact_time' => $lead_data['contact_time'],
            'notes' => $lead_data['notes'],
            'estimated_savings' => $lead_data['estimated_savings'],
            'submission_date' => $lead_data['submission_date'],
            'ip_address' => $lead_data['ip_address'],
            'user_agent' => $lead_data['user_agent']
        )
    );
    
    // Send notification email to admin
    $admin_email = get_option('admin_email');
    $subject = '🔥 ליד חדש מ-Energi.co.il!';
    $message = "
    ליד חדש נשלח מהמחשבון:
    
    👤 שם: {$lead_data['full_name']}
    📞 טלפון: {$lead_data['phone']}
    📧 אימייל: {$lead_data['email']}
    🏠 סוג נכס: {$lead_data['property_type']}
    💡 פתרונות: " . implode(', ', $lead_data['solutions']) . "
    📊 גודל נכס: {$lead_data['property_size']}
    💰 חשבון חודשי: {$lead_data['monthly_bill']}
    🌍 עיר: {$lead_data['city']}
    ⏰ זמן קשר: {$lead_data['contact_time']}
    💵 חיסכון צפוי: {$lead_data['estimated_savings']}
    
    הערות: {$lead_data['notes']}
    
    תאריך: {$lead_data['submission_date']}
    ";
    
    wp_mail($admin_email, $subject, $message);
    
    // Set success flag
    $form_submitted = true;
}
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>קבל הצעת מחיר מיידית לאנרגיה ירוקה | <?php bloginfo('name'); ?></title>
    <meta name="description" content="מחשבון חיסכון באנרגיה - קבל הצעות מחיר מותאמות אישית. חיסכון של עד 40% בחשבון החשמל!">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            direction: rtl;
            color: #333;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            color: white;
            margin-bottom: 30px;
        }
        
        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .header p {
            font-size: 1.2rem;
            opacity: 0.9;
        }
        
        .urgency-banner {
            background: linear-gradient(45deg, #ff6b6b, #ee5a24);
            color: white;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 20px;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }
        
        .form-container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .progress-bar {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            position: relative;
        }
        
        .progress-step {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #666;
            position: relative;
            z-index: 2;
            transition: all 0.3s ease;
        }
        
        .progress-step.active {
            background: #4CAF50;
            color: white;
            transform: scale(1.1);
        }
        
        .progress-step.completed {
            background: #2196F3;
            color: white;
        }
        
        .progress-line {
            position: absolute;
            top: 15px;
            left: 15px;
            right: 15px;
            height: 2px;
            background: #e0e0e0;
            z-index: 1;
        }
        
        .form-step {
            display: none;
            opacity: 0;
            transform: translateX(20px);
            transition: all 0.3s ease;
        }
        
        .form-step.active {
            display: block;
            opacity: 1;
            transform: translateX(0);
            animation: slideIn 0.3s ease-in-out;
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        .form-step h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 1.8rem;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        
        input, select, textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
            font-family: inherit;
        }
        
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
            transform: translateY(-1px);
        }
        
        .checkbox-group {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }
        
        .checkbox-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
        }
        
        .checkbox-item:hover {
            border-color: #4CAF50;
            background: #f8f9fa;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .checkbox-item.selected {
            border-color: #4CAF50;
            background: #e8f5e8;
            transform: scale(1.02);
        }
        
        .checkbox-item span {
            font-weight: 500;
            font-size: 1rem;
        }
        
        .checkbox-item input[type="checkbox"],
        .checkbox-item input[type="radio"] {
            width: auto;
            margin-left: 8px;
            display: none;
        }
        
        .calculator-preview {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 25px;
            border-radius: 15px;
            margin: 20px 0;
            text-align: center;
            border: 1px solid #dee2e6;
        }
        
        .calculator-preview h3 {
            color: #495057;
            margin-bottom: 10px;
            font-size: 1.3rem;
        }
        
        .savings-estimate {
            font-size: 2.5rem;
            color: #4CAF50;
            font-weight: bold;
            margin: 15px 0;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
            animation: countUp 0.5s ease-out;
        }
        
        @keyframes countUp {
            from { transform: scale(0.8); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        
        .buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            gap: 15px;
        }
        
        .btn {
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            text-transform: none;
            position: relative;
            overflow: hidden;
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn:hover::before {
            left: 100%;
        }
        
        .btn-next {
            background: linear-gradient(135deg, #4CAF50, #45a049);
            color: white;
            flex: 1;
            max-width: 200px;
        }
        
        .btn-next:hover {
            background: linear-gradient(135deg, #45a049, #3d8b40);
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(76, 175, 80, 0.3);
        }
        
        .btn-prev {
            background: linear-gradient(135deg, #6c757d, #5a6268);
            color: white;
        }
        
        .btn-prev:hover {
            background: linear-gradient(135deg, #5a6268, #495057);
            transform: translateY(-2px);
        }
        
        .success-message {
            display: none;
            text-align: center;
            padding: 50px;
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            border-radius: 15px;
            border: 2px solid #28a745;
            color: #155724;
        }
        
        .success-message.show {
            display: block;
            animation: successFadeIn 0.5s ease-in-out;
        }
        
        @keyframes successFadeIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
        
        .success-message h2 {
            color: #155724;
            margin-bottom: 20px;
            font-size: 2rem;
        }
        
        .benefits {
            background: rgba(255,255,255,0.95);
            border-radius: 15px;
            padding: 30px;
            margin-top: 20px;
            backdrop-filter: blur(10px);
        }
        
        .benefits h3 {
            color: #333;
            margin-bottom: 20px;
            text-align: center;
            font-size: 1.5rem;
        }
        
        .benefits-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .benefit-item {
            display: flex;
            align-items: center;
            padding: 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            border: 1px solid #e9ecef;
        }
        
        .benefit-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.15);
        }
        
        .benefit-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #4CAF50, #45a049);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: 15px;
            font-size: 24px;
            color: white;
            flex-shrink: 0;
        }
        
        .benefit-item div {
            flex: 1;
        }
        
        .benefit-item strong {
            display: block;
            color: #333;
            margin-bottom: 5px;
            font-size: 1.1rem;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .container { padding: 15px; }
            .header h1 { font-size: 2rem; }
            .form-container { padding: 25px; }
            .checkbox-group { grid-template-columns: 1fr; }
            .benefits-grid { grid-template-columns: 1fr; }
            .buttons { flex-direction: column; }
            .btn { width: 100%; margin-bottom: 10px; }
            .savings-estimate { font-size: 2rem; }
        }
    </style>
    
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <div class="container">
        <div class="header">
            <h1>🔋 קבל הצעת מחיר מיידית</h1>
            <p>לפתרונות אנרגיה ירוקה מותאמים אישית</p>
        </div>
        
        <div class="urgency-banner">
            ⚡ מבצע מיוחד: חיסכון של עד 40% בחשבון החשמל!
        </div>
        
        <div class="form-container">
            <?php if (isset($form_submitted) && $form_submitted): ?>
                <div class="success-message show">
                    <h2>🎉 תודה! הבקשה שלך נשלחה בהצלחה</h2>
                    <p>נציג מקצועי יחזור אליך תוך 24 שעות עם הצעות מחיר מותאמות אישית.</p>
                    <p><strong>במקביל, תקבל למייל:</strong></p>
                    <ul style="text-align: right; margin: 20px 0;">
                        <li>מדריך מלא לחיסכון באנרגיה</li>
                        <li>מחשבון ROI אישי</li>
                        <li>רשימת ספקים מומלצים באזורך</li>
                    </ul>
                    <a href="<?php echo get_permalink(); ?>" class="btn btn-next">הגש בקשה נוספת</a>
                </div>
            <?php else: ?>
                <div class="progress-bar">
                    <div class="progress-line"></div>
                    <div class="progress-step active">1</div>
                    <div class="progress-step">2</div>
                    <div class="progress-step">3</div>
                    <div class="progress-step">4</div>
                </div>
                
                <form id="leadForm" method="post" action="">
                    <input type="hidden" name="energi_lead_form" value="1">
                    <input type="hidden" name="estimatedSavings" id="hiddenSavings" value="₪0">
                    
                    <!-- Step 1: Property Type -->
                    <div class="form-step active" id="step1">
                        <h2>איזה סוג נכס יש לך?</h2>
                        <div class="checkbox-group">
                            <div class="checkbox-item" onclick="selectOption(this, 'propertyType', 'house')">
                                <span>🏠 בית פרטי</span>
                                <input type="radio" name="propertyType" value="house">
                            </div>
                            <div class="checkbox-item" onclick="selectOption(this, 'propertyType', 'apartment')">
                                <span>🏢 דירה</span>
                                <input type="radio" name="propertyType" value="apartment">
                            </div>
                            <div class="checkbox-item" onclick="selectOption(this, 'propertyType', 'business')">
                                <span>💼 עסק</span>
                                <input type="radio" name="propertyType" value="business">
                            </div>
                            <div class="checkbox-item" onclick="selectOption(this, 'propertyType', 'building')">
                                <span>🏬 בניין מגורים</span>
                                <input type="radio" name="propertyType" value="building">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Step 2: Energy Solutions -->
                    <div class="form-step" id="step2">
                        <h2>באיזה פתרונות אתה מעוניין?</h2>
                        <div class="checkbox-group">
                            <div class="checkbox-item" onclick="toggleCheckbox(this, 'solar')">
                                <span>☀️ מערכת סולארית</span>
                                <input type="checkbox" name="solutions[]" value="solar">
                            </div>
                            <div class="checkbox-item" onclick="toggleCheckbox(this, 'battery')">
                                <span>🔋 מערכת אגירה</span>
                                <input type="checkbox" name="solutions[]" value="battery">
                            </div>
                            <div class="checkbox-item" onclick="toggleCheckbox(this, 'smart_home')">
                                <span>🏠 בית חכם</span>
                                <input type="checkbox" name="solutions[]" value="smart_home">
                            </div>
                            <div class="checkbox-item" onclick="toggleCheckbox(this, 'led_lighting')">
                                <span>💡 תאורת LED</span>
                                <input type="checkbox" name="solutions[]" value="led_lighting">
                            </div>
                            <div class="checkbox-item" onclick="toggleCheckbox(this, 'heat_pump')">
                                <span>🌡️ משאבת חום</span>
                                <input type="checkbox" name="solutions[]" value="heat_pump">
                            </div>
                            <div class="checkbox-item" onclick="toggleCheckbox(this, 'electric_vehicle')">
                                <span>🚗 עמדת טעינה לרכב חשמלי</span>
                                <input type="checkbox" name="solutions[]" value="electric_vehicle">
                            </div>
                        </div>
                        
                        <div class="calculator-preview">
                            <h3>חיסכון משמעותי צפוי:</h3>
                            <div class="savings-estimate" id="savingsEstimate">₪0</div>
                            <p>חיסכון שנתי בחשבון החשמל</p>
                        </div>
                    </div>
                    
                    <!-- Step 3: Property Details -->
                    <div class="form-step" id="step3">
                        <h2>פרטי הנכס</h2>
                        <div class="form-group">
                            <label>גודל הנכס (מ"ר)</label>
                            <select name="propertySize" onchange="updateSavings()">
                                <option value="">בחר גודל</option>
                                <option value="50">עד 50 מ"ר</option>
                                <option value="100">51-100 מ"ר</option>
                                <option value="150">101-150 מ"ר</option>
                                <option value="200">151-200 מ"ר</option>
                                <option value="300">200+ מ"ר</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>חשבון חשמל חודשי ממוצע</label>
                            <select name="monthlyBill" onchange="updateSavings()">
                                <option value="">בחר סכום</option>
                                <option value="200">עד ₪200</option>
                                <option value="400">₪201-400</option>
                                <option value="600">₪401-600</option>
                                <option value="800">₪601-800</option>
                                <option value="1000">₪800+</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>עיר</label>
                            <input type="text" name="city" placeholder="לדוגמה: תל אביב">
                        </div>
                    </div>
                    
                    <!-- Step 4: Contact Details -->
                    <div class="form-step" id="step4">
                        <h2>פרטי יצירת קשר</h2>
                        <div class="form-group">
                            <label>שם מלא *</label>
                            <input type="text" name="fullName" required placeholder="הכנס את שמך המלא">
                        </div>
                        
                        <div class="form-group">
                            <label>טלפון *</label>
                            <input type="tel" name="phone" required placeholder="050-1234567">
                        </div>
                        
                        <div class="form-group">
                            <label>אימייל</label>
                            <input type="email" name="email" placeholder="example@gmail.com">
                        </div>
                        
                        <div class="form-group">
                            <label>מתי נוח לך שניצור קשר?</label>
                            <select name="contactTime">
                                <option value="morning">בוקר (9:00-12:00)</option>
                                <option value="afternoon">צהריים (12:00-16:00)</option>
                                <option value="evening">ערב (16:00-20:00)</option>
                                <option value="anytime">כל זמן שמתאים</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>הערות נוספות</label>
                            <textarea name="notes" rows="3" placeholder="ספר לנו יותר על הפרויקט שלך..."></textarea>
                        </div>
                    </div>
                    
                    <div class="buttons">
                        <button type="button" class="btn btn-prev" id="prevBtn" onclick="prevStep()" style="display:none;">חזור</button>
                        <button type="button" class="btn btn-next" id="nextBtn" onclick="nextStep()">המשך</button>
                        <button type="submit" class="btn btn-next" id="submitBtn" style="display:none;">קבל הצעות מחיר 🚀</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
        
        <div class="benefits">
            <h3>למה לבחור ב-<?php bloginfo('name'); ?>?</h3>
            <div class="benefits-grid">
                <div class="benefit-item">
                    <div class="benefit-icon">⚡</div>
                    <div>
                        <strong>חיסכון מיידי</strong><br>
                        עד 40% הפחתה בחשבון החשמל
                    </div>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon">🔍</div>
                    <div>
                        <strong>השוואת מחירים</strong><br>
                        מספקים מובילים במחיר הטוב ביותר
                    </div>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon">🛡️</div>
                    <div>
                        <strong>ספקים מאומתים</strong><br>
                        רק ספקים עם רישיון ומוניטין
                    </div>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon">📞</div>
                    <div>
                        <strong>ליווי מקצועי</strong><br>
                        מייעוץ ועד התקנה מלאה
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentStep = 1;
        const totalSteps = 4;
        
        function nextStep() {
            if (validateCurrentStep()) {
                if (currentStep < totalSteps) {
                    currentStep++;
                    showStep(currentStep);
                    updateProgressBar();
                }
            }
        }
        
        function prevStep() {
            if (currentStep > 1) {
                currentStep--;
                showStep(currentStep);
                updateProgressBar();
            }
        }
        
        function showStep(step) {
            document.querySelectorAll('.form-step').forEach(s => s.classList.remove('active'));
            document.getElementById(`step${step}`).classList.add('active');
            
            document.getElementById('prevBtn').style.display = step > 1 ? 'inline-block' : 'none';
            document.getElementById('nextBtn').style.display = step < totalSteps ? 'inline-block' : 'none';
            document.getElementById('submitBtn').style.display = step === totalSteps ? 'inline-block' : 'none';
        }
        
        function updateProgressBar() {
            document.querySelectorAll('.progress-step').forEach((step, index) => {
                step.classList.remove('active', 'completed');
                if (index + 1 < currentStep) {
                    step.classList.add('completed');
                } else if (index + 1 === currentStep) {
                    step.classList.add('active');
                }
            });
        }
        
        function validateCurrentStep() {
            const currentStepElement = document.getElementById(`step${currentStep}`);
            const requiredInputs = currentStepElement.querySelectorAll('input[required], select[required]');
            
            for (let input of requiredInputs) {
                if (!input.value.trim()) {
                    input.focus();
                    alert('אנא מלא את כל השדות הנדרשים');
                    return false;
                }
            }
            
            if (currentStep === 1) {
                const propertyType = document.querySelector('input[name="propertyType"]:checked');
                if (!propertyType) {
                    alert('אנא בחר סוג נכס');
                    return false;
                }
            }
            
            if (currentStep === 2) {
                const solutions = document.querySelectorAll('input[name="solutions[]"]:checked');
                if (solutions.length === 0) {
                    alert('אנא בחר לפחות פתרון אחד');
                    return false;
                }
            }
            
            return true;
        }
        
        function selectOption(element, name, value) {
            element.parentElement.querySelectorAll('.checkbox-item').forEach(item => {
                item.classList.remove('selected');
            });
            
            element.classList.add('selected');
            
            const radio = element.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
            }
            
            updateSavings();
        }
        
        function toggleCheckbox(element, value) {
            const checkbox = element.querySelector('input[type="checkbox"]');
            checkbox.checked = !checkbox.checked;
            
            if (checkbox.checked) {
                element.classList.add('selected');
            } else {
                element.classList.remove('selected');
            }
            
            updateSavings();
        }
        
        function updateSavings() {
            let baseSavings = 0;
            const solutions = document.querySelectorAll('input[name="solutions[]"]:checked');
            const monthlyBill = document.querySelector('select[name="monthlyBill"]')?.value || 0;
            const propertySize = document.querySelector('select[name="propertySize"]')?.value || 0;
            
            solutions.forEach(solution => {
                switch(solution.value) {
                    case 'solar': baseSavings += 2000; break;
                    case 'battery': baseSavings += 1500; break;
                    case 'smart_home': baseSavings += 800; break;
                    case 'led_lighting': baseSavings += 600; break;
                    case 'heat_pump': baseSavings += 1200; break;
                    case 'electric_vehicle': baseSavings += 1000; break;
                }
            });
            
            if (propertySize > 100) baseSavings *= 1.5;
            if (propertySize > 200) baseSavings *= 2;
            
            if (monthlyBill > 400) baseSavings *= 1.3;
            if (monthlyBill > 800) baseSavings *= 1.8;
            
            const savingsText = `₪${Math.round(baseSavings).toLocaleString()}`;
            document.getElementById('savingsEstimate').textContent = savingsText;
            document.getElementById('hiddenSavings').value = savingsText;
        }
        
        // Initialize
        updateSavings();
    </script>
    
    <?php wp_footer(); ?>
</body>
</html>