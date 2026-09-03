<?php
//kill Gutenberg stylesheet
function wp_dequeue_gutenberg_styles() {
    wp_dequeue_style( 'wp-block-library' );
    wp_dequeue_style( 'wp-block-library-theme' );
}
add_action( 'wp_print_styles', 'wp_dequeue_gutenberg_styles', 100 );

/* Remove type attribute from scripts and styles */
function myplugin_remove_type_attr($tag, $handle) {
    return preg_replace( "/type=['\"]text\/(javascript|css)['\"]/", '', $tag );
}

// Random ordered on category pages
function shailan_post_order( $query ) {
    if ( $query->is_archive ) {
        $query->set( 'orderby', 'rand' );
    }
}
add_action( 'pre_get_posts', 'shailan_post_order' );

/**
 * Show all Portfolio CPT items on archive
 */

/*function to add async to all scripts*/
/*
function js_async_attr($tag){
  //Add async to all  scripts tags
  return str_replace( ' src', ' async="async" src', $tag );
}
add_filter( 'script_loader_tag', 'js_async_attr', 10 );
*/

// ========================================
// 🔋 ENERGI v3.0 - מערכת לידים עצמאית ומושלמת
// ללא תלות בשירותים חיצוניים!
// ========================================

// יצירת API endpoint עצמאי לטופס הלידים
add_action('rest_api_init', function () {
    register_rest_route('energi/v1', '/submit-lead', array(
        'methods' => 'POST',
        'callback' => 'energi_handle_lead_submission',
        'permission_callback' => '__return_true', // גישה ציבורית
        'args' => array(
            'fullName' => array(
                'required' => true,
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'phone' => array(
                'required' => true,
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'email' => array(
                'required' => false,
                'sanitize_callback' => 'sanitize_email',
            ),
        ),
    ));
});

// פונקציה לטיפול בליד חדש
function energi_handle_lead_submission(WP_REST_Request $request) {
    // איסוף הנתונים מהטופס
    $data = $request->get_json_params();
    
    // בדיקת נתונים בסיסיים
    if (empty($data['fullName']) || empty($data['phone'])) {
        return new WP_REST_Response(array(
            'success' => false, 
            'message' => 'חסרים פרטים חיוניים'
        ), 400);
    }
    
    // ניקוי ואבטחת הנתונים
    $clean_data = array(
        'fullName' => sanitize_text_field($data['fullName']),
        'phone' => sanitize_text_field($data['phone']),
        'email' => sanitize_email($data['email'] ?? ''),
        'propertyType' => sanitize_text_field($data['propertyType'] ?? ''),
        'solutions' => sanitize_textarea_field($data['solutions'] ?? ''),
        'city' => sanitize_text_field($data['city'] ?? ''),
        'estimatedSavings' => sanitize_text_field($data['estimatedSavings'] ?? ''),
        'submission_date' => current_time('mysql'),
        'user_ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'page_url' => esc_url_raw($data['pageUrl'] ?? ''),
    );
    
    // שמירת הליד במסד הנתונים
    $lead_id = energi_save_lead_to_database($clean_data);
    
    // שליחת מיילים
    $email_sent = energi_send_lead_notifications($clean_data, $lead_id);
    
    if ($lead_id && $email_sent) {
        return new WP_REST_Response(array(
            'success' => true,
            'message' => 'הליד נשמר בהצלחה!',
            'lead_id' => $lead_id
        ), 200);
    } else {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => 'שגיאה בשמירת הליד'
        ), 500);
    }
}

// שמירת הליד במסד הנתונים
function energi_save_lead_to_database($data) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'energi_leads';
    
    // וידוא שהטבלה קיימת
    energi_create_leads_table();
    
    // שמירת הנתונים
    $result = $wpdb->insert(
        $table_name,
        array(
            'full_name' => $data['fullName'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'property_type' => $data['propertyType'],
            'solutions' => $data['solutions'],
            'city' => $data['city'],
            'estimated_savings' => $data['estimatedSavings'],
            'submission_date' => $data['submission_date'],
            'user_ip' => $data['user_ip'],
            'page_url' => $data['page_url'],
            'status' => 'new'
        ),
        array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
    );
    
    return $result ? $wpdb->insert_id : false;
}

// יצירת טבלת הלידים
function energi_create_leads_table() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'energi_leads';
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        full_name tinytext NOT NULL,
        phone varchar(20) NOT NULL,
        email varchar(100) DEFAULT '',
        property_type varchar(50) DEFAULT '',
        solutions text DEFAULT '',
        city varchar(100) DEFAULT '',
        estimated_savings varchar(20) DEFAULT '',
        submission_date datetime DEFAULT CURRENT_TIMESTAMP,
        user_ip varchar(45) DEFAULT '',
        page_url varchar(255) DEFAULT '',
        status varchar(20) DEFAULT 'new',
        notes text DEFAULT '',
        PRIMARY KEY (id),
        INDEX phone_idx (phone),
        INDEX status_idx (status),
        INDEX date_idx (submission_date)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// שליחת התראות מייל
function energi_send_lead_notifications($data, $lead_id) {
    $admin_email = get_option('admin_email');
    $site_name = get_bloginfo('name');
    
    // מייל למנהל - עיצוב מקצועי
    $subject = "🔥 ליד חדש #{$lead_id} מ-{$site_name}";
    
    $admin_body = '
    <div dir="rtl" style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background: #f9f9f9;">
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; border-radius: 10px; color: white; text-align: center; margin-bottom: 20px;">
            <h1 style="margin: 0; font-size: 24px;">🔋 ליד חדש!</h1>
            <p style="margin: 10px 0 0 0; opacity: 0.9;">מחשבון האנרגיה באתר</p>
        </div>
        
        <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <h2 style="color: #333; margin-bottom: 20px; border-bottom: 2px solid #4CAF50; padding-bottom: 10px;">📋 פרטי הליד</h2>
            
            <table style="width: 100%; border-collapse: collapse;">
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 12px; font-weight: bold; color: #555; width: 40%;">👤 שם מלא:</td>
                    <td style="padding: 12px; color: #333;">' . esc_html($data['fullName']) . '</td>
                </tr>
                <tr style="border-bottom: 1px solid #eee; background: #f8f9fa;">
                    <td style="padding: 12px; font-weight: bold; color: #555;">📞 טלפון:</td>
                    <td style="padding: 12px;"><a href="tel:' . esc_attr($data['phone']) . '" style="color: #4CAF50; text-decoration: none; font-weight: bold;">' . esc_html($data['phone']) . '</a></td>
                </tr>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 12px; font-weight: bold; color: #555;">📧 אימייל:</td>
                    <td style="padding: 12px;">' . ($data['email'] ? '<a href="mailto:' . esc_attr($data['email']) . '" style="color: #4CAF50; text-decoration: none;">' . esc_html($data['email']) . '</a>' : '<span style="color: #999;">לא סופק</span>') . '</td>
                </tr>
                <tr style="border-bottom: 1px solid #eee; background: #f8f9fa;">
                    <td style="padding: 12px; font-weight: bold; color: #555;">🏠 סוג נכס:</td>
                    <td style="padding: 12px;">' . esc_html($data['propertyType'] ?: 'לא צוין') . '</td>
                </tr>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 12px; font-weight: bold; color: #555;">⚡ פתרונות:</td>
                    <td style="padding: 12px;">' . esc_html($data['solutions'] ?: 'לא צוין') . '</td>
                </tr>
                <tr style="border-bottom: 1px solid #eee; background: #f8f9fa;">
                    <td style="padding: 12px; font-weight: bold; color: #555;">🌍 עיר:</td>
                    <td style="padding: 12px;">' . esc_html($data['city'] ?: 'לא צוין') . '</td>
                </tr>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 12px; font-weight: bold; color: #555;">💰 חיסכון צפוי:</td>
                    <td style="padding: 12px; color: #4CAF50; font-weight: bold;">' . esc_html($data['estimatedSavings'] ?: 'לא חושב') . '</td>
                </tr>
                <tr style="background: #f8f9fa;">
                    <td style="padding: 12px; font-weight: bold; color: #555;">📅 תאריך:</td>
                    <td style="padding: 12px;">' . date('d/m/Y H:i', strtotime($data['submission_date'])) . '</td>
                </tr>
            </table>
            
            <div style="margin-top: 30px; padding: 20px; background: #e8f5e8; border-radius: 8px; border-right: 4px solid #4CAF50;">
                <h3 style="color: #2e7d32; margin: 0 0 15px 0;">🚀 פעולות מומלצות:</h3>
                <ul style="margin: 0; padding-right: 20px; color: #2e7d32;">
                    <li><strong>התקשר ללקוח תוך 2-4 שעות</strong> (זמן התגובה קריטי!)</li>
                    <li>הכן הצעת מחיר מותאמת לנתונים שהוא מסר</li>
                    <li>קבע פגישת ייעוץ חינם בביתו</li>
                    <li>שלח לו את מדריך החיסכון באנרגיה</li>
                </ul>
            </div>
            
            <div style="text-align: center; margin-top: 30px;">
                <a href="' . home_url('/wp-admin/admin.php?page=energi-leads') . '" style="background: #4CAF50; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold;">👥 ניהול כל הלידים</a>
            </div>
        </div>
        
        <div style="text-align: center; margin-top: 20px; color: #999; font-size: 12px;">
            <p>מערכת לידים אוטומטית - ' . $site_name . '</p>
        </div>
    </div>';
    
    // שליחת המייל למנהל
    $headers = array('Content-Type: text/html; charset=UTF-8');
    $admin_sent = wp_mail($admin_email, $subject, $admin_body, $headers);
    
    // מייל אישור ללקוח (אם יש אימייל)
    if (!empty($data['email'])) {
        energi_send_customer_confirmation($data);
    }
    
    return $admin_sent;
}

// מייל אישור ללקוח
function energi_send_customer_confirmation($data) {
    $site_name = get_bloginfo('name');
    $subject = "תודה על פנייתך ל-{$site_name} 🔋";
    
    $customer_body = '
    <div dir="rtl" style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background: #f9f9f9;">
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; border-radius: 10px; color: white; text-align: center; margin-bottom: 20px;">
            <h1 style="margin: 0; font-size: 24px;">🎉 תודה רבה!</h1>
            <p style="margin: 10px 0 0 0; opacity: 0.9;">קיבלנו את פנייתך בהצלחה</p>
        </div>
        
        <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <p style="font-size: 18px; color: #333; margin-bottom: 20px;">שלום <strong>' . esc_html($data['fullName']) . '</strong>,</p>
            
            <p style="color: #666; line-height: 1.6;">תודה על פנייתך אלינו לקבלת הצעת מחיר לפתרונות אנרגיה ירוקה. קיבלנו את פרטיך ונציג מקצועי יחזור אליך בהקדם.</p>
            
            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; border-right: 4px solid #4CAF50;">
                <h3 style="color: #333; margin: 0 0 15px 0;">📋 סיכום הבקשה שלך:</h3>
                <ul style="margin: 0; padding-right: 20px; color: #555;">
                    <li><strong>סוג נכס:</strong> ' . esc_html($data['propertyType'] ?: 'לא צוין') . '</li>
                    <li><strong>פתרונות מעניינים:</strong> ' . esc_html($data['solutions'] ?: 'לא צוין') . '</li>
                    <li><strong>עיר:</strong> ' . esc_html($data['city'] ?: 'לא צוין') . '</li>
                    <li><strong>חיסכון שנתי צפוי:</strong> <span style="color: #4CAF50; font-weight: bold;">' . esc_html($data['estimatedSavings'] ?: 'לא חושב') . '</span></li>
                </ul>
            </div>
            
            <div style="background: #e8f5e8; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h3 style="color: #2e7d32; margin: 0 0 15px 0;">🎯 מה הלאה?</h3>
                <ul style="margin: 0; padding-right: 20px; color: #2e7d32;">
                    <li>נציג מקצועי יחזור אליך <strong>תוך 24 שעות</strong></li>
                    <li>נכין עבורך <strong>הצעת מחיר מותאמת אישית</strong></li>
                    <li>נקבע <strong>ייעוץ חינם בביתך</strong> בזמן שנוח לך</li>
                    <li>תקבל <strong>מדריכים שימושיים</strong> למייל זה</li>
                </ul>
            </div>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="' . home_url('/calculator') . '" style="background: #4CAF50; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; margin: 0 10px;">🧮 מחשבון נוסף</a>
                <a href="' . home_url() . '" style="background: #2196F3; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; margin: 0 10px;">🏠 לאתר הראשי</a>
            </div>
            
            <p style="color: #666; font-size: 14px; text-align: center; margin-top: 30px;">
                שאלות? צור קשר: <a href="mailto:' . get_option('admin_email') . '" style="color: #4CAF50;">' . get_option('admin_email') . '</a>
            </p>
        </div>
        
        <div style="text-align: center; margin-top: 20px; color: #999; font-size: 12px;">
            <p>בברכה, צוות ' . $site_name . '</p>
        </div>
    </div>';
    
    $headers = array('Content-Type: text/html; charset=UTF-8');
    return wp_mail($data['email'], $subject, $customer_body, $headers);
}

// הוספת עמוד ניהול לידים מקצועי
add_action('admin_menu', 'energi_add_admin_pages');

function energi_add_admin_pages() {
    add_menu_page(
        'לידים - Energi',
        'לידים 🔋',
        'manage_options',
        'energi-leads',
        'energi_leads_admin_page',
        'dashicons-groups',
        26
    );
    
    add_submenu_page(
        'energi-leads',
        'סטטיסטיקות',
        'סטטיסטיקות 📊',
        'manage_options',
        'energi-stats',
        'energi_stats_admin_page'
    );
}

// עמוד ניהול לידים מקצועי
function energi_leads_admin_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'energi_leads';
    
    // טיפול בעדכון סטטוס
    if (isset($_POST['update_status']) && wp_verify_nonce($_POST['_wpnonce'], 'update_lead_status')) {
        $lead_id = intval($_POST['lead_id']);
        $new_status = sanitize_text_field($_POST['new_status']);
        
        $updated = $wpdb->update(
            $table_name,
            array('status' => $new_status),
            array('id' => $lead_id),
            array('%s'),
            array('%d')
        );
        
        if ($updated !== false) {
            echo '<div class="notice notice-success"><p>✅ סטטוס הליד עודכן בהצלחה!</p></div>';
        }
    }
    
    // סטטיסטיקות
    $total_leads = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    $new_leads = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'new'");
    $today_leads = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE DATE(submission_date) = CURDATE()");
    $week_leads = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE submission_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
    
    // קבלת לידים
    $leads = $wpdb->get_results("SELECT * FROM $table_name ORDER BY submission_date DESC LIMIT 100");
    
    ?>
    <div class="wrap">
        <h1>🔋 ניהול לידים - Energi.co.il</h1>
        
        <!-- כרטיסי סטטיסטיקות -->
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin: 20px 0;">
            <div style="background: #0073aa; color: white; padding: 20px; border-radius: 8px; text-align: center;">
                <h3 style="margin: 0; color: white;">סה"כ לידים</h3>
                <div style="font-size: 2.5em; font-weight: bold; margin: 10px 0;"><?php echo $total_leads; ?></div>
            </div>
            <div style="background: #00a32a; color: white; padding: 20px; border-radius: 8px; text-align: center;">
                <h3 style="margin: 0; color: white;">לידים חדשים</h3>
                <div style="font-size: 2.5em; font-weight: bold; margin: 10px 0;"><?php echo $new_leads; ?></div>
            </div>
            <div style="background: #ff8c20; color: white; padding: 20px; border-radius: 8px; text-align: center;">
                <h3 style="margin: 0; color: white;">היום</h3>
                <div style="font-size: 2.5em; font-weight: bold; margin: 10px 0;"><?php echo $today_leads; ?></div>
            </div>
            <div style="background: #8b5cf6; color: white; padding: 20px; border-radius: 8px; text-align: center;">
                <h3 style="margin: 0; color: white;">השבוע</h3>
                <div style="font-size: 2.5em; font-weight: bold; margin: 10px 0;"><?php echo $week_leads; ?></div>
            </div>
        </div>
        
        <?php if ($leads): ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>שם</th>
                    <th>טלפון</th>
                    <th>אימייל</th>
                    <th>נכס</th>
                    <th>חיסכון</th>
                    <th>עיר</th>
                    <th>תאריך</th>
                    <th>סטטוס</th>
                    <th>פעולות</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($leads as $lead): ?>
                <tr>
                    <td><strong>#<?php echo $lead->id; ?></strong></td>
                    <td><strong><?php echo esc_html($lead->full_name); ?></strong></td>
                    <td>
                        <a href="tel:<?php echo esc_attr($lead->phone); ?>" style="color: #0073aa; font-weight: bold;">
                            <?php echo esc_html($lead->phone); ?>
                        </a>
                    </td>
                    <td>
                        <?php if ($lead->email): ?>
                            <a href="mailto:<?php echo esc_attr($lead->email); ?>" style="color: #0073aa;">
                                <?php echo esc_html($lead->email); ?>
                            </a>
                        <?php else: ?>
                            <span style="color: #999;">לא סופק</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo esc_html($lead->property_type ?: 'לא צוין'); ?></td>
                    <td style="color: #00a32a; font-weight: bold;"><?php echo esc_html($lead->estimated_savings ?: 'לא חושב'); ?></td>
                    <td><?php echo esc_html($lead->city ?: 'לא צוין'); ?></td>
                    <td><?php echo date('d/m H:i', strtotime($lead->submission_date)); ?></td>
                    <td>
                        <form method="post" style="display: inline;">
                            <?php wp_nonce_field('update_lead_status'); ?>
                            <select name="new_status" onchange="this.form.submit()" style="font-size: 12px;">
                                <option value="new"<?php selected($lead->status, 'new'); ?>>🆕 חדש</option>
                                <option value="contacted"<?php selected($lead->status, 'contacted'); ?>>📞 יצרתי קשר</option>
                                <option value="qualified"<?php selected($lead->status, 'qualified'); ?>>👍 מעוניין</option>
                                <option value="converted"<?php selected($lead->status, 'converted'); ?>>💰 נסגר</option>
                                <option value="closed"<?php selected($lead->status, 'closed'); ?>>❌ סגור</option>
                            </select>
                            <input type="hidden" name="lead_id" value="<?php echo $lead->id; ?>">
                            <input type="hidden" name="update_status" value="1">
                        </form>
                    </td>
                    <td>
                        <button onclick="showLeadDetails(<?php echo $lead->id; ?>)" class="button button-small">
                            👁️ פרטים
                        </button>
                    </td>
                </tr>
                
                <!-- שורה נסתרת עם פרטים מלאים -->
                <tr id="details-<?php echo $lead->id; ?>" style="display: none; background: #f9f9f9;">
                    <td colspan="10" style="padding: 20px;">
                        <h4>📋 פרטים מלאים - ליד #<?php echo $lead->id; ?></h4>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div>
                                <p><strong>פתרונות מבוקשים:</strong><br><?php echo nl2br(esc_html($lead->solutions ?: 'לא צוין')); ?></p>
                                <p><strong>כתובת IP:</strong> <?php echo esc_html($lead->user_ip); ?></p>
                            </div>
                            <div>
                                <p><strong>דף מקור:</strong><br><a href="<?php echo esc_url($lead->page_url); ?>" target="_blank">צפה בדף</a></p>
                                <p><strong>תאריך מלא:</strong> <?php echo date('d/m/Y H:i:s', strtotime($lead->submission_date)); ?></p>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <?php if ($total_leads > 100): ?>
        <p style="margin-top: 20px;"><em>מוצגים 100 הלידים האחרונים מתוך <?php echo $total_leads; ?> סה"כ.</em></p>
        <?php endif; ?>
        
        <?php else: ?>
        <div style="text-align: center; padding: 50px; background: #f9f9f9; border-radius: 8px; margin: 20px 0;">
            <h3>😴 אין לידים עדיין</h3>
            <p>ברגע שמישהו ימלא את הטופס במחשבון, הלידים יופיעו כאן.</p>
            <a href="<?php echo home_url('/calculator'); ?>" class="button button-primary" target="_blank">🧮 צפה במחשבון</a>
        </div>
        <?php endif; ?>
    </div>
    
    <script>
    function showLeadDetails(leadId) {
        var row = document.getElementById('details-' + leadId);
        if (row.style.display === 'none') {
            row.style.display = 'table-row';
        } else {
            row.style.display = 'none';
        }
    }
    </script>
    <?php
}

// עמוד סטטיסטיקות
function energi_stats_admin_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'energi_leads';
    
    echo '<div class="wrap">';
    echo '<h1>📊 סטטיסטיקות לידים - Energi.co.il</h1>';
    echo '<p>עמוד זה יכיל גרפים וסטטיסטיקות מתקדמות (בפיתוח)</p>';
    echo '</div>';
}

// יצירת הטבלה בהפעלת התבנית
add_action('after_switch_theme', 'energi_create_leads_table');

// הוספת הודעה מקצועת במידה והליד נשמר בהצלחה
add_action('admin_notices', 'energi_admin_notices');

function energi_admin_notices() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'energi_leads';
    $new_leads = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'new'");
    
    if ($new_leads > 0) {
        echo '<div class="notice notice-info">';
        echo '<p>🔋 <strong>Energi:</strong> יש לך ' . $new_leads . ' לידים חדשים הממתינים לטיפול! ';
        echo '<a href="' . admin_url('admin.php?page=energi-leads') . '">צפה בלידים</a></p>';
        echo '</div>';
    }
}




// הוסף את הקוד הזה לקובץ functions.php של הערכת העיצוב

// הוספת CTA אוטומטית לסוף כל פוסט
add_filter('the_content', 'energi_add_cta_to_posts');

function energi_add_cta_to_posts($content) {
    // רק בפוסט בודד ולא בעמודים או בארכיון
    if (!is_single() || is_page()) {
        return $content;
    }
    
    // רק בפוסטים מסוג 'post'
    if (get_post_type() !== 'post') {
        return $content;
    }
    
    // קישור למחשבון
    $calculator_url = site_url('/calculator');
    
    // HTML של ה-CTA
    $cta_html = '
    <div style="border: 2px solid #4CAF50; padding: 20px; border-radius: 10px; margin: 25px 0; text-align: center; background: linear-gradient(135deg, #f8fff8, #e8f5e8);">
        <p style="margin: 0 0 15px 0; font-size: 18px; color: #333;">
            <strong>🤔 יש לכם שאלות נוספות על חיסכון באנרגיה?</strong>
        </p>
        <p style="margin: 0 0 15px 0; color: #666;">
            קבלו ייעוץ מקצועי חינם ופרטי התאמה ישירות לבית שלכם
        </p>
        <a href="' . $calculator_url . '" style="display: inline-block; background: linear-gradient(135deg, #4CAF50, #45a049); color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 16px; transition: all 0.3s ease;">
            קבל הצעת מחיר מיידית 🔋
        </a>
        <p style="margin: 15px 0 0 0; font-size: 12px; color: #888;">
            ✅ חינמי לחלוטין | ✅ תוצאות מיידיות | ✅ ספקים מאומתים
        </p>
    </div>';
    
    return $content . $cta_html;
}

// CTA דינמי לפי קטגוריות
add_filter('the_content', 'energi_add_dynamic_cta_to_posts');

function energi_add_dynamic_cta_to_posts($content) {
    if (!is_single() || is_page()) {
        return $content;
    }
    
    if (get_post_type() !== 'post') {
        return $content;
    }
    
    $calculator_url = site_url('/calculator');
    $post_categories = get_the_category();
    
    // CTA מותאם לפי קטגוריה
    $cta_config = array(
        'solar' => array(
            'title' => '☀️ כמה תחסכו עם מערכת סולארית?',
            'description' => 'המחשבון החכם שלנו יראה לכם בדיוק כמה תחסכו ומתי ההשקעה תחזור לעצמה',
            'button' => 'חשבו את החיסכון שלכם 🧮',
            'gradient' => 'linear-gradient(45deg, #FFA726, #FF7043)',
            'stats' => '💡 לקוחות שלנו חוסכים בממוצע ₪2,400 בשנה'
        ),
        'smart-home' => array(
            'title' => '🏠 מוכנים לבית חכם שחוסך כסף?',
            'description' => 'גלו איך פתרונות בית חכם יכולים להוריד את חשבון החשמל שלכם ב-25%-40%',
            'button' => 'קבל הצעת מחיר לבית חכם 🏡',
            'gradient' => 'linear-gradient(135deg, #43A047, #66BB6A)',
            'stats' => '🎯 התקנה מקצועית | 📱 שליטה מהטלפון | 💰 החזר השקעה מהיר'
        ),
        'energy-saving' => array(
            'title' => '⚡ רוצים לחסוך יותר בחשבון החשמל?',
            'description' => 'בדקו עכשיو בחינם במחשבון האנרגיה החכם שלנו וקבלו הצעות מחיר מותאמות אישית',
            'button' => 'מחשבון חיסכון מיידי ⚡',
            'gradient' => 'linear-gradient(135deg, #667eea, #764ba2)',
            'stats' => '✅ חינמי לחלוטין | ✅ תוצאות מיידיות | ✅ ספקים מאומתים'
        )
    );
    
    // ברירת מחדל
    $selected_cta = $cta_config['energy-saving'];
    
    // בדיקה אם הפוסט שייך לקטגוריה ספציפית
    foreach ($post_categories as $category) {
        $slug = $category->slug;
        if (isset($cta_config[$slug])) {
            $selected_cta = $cta_config[$slug];
            break;
        }
        
        // בדיקות נוספות לפי שמות קטגוריות
        if (strpos($slug, 'solar') !== false || strpos($category->name, 'סולאר') !== false) {
            $selected_cta = $cta_config['solar'];
            break;
        }
        if (strpos($slug, 'smart') !== false || strpos($category->name, 'חכם') !== false) {
            $selected_cta = $cta_config['smart-home'];
            break;
        }
    }
    
    $dynamic_cta = '
    <div style="background: ' . $selected_cta['gradient'] . '; color: white; padding: 25px; border-radius: 15px; margin: 30px 0; text-align: center; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
        <h3 style="margin: 0 0 15px 0; font-size: 24px;">' . $selected_cta['title'] . '</h3>
        <p style="margin: 15px 0; font-size: 18px; opacity: 0.9;">' . $selected_cta['description'] . '</p>
        <a href="' . $calculator_url . '" style="display: inline-block; background: #4CAF50; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 18px; margin-top: 15px; transition: all 0.3s ease;">
            ' . $selected_cta['button'] . '
        </a>
        <p style="margin: 15px 0 0 0; font-size: 14px; opacity: 0.8;">' . $selected_cta['stats'] . '</p>
    </div>';
    
    return $content . $dynamic_cta;
}

// פונקציה להוספת CTA ידנית לפוסט ספציפי
function energi_manual_cta($type = 'default') {
    $calculator_url = site_url('/calculator');
    
    $cta_types = array(
        'urgent' => '
        <div style="background: linear-gradient(45deg, #E91E63, #FF5722); color: white; padding: 25px; border-radius: 15px; margin: 30px 0; text-align: center; box-shadow: 0 10px 25px rgba(0,0,0,0.1); animation: pulse 2s infinite;">
            <h3 style="margin: 0 0 15px 0; font-size: 24px;">🔥 מבצע מיוחד - מרץ 2025!</h3>
            <p style="margin: 15px 0; font-size: 18px; opacity: 0.9;">הזמינו ייעוץ חינם לפתרונות אנרגיה ירוקה וקבלו הנחה של 15% על כל המערכות</p>
            <a href="' . $calculator_url . '" style="display: inline-block; background: #4CAF50; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 18px; margin-top: 15px;">
                תופסים את המבצע! ⏰
            </a>
            <p style="margin: 15px 0 0 0; font-size: 14px; opacity: 0.8;">⏳ המבצע בתוקף עד סוף החודש | 🎁 ייעוץ חינם בביתכם</p>
        </div>',
        
        'simple' => '
        <div style="border: 2px solid #4CAF50; padding: 20px; border-radius: 10px; margin: 25px 0; text-align: center; background: #f8fff8;">
            <p style="margin: 0 0 15px 0; font-size: 18px; color: #333;">
                <strong>🤔 יש לכם שאלות נוספות על חיסכון באנרגיה?</strong>
            </p>
            <p style="margin: 0 0 15px 0; color: #666;">
                קבלו ייעוץ מקצועי חינם ופרטי התאמה ישירות לבית שלכם
            </p>
            <a href="' . $calculator_url . '" style="display: inline-block; background: #4CAF50; color: white; padding: 12px 25px; text-decoration: none; border-radius: 6px; font-weight: bold;">
                ייעוץ חינם עכשיו
            </a>
        </div>',
        
        'default' => '
        <div style="background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 25px; border-radius: 15px; margin: 30px 0; text-align: center; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
            <h3 style="margin: 0 0 15px 0; font-size: 24px;">🔋 רוצים לדעת כמה תוכלו לחסוך?</h3>
            <p style="margin: 15px 0; font-size: 18px; opacity: 0.9;">בדקו עכשיו בחינם במחשבון האנרגיה החכם שלנו וקבלו הצעות מחיר מותאמות אישית מהספקים המובילים</p>
            <a href="' . $calculator_url . '" style="display: inline-block; background: #4CAF50; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 18px; margin-top: 15px;">
                מחשבון חיסכון מיידי ⚡
            </a>
            <p style="margin: 15px 0 0 0; font-size: 14px; opacity: 0.8;">✅ חינמי לחלוטין | ✅ תוצאות מיידיות | ✅ ספקים מאומתים</p>
        </div>'
    );
    
    return isset($cta_types[$type]) ? $cta_types[$type] : $cta_types['default'];
}

// Shortcode לשימוש ידני
add_shortcode('energi_cta', 'energi_cta_shortcode');

function energi_cta_shortcode($atts) {
    $atts = shortcode_atts(array(
        'type' => 'default'
    ), $atts);
    
    return energi_manual_cta($atts['type']);
}

// הוספת מטא בוקס לעריכת פוסטים
add_action('add_meta_boxes', 'energi_cta_meta_box');

function energi_cta_meta_box() {
    add_meta_box(
        'energi_cta_settings',
        'הגדרות CTA - Energi',
        'energi_cta_meta_box_callback',
        'post'
    );
}

function energi_cta_meta_box_callback($post) {
    wp_nonce_field('energi_cta_nonce', 'energi_cta_nonce');
    
    $disable_cta = get_post_meta($post->ID, '_energi_disable_cta', true);
    $cta_type = get_post_meta($post->ID, '_energi_cta_type', true);
    
    echo '<table class="form-table">';
    echo '<tr>';
    echo '<th><label for="energi_disable_cta">השבת CTA אוטומטי</label></th>';
    echo '<td><input type="checkbox" id="energi_disable_cta" name="energi_disable_cta" value="1" ' . checked($disable_cta, 1, false) . ' /></td>';
    echo '</tr>';
    echo '<tr>';
    echo '<th><label for="energi_cta_type">סוג CTA</label></th>';
    echo '<td>';
    echo '<select id="energi_cta_type" name="energi_cta_type">';
    echo '<option value="default" ' . selected($cta_type, 'default', false) . '>רגיל</option>';
    echo '<option value="urgent" ' . selected($cta_type, 'urgent', false) . '>דחוף/מבצע</option>';
    echo '<option value="simple" ' . selected($cta_type, 'simple', false) . '>פשוט</option>';
    echo '</select>';
    echo '</td>';
    echo '</tr>';
    echo '</table>';
    
    echo '<p><strong>שימוש ב-Shortcode:</strong></p>';
    echo '<p><code>[energi_cta type="default"]</code> - CTA רגיל</p>';
    echo '<p><code>[energi_cta type="urgent"]</code> - CTA דחוף</p>';
    echo '<p><code>[energi_cta type="simple"]</code> - CTA פשוט</p>';
}

// שמירת הגדרות המטא בוקס
add_action('save_post', 'energi_save_cta_meta');

function energi_save_cta_meta($post_id) {
    if (!isset($_POST['energi_cta_nonce']) || !wp_verify_nonce($_POST['energi_cta_nonce'], 'energi_cta_nonce')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    $disable_cta = isset($_POST['energi_disable_cta']) ? 1 : 0;
    update_post_meta($post_id, '_energi_disable_cta', $disable_cta);
    
    if (isset($_POST['energi_cta_type'])) {
        update_post_meta($post_id, '_energi_cta_type', sanitize_text_field($_POST['energi_cta_type']));
    }
}

// עדכון הפונקציה הראשית כדי לכבד הגדרות הפוסט
function energi_add_cta_to_posts_updated($content) {
    if (!is_single() || is_page()) {
        return $content;
    }
    
    if (get_post_type() !== 'post') {
        return $content;
    }
    
    // בדיקה אם CTA מושבת לפוסט זה
    $disable_cta = get_post_meta(get_the_ID(), '_energi_disable_cta', true);
    if ($disable_cta) {
        return $content;
    }
    
    // קבלת סוג ה-CTA מהגדרות הפוסט
    $cta_type = get_post_meta(get_the_ID(), '_energi_cta_type', true);
    if (!$cta_type) {
        $cta_type = 'default';
    }
    
    $cta_html = energi_manual_cta($cta_type);
    
    return $content . $cta_html;
}

// החלפת הפונקציה הישנה בחדשה
remove_filter('the_content', 'energi_add_cta_to_posts');
remove_filter('the_content', 'energi_add_dynamic_cta_to_posts');
add_filter('the_content', 'energi_add_cta_to_posts_updated');

// CSS עבור אנימציות
add_action('wp_head', 'energi_cta_css');

function energi_cta_css() {
    echo '<style>
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }
        
        .energi-cta a:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(76, 175, 80, 0.3);
        }
    </style>';
}
?>