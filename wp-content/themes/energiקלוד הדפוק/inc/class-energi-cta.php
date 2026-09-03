<?php
/**
 * Energi Smart CTA System
 * 
 * @package Energi
 */

class Energi_CTA {
    
    private $cta_types;
    
    public function __construct() {
        $this->init_cta_types();
        add_filter('the_content', [$this, 'add_smart_cta'], 10);
        add_shortcode('energi_cta', [$this, 'cta_shortcode']);
    }
    
    /**
     * Initialize CTA Types
     */
    private function init_cta_types() {
        $this->cta_types = [
            'solar' => [
                'keywords' => ['סולר', 'שמש', 'פוטו'],
                'title' => '☀️ מערכת סולארית מתאימה לכם?',
                'button' => 'חשבו חיסכון סולארי',
                'color' => '#FF9800'
            ],
            'smart_home' => [
                'keywords' => ['חכם', 'אוטומציה', 'IoT'],
                'title' => '🏠 בית חכם שחוסך כסף',
                'button' => 'מחשבון בית חכם',
                'color' => '#2196F3'
            ],
            'default' => [
                'title' => '⚡ מחשבון חיסכון אנרגיה',
                'button' => 'התחל עכשיו',
                'color' => '#4CAF50'
            ]
        ];
    }
    
    /**
     * Add Smart CTA to Content
     */
    public function add_smart_cta($content) {
        if (!is_single() || is_admin()) {
            return $content;
        }
        
        // Check if CTA is disabled for this post
        if (get_post_meta(get_the_ID(), '_disable_energi_cta', true)) {
            return $content;
        }
        
        $cta_type = $this->detect_content_type($content);
        $cta_html = $this->render_cta($cta_type);
        
        return $content . $cta_html;
    }
    
    /**
     * Detect Content Type using AI-like analysis
     */
    private function detect_content_type($content) {
        $content_lower = mb_strtolower(strip_tags($content));
        $scores = [];
        
        foreach ($this->cta_types as $type => $config) {
            if ($type === 'default') continue;
            
            $scores[$type] = 0;
            foreach ($config['keywords'] as $keyword) {
                $scores[$type] += substr_count($content_lower, $keyword);
            }
        }
        
        $max_score = max($scores);
        return $max_score > 0 ? array_search($max_score, $scores) : 'default';
    }
    
    /**
     * Render CTA HTML
     */
    private function render_cta($type) {
        $config = $this->cta_types[$type] ?? $this->cta_types['default'];
        
        ob_start();
        include ENERGI_PATH . '/templates/parts/cta-' . $type . '.php';
        return ob_get_clean();
    }
    
    /**
     * CTA Shortcode
     */
    public function cta_shortcode($atts) {
        $atts = shortcode_atts([
            'type' => 'default',
            'title' => '',
            'button' => ''
        ], $atts);
        
        return $this->render_cta($atts['type']);
    }
}