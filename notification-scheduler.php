<?php
/**
 * Plugin Name: Notification Scheduler
 * Description: A multipurpose plugin that displays product notifications with customizable intervals and text variables.
 * Version: 1.0.1
 * Author: Jotweb Studio
 * Author URI: https://jotwebstudio.com
 * License: GPL v2 or later
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('NS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('NS_PLUGIN_PATH', plugin_dir_path(__FILE__));

// Include required files
require_once NS_PLUGIN_PATH . 'includes/class-notification-scheduler.php';

// Initialize the plugin
function ns_init() {
    new Notification_Scheduler();
}
add_action('plugins_loaded', 'ns_init');

// Activation hook
register_activation_hook(__FILE__, 'ns_activate');
function ns_activate() {
    $default_options = array(
        'interval' => 30,
        'text_template' => 'Someone from {city} just purchased {product}',
        'variables' => array(
            'city' => array(
                'type' => 'array',
                'values' => 'New York|Los Angeles|Chicago|Miami|Seattle'
            ),
            'product' => array(
                'type' => 'array',
                'values' => 'Premium Widget|Super Gadget|Amazing Tool|Best Product'
            )
        )
    );
    
    add_option('ns_settings', $default_options);
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'ns_deactivate');
function ns_deactivate() {
    // Clean up if needed
}

// Helper to get all WooCommerce products for JS
function ns_get_all_woocommerce_products_for_js() {
    if (!class_exists('WooCommerce')) return array();
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => 50,
        'post_status' => 'publish',
    );
    $products = get_posts($args);
    $result = array();
    foreach ($products as $p) {
        $product = wc_get_product($p->ID);
        if ($product) {
            $result[] = array(
                'product' => $product->get_name(),
                'price' => $product->get_price(),
                'image' => get_the_post_thumbnail_url($product->get_id(), 'thumbnail'),
            );
        }
    }
    return $result;
}

// Patch: Add WooCommerce products to JS if needed
add_action('wp_enqueue_scripts', function() {
    $settings = get_option('ns_settings', array());
	// if(empty($_GET['notification'])) return;
    if (isset($settings['template']) && $settings['template'] === 'woocommerce') {
        $settings['woocommerce_products'] = ns_get_all_woocommerce_products_for_js();
        wp_localize_script('ns-popup-script', 'nsSettings', array(
            'settings' => $settings
        ));
    }
}, 20);