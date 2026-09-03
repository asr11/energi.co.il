<?php
/*
Plugin Name: Energi Leads Manager
Description: ניהול לידים פשוט עבור Energi.co.il
Version: 1.0
Author: Energi Team
*/

// Prevent direct access
if (!defined('ABSPATH')) exit;

// Activation hook
register_activation_hook(__FILE__, 'energi_leads_activate');

function energi_leads_activate() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'energi_leads';
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE $table_name (
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
}

// Add admin menu
add_action('admin_menu', 'energi_leads_menu');

function energi_leads_menu() {
    add_menu_page(
        'לידים - Energi',
        'לידים',
        'manage_options',
        'energi-leads',
        'energi_leads_page',
        'dashicons-phone',
        30
    );
    
    add_submenu_page(
        'energi-leads',
        'ייצא לידים',
        'ייצא לידים',
        'manage_options',
        'energi-leads-export',
        'energi_leads_export_page'
    );
}

// Main leads page
function energi_leads_page() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'energi_leads';
    
    // Handle status updates
    if (isset($_POST['update_status']) && isset($_POST['lead_id']) && isset($_POST['new_status'])) {
        $lead_id = intval($_POST['lead_id']);
        $new_status = sanitize_text_field($_POST['new_status']);
        
        $wpdb->update(
            $table_name,
            array('status' => $new_status),
            array('id' => $lead_id)
        );
        
        echo '<div class="notice notice-success"><p>הסטטוס עודכן בהצלחה!</p></div>';
    }
    
    // Handle delete
    if (isset($_POST['delete_lead']) && isset($_POST['lead_id'])) {
        $lead_id = intval($_POST['lead_id']);
        $wpdb->delete($table_name, array('id' => $lead_id));
        echo '<div class="notice notice-success"><p>הליד נמחק בהצלחה!</p></div>';
    }
    
    // Get filter
    $status_filter = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : 'all';
    $search = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
    
    // Build query
    $where_clause = "WHERE 1=1";
    if ($status_filter !== 'all') {
        $where_clause .= $wpdb->prepare(" AND status = %s", $status_filter);
    }
    if (!empty($search)) {
        $where_clause .= $wpdb->prepare(" AND (full_name LIKE %s OR phone LIKE %s OR email LIKE %s OR city LIKE %s)", 
            "%$search%", "%$search%", "%$search%", "%$search%");
    }
    
    $leads = $wpdb->get_results("SELECT * FROM $table_name $where_clause ORDER BY submission_date DESC");
    
    // Get stats
    $total_leads = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    $new_leads = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'new'");
    $contacted_leads = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'contacted'");
    $converted_leads = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'converted'");
    
    ?>
    <div class="wrap">
        <h1>ניהול לידים - Energi.co.il</h1>
        
        <!-- Stats Dashboard -->
        <div style="display: flex; gap: 20px; margin: 20px 0;">
            <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-align: center; min-width: 150px;">
                <h2 style="margin: 0; color: #0073aa; font-size: 2.5em;"><?php echo $total_leads; ?></h2>
                <p style="margin: 5px 0 0 0; font-weight: bold;">סה"כ לידים</p>
            </div>
            <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-align: center; min-width: 150px;">
                <h2 style="margin: 0; color: #d63638; font-size: 2.5em;"><?php echo $new_leads; ?></h2>
                <p style="margin: 5px 0 0 0; font-weight: bold;">חדשים</p>
            </div>
            <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-align: center; min-width: 150px;">
                <h2 style="margin: 0; color: #dba617; font-size: 2.5em;"><?php echo $contacted_leads; ?></h2>
                <p style="margin: 5px 0 0 0; font-weight: bold;">בטיפול</p>
            </div>
            <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-align: center; min-width: 150px;">
                <h2 style="margin: 0; color: #00a32a; font-size: 2.5em;"><?php echo $converted_leads; ?></h2>
                <p style="margin: 5px 0 0 0; font-weight: bold;">הומרו</p>
            </div>
        </div>
        
        <!-- Filters -->
        <div style="background: #fff; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <form method="GET" style="display: flex; gap: 15px; align-items: center;">
                <input type="hidden" name="page" value="energi-leads">
                
                <label>סטטוס:</label>
                <select name="status" onchange="this.form.submit()">
                    <option value="all" <?php selected($status_filter, 'all'); ?>>הכל</option>
                    <option value="new" <?php selected($status_filter, 'new'); ?>>חדש</option>
                    <option value="contacted" <?php selected($status_filter, 'contacted'); ?>>נוצר קשר</option>
                    <option value="converted" <?php selected($status_filter, 'converted'); ?>>הומר</option>
                    <option value="rejected" <?php selected($status_filter, 'rejected'); ?>>נדחה</option>
                </select>
                
                <label>חיפוש:</label>
                <input type="text" name="search" value="<?php echo esc_attr($search); ?>" placeholder="שם, טלפון, אימייל או עיר">
                
                <button type="submit" class="button">חפש</button>
                <a href="?page=energi-leads" class="button">נקה פילטרים</a>
            </form>
        </div>
        
        <!-- Leads Table -->
        <div style="background: #fff; border-radius: 8px; overflow: hidden;">
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>תאריך</th>
                        <th>שם</th>
                        <th>טלפון</th>
                        <th>אימייל</th>
                        <th>נכס</th>
                        <th>פתרונות</th>
                        <th>חיסכון צפוי</th>
                        <th>עיר</th>
                        <th>סטטוס</th>
                        <th>פעולות</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($leads)): ?>
                        <tr>
                            <td colspan="10" style="text-align: center; padding: 40px;">
                                <p><strong>אין לידים להצגה</strong></p>
                                <p>ברגע שמישהו ימלא את הטופס במחשבון, הלידים יופיעו כאן.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($leads as $lead): ?>
                            <tr>
                                <td><?php echo date('d/m/Y H:i', strtotime($lead->submission_date)); ?></td>
                                <td><strong><?php echo esc_html($lead->full_name); ?></strong></td>
                                <td>
                                    <a href="tel:<?php echo esc_attr($lead->phone); ?>" style="color: #0073aa; text-decoration: none;">
                                        <?php echo esc_html($lead->phone); ?>
                                    </a>
                                </td>
                                <td>
                                    <?php if ($lead->email): ?>
                                        <a href="mailto:<?php echo esc_attr($lead->email); ?>" style="color: #0073aa; text-decoration: none;">
                                            <?php echo esc_html($lead->email); ?>
                                        </a>
                                    <?php else: ?>
                                        <span style="color: #999;">לא צוין</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo esc_html($lead->property_type); ?></td>
                                <td>
                                    <?php 
                                    $solutions = json_decode($lead->solutions, true);
                                    if (is_array($solutions)) {
                                        echo esc_html(implode(', ', $solutions));
                                    }
                                    ?>
                                </td>
                                <td><strong style="color: #00a32a;"><?php echo esc_html($lead->estimated_savings); ?></strong></td>
                                <td><?php echo esc_html($lead->city); ?></td>
                                <td>
                                    <?php
                                    $status_colors = array(
                                        'new' => '#d63638',
                                        'contacted' => '#dba617',
                                        'converted' => '#00a32a',
                                        'rejected' => '#999'
                                    );
                                    $status_names = array(
                                        'new' => 'חדש',
                                        'contacted' => 'נוצר קשר',
                                        'converted' => 'הומר',
                                        'rejected' => 'נדחה'
                                    );
                                    $color = isset($status_colors[$lead->status]) ? $status_colors[$lead->status] : '#999';
                                    $name = isset($status_names[$lead->status]) ? $status_names[$lead->status] : $lead->status;
                                    ?>
                                    <span style="background: <?php echo $color; ?>; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px;">
                                        <?php echo $name; ?>
                                    </span>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 5px;">
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="lead_id" value="<?php echo $lead->id; ?>">
                                            <select name="new_status" onchange="this.form.submit()" style="font-size: 12px;">
                                                <option value="">שנה סטטוס</option>
                                                <option value="new">חדש</option>
                                                <option value="contacted">נוצר קשר</option>
                                                <option value="converted">הומר</option>
                                                <option value="rejected">נדחה</option>
                                            </select>
                                            <input type="hidden" name="update_status" value="1">
                                        </form>
                                        
                                        <button onclick="showLeadDetails(<?php echo htmlspecialchars(json_encode($lead)); ?>)" 
                                                class="button button-small">פרטים</button>
                                        
                                        <form method="POST" style="display: inline;" 
                                              onsubmit="return confirm('האם אתה בטוח שברצונך למחוק ליד זה?')">
                                            <input type="hidden" name="lead_id" value="<?php echo $lead->id; ?>">
                                            <input type="hidden" name="delete_lead" value="1">
                                            <button type="submit" class="button button-small" style="color: #d63638;">מחק</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Lead Details Modal -->
    <div id="leadModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 999999;">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: #fff; padding: 30px; border-radius: 8px; max-width: 500px; width: 90%;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="margin: 0;">פרטי הליד</h2>
                <button onclick="hideLeadDetails()" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
            </div>
            <div id="leadModalContent"></div>
        </div>
    </div>
    
    <script>
        function showLeadDetails(lead) {
            const solutions = JSON.parse(lead.solutions || '[]');
            const solutionsText = Array.isArray(solutions) ? solutions.join(', ') : '';
            
            const content = `
                <div style="line-height: 1.6;">
                    <p><strong>שם מלא:</strong> ${lead.full_name}</p>
                    <p><strong>טלפון:</strong> <a href="tel:${lead.phone}">${lead.phone}</a></p>
                    <p><strong>אימייל:</strong> ${lead.email ? '<a href="mailto:' + lead.email + '">' + lead.email + '</a>' : 'לא צוין'}</p>
                    <p><strong>סוג נכס:</strong> ${lead.property_type || 'לא צוין'}</p>
                    <p><strong>גודל נכס:</strong> ${lead.property_size || 'לא צוין'} מ"ר</p>
                    <p><strong>חשבון חודשי:</strong> ${lead.monthly_bill || 'לא צוין'}</p>
                    <p><strong>עיר:</strong> ${lead.city || 'לא צוין'}</p>
                    <p><strong>פתרונות מעניינים:</strong> ${solutionsText || 'לא צוין'}</p>
                    <p><strong>חיסכון צפוי:</strong> <span style="color: #00a32a; font-weight: bold;">${lead.estimated_savings || 'לא חושב'}</span></p>
                    <p><strong>זמן נוח לקשר:</strong> ${lead.contact_time || 'לא צוין'}</p>
                    <p><strong>הערות:</strong> ${lead.notes || 'אין הערות'}</p>
                    <p><strong>תאריך הגשה:</strong> ${new Date(lead.submission_date).toLocaleString('he-IL')}</p>
                    <p><strong>כתובת IP:</strong> ${lead.ip_address || 'לא זמין'}</p>
                </div>
            `;
            
            document.getElementById('leadModalContent').innerHTML = content;
            document.getElementById('leadModal').style.display = 'block';
        }
        
        function hideLeadDetails() {
            document.getElementById('leadModal').style.display = 'none';
        }
        
        // Close modal on outside click
        document.getElementById('leadModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideLeadDetails();
            }
        });
    </script>
    <?php
}

// Export page
function energi_leads_export_page() {
    global $wpdb;
    
    if (isset($_POST['export_leads'])) {
        $table_name = $wpdb->prefix . 'energi_leads';
        $leads = $wpdb->get_results("SELECT * FROM $table_name ORDER BY submission_date DESC", ARRAY_A);
        
        if (!empty($leads)) {
            // Set headers for CSV download
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=energi_leads_' . date('Y-m-d') . '.csv');
            
            // Create CSV content
            $output = fopen('php://output', 'w');
            
            // Add BOM for Hebrew support in Excel
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers
            fputcsv($output, array(
                'תאריך', 'שם מלא', 'טלפון', 'אימייל', 'סוג נכס', 'פתרונות', 
                'גודל נכס', 'חשבון חודשי', 'עיר', 'זמן קשר', 'הערות', 
                'חיסכון צפוי', 'סטטוס', 'IP'
            ));
            
            // Data rows
            foreach ($leads as $lead) {
                $solutions = json_decode($lead['solutions'], true);
                $solutions_text = is_array($solutions) ? implode(', ', $solutions) : '';
                
                fputcsv($output, array(
                    $lead['submission_date'],
                    $lead['full_name'],
                    $lead['phone'],
                    $lead['email'],
                    $lead['property_type'],
                    $solutions_text,
                    $lead['property_size'],
                    $lead['monthly_bill'],
                    $lead['city'],
                    $lead['contact_time'],
                    $lead['notes'],
                    $lead['estimated_savings'],
                    $lead['status'],
                    $lead['ip_address']
                ));
            }
            
            fclose($output);
            exit;
        }
    }
    
    ?>
    <div class="wrap">
        <h1>ייצא לידים</h1>
        
        <div style="background: #fff; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h2>ייצא לקובץ CSV</h2>
            <p>ייצא את כל הלידים לקובץ Excel/CSV לשימוש חיצוני או גיבוי.</p>
            
            <form method="POST">
                <p>
                    <button type="submit" name="export_leads" class="button button-primary">
                        ייצא את כל הלידים לקובץ CSV
                    </button>
                </p>
            </form>
        </div>
    </div>
    <?php
}

// Add dashboard widget
add_action('wp_dashboard_setup', 'energi_leads_dashboard_widget');

function energi_leads_dashboard_widget() {
    wp_add_dashboard_widget(
        'energi_leads_widget',
        'לידים - Energi',
        'energi_leads_dashboard_widget_content'
    );
}

function energi_leads_dashboard_widget_content() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'energi_leads';
    $total_leads = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    $new_leads = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'new'");
    $today_leads = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE DATE(submission_date) = CURDATE()");
    
    echo '<div style="display: flex; justify-content: space-between; text-align: center;">';
    echo '<div><h3 style="margin: 0; color: #0073aa;">' . $total_leads . '</h3><p>סה"כ לידים</p></div>';
    echo '<div><h3 style="margin: 0; color: #d63638;">' . $new_leads . '</h3><p>חדשים</p></div>';
    echo '<div><h3 style="margin: 0; color: #00a32a;">' . $today_leads . '</h3><p>היום</p></div>';
    echo '</div>';
    
    echo '<p style="text-align: center; margin-top: 15px;">';
    echo '<a href="' . admin_url('admin.php?page=energi-leads') . '" class="button button-primary">נהל לידים</a>';
    echo '</p>';
}
?>