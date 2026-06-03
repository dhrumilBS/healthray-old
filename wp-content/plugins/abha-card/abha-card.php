<?php
/**
 * Plugin Name: ABHA Card
 * Description: Aadhaar Authentication and ABHA Card Integration for HealthRay
 * Version:     1.1.0
 * Author:      HealthRay
 */

if (!defined('ABSPATH')) {
    exit;
}

// ---------------------------------------------------------------------------
// Constants
// ---------------------------------------------------------------------------
define('ABHA_LIVE_API_PATH', 'https://node.healthray.com/api/');
define('ABHA_STAGE_API_PATH', 'https://node-stage.healthray.com/api/');
define('ABHA_LOCAL_API_PATH', 'http://192.168.0.162:4004/api/');
define('ABHA_API_PATH', ABHA_LIVE_API_PATH);

// ---------------------------------------------------------------------------
// Shortcode (kept minimal; extend via abhacardForm.php as needed)
// ---------------------------------------------------------------------------
add_shortcode('adharAuthForm', function () {
    ob_start();
    require_once __DIR__ . '/abhacardForm.php';
    return ob_get_clean();
});

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('abha-card', plugin_dir_url(__FILE__) . 'assets/css/abha-card.css');
    wp_enqueue_script('abha-card', plugin_dir_url(__FILE__) . 'assets/js/abha-card.js', array(), null, true);
    wp_localize_script('abha-card', 'ajax_obj', array('url' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('abha_nonce')));
});

// ---------------------------------------------------------------------------
// Bootstrap class
// ---------------------------------------------------------------------------
require_once __DIR__ . '/includes/class-abha-card.php';

new ABHA_Card();