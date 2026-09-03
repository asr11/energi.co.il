<?php
/**
 * Energi Admin Interface
 * 
 * @package Energi
 */

class Energi_Admin {
    
    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_pages']);
        add_action('admin_enqueue_scripts', [$this, 'admin_scripts']);
        add_action('wp_ajax_energi_admin_action', [$this, 'handle_admin_ajax']);
    }
    
    /**
     * Add Admin Pages
     */
    public function add_admin_pages() {
        add_menu_page(
            'Energi Dashboard',
            'Energi 🔋',
            'manage_options',
            'energi-dashboard',
            [$this, 'dashboard_page'],
            'dashicons-chart-line',
            25
        );
        
        add_submenu_page(
            'energi-dashboard',
            'Leads Management',
            'לידים 📊',
            'manage_options',
            'energi-leads',
            [$this, 'leads_page']
        );
    }
    
    /**
     * Dashboard Page
     */
    public function dashboard_page() {
        $leads = new Energi_Leads();
        $stats = $leads->get_statistics();
        
        include ENERGI_PATH . '/templates/admin/dashboard.php';
    }
    
    /**
     * Leads Management Page
     */
    public function leads_page() {
        include ENERGI_PATH . '/templates/admin/leads.php';
    }
    
    /**
     * Load Admin Scripts
     */
    public function admin_scripts($hook) {
        if (strpos($hook, 'energi') === false) {
            return;
        }
        
        wp_enqueue_script(
            'energi-admin',
            ENERGI_URL . '/assets/js/admin.js',
            ['jquery'],
            ENERGI_VERSION,
            true
        );
        
        wp_localize_script('energi-admin', 'energiAdmin', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('energi_admin_nonce')
        ]);
    }
    
    /**
     * Handle Admin AJAX
     */
    public function handle_admin_ajax() {
        check_ajax_referer('energi_admin_nonce', 'nonce');
        
        $action = sanitize_text_field($_POST['admin_action']);
        
        switch ($action) {
            case 'update_lead_status':
                $this->update_lead_status();
                break;
            case 'bulk_action':
                $this->handle_bulk_action();
                break;
            default:
                wp_die('Invalid action');
        }
    }
    
    /**
     * Update Lead Status
     */
    private function update_lead_status() {
        $lead_id = intval($_POST['lead_id']);
        $status = sanitize_text_field($_POST['status']);
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'energi_leads';
        
        $result = $wpdb->update(
            $table_name,
            ['status' => $status, 'updated_at' => current_time('mysql')],
            ['id' => $lead_id],
            ['%s', '%s'],
            ['%d']
        );
        
        wp_send_json_success([
            'message' => 'Status updated successfully',
            'lead_id' => $lead_id,
            'new_status' => $status
        ]);
    }
}