<?php
/**
 * Energi Leads Management System
 * 
 * @package Energi
 */

class Energi_Leads {
    
    private $table_name;
    
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'energi_leads';
        
        add_action('rest_api_init', [$this, 'register_api_routes']);
        add_action('wp_ajax_energi_lead', [$this, 'handle_ajax_lead']);
        add_action('wp_ajax_nopriv_energi_lead', [$this, 'handle_ajax_lead']);
        
        register_activation_hook(__FILE__, [$this, 'create_tables']);
    }
    
    /**
     * Create Database Tables
     */
    public function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            full_name varchar(100) NOT NULL,
            phone varchar(20) NOT NULL,
            email varchar(100) DEFAULT '',
            data longtext DEFAULT '',
            status varchar(20) DEFAULT 'new',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY status_idx (status),
            KEY created_idx (created_at)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Register REST API Routes
     */
    public function register_api_routes() {
        register_rest_route('energi/v1', '/lead', [
            'methods' => 'POST',
            'callback' => [$this, 'api_create_lead'],
            'permission_callback' => '__return_true',
            'args' => $this->get_api_args()
        ]);
        
        register_rest_route('energi/v1', '/leads', [
            'methods' => 'GET',
            'callback' => [$this, 'api_get_leads'],
            'permission_callback' => [$this, 'check_admin_permission']
        ]);
    }
    
    /**
     * API Arguments Validation
     */
    private function get_api_args() {
        return [
            'fullName' => [
                'required' => true,
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'validate_callback' => [$this, 'validate_name']
            ],
            'phone' => [
                'required' => true,
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'validate_callback' => [$this, 'validate_phone']
            ],
            'email' => [
                'required' => false,
                'type' => 'string',
                'sanitize_callback' => 'sanitize_email'
            ]
        ];
    }
    
    /**
     * Create Lead via API
     */
    public function api_create_lead(WP_REST_Request $request) {
        $data = $request->get_json_params();
        
        // Rate limiting
        if (!$this->check_rate_limit()) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Too many requests'
            ], 429);
        }
        
        // Create lead
        $lead_id = $this->create_lead($data);
        
        if ($lead_id) {
            // Background processing
            wp_schedule_single_event(time(), 'energi_process_lead', [$lead_id]);
            
            return new WP_REST_Response([
                'success' => true,
                'lead_id' => $lead_id,
                'message' => 'Lead created successfully'
            ], 201);
        }
        
        return new WP_REST_Response([
            'success' => false,
            'message' => 'Failed to create lead'
        ], 500);
    }
    
    /**
     * Create Lead in Database
     */
    public function create_lead($data) {
        global $wpdb;
        
        $lead_data = [
            'full_name' => $data['fullName'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?? '',
            'data' => json_encode($data),
            'status' => 'new'
        ];
        
        $result = $wpdb->insert($this->table_name, $lead_data);
        
        return $result ? $wpdb->insert_id : false;
    }
    
    /**
     * Validate Name
     */
    public function validate_name($value) {
        return strlen($value) >= 2 && strlen($value) <= 100;
    }
    
    /**
     * Validate Phone
     */
    public function validate_phone($value) {
        return preg_match('/^[0-9\-\+\s\(\)]{8,20}$/', $value);
    }
    
    /**
     * Check Rate Limit
     */
    private function check_rate_limit() {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $cache_key = 'energi_rate_limit_' . md5($ip);
        
        $requests = get_transient($cache_key) ?: 0;
        
        if ($requests >= 5) { // 5 requests per minute
            return false;
        }
        
        set_transient($cache_key, $requests + 1, MINUTE_IN_SECONDS);
        return true;
    }
    
    /**
     * Check Admin Permission
     */
    public function check_admin_permission() {
        return current_user_can('manage_options');
    }
    
    /**
     * Get Leads Statistics
     */
    public function get_statistics() {
        global $wpdb;
        
        return [
            'total' => $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_name}"),
            'new' => $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_name} WHERE status = 'new'"),
            'today' => $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_name} WHERE DATE(created_at) = CURDATE()"),
            'conversion_rate' => $this->calculate_conversion_rate()
        ];
    }
    
    /**
     * Calculate Conversion Rate
     */
    private function calculate_conversion_rate() {
        global $wpdb;
        
        $total = $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_name}");
        $converted = $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_name} WHERE status = 'converted'");
        
        return $total > 0 ? round(($converted / $total) * 100, 1) : 0;
    }
}

// Background lead processing
add_action('energi_process_lead', function($lead_id) {
    $leads = new Energi_Leads();
    $notification = new Energi_Notification();
    
    $lead = $leads->get_lead($lead_id);
    if ($lead) {
        $notification->send_admin_notification($lead);
        $notification->send_customer_confirmation($lead);
    }
});