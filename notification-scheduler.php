<?php
/**
 * Plugin Name: Notification Scheduler
 * Description: A multipurpose plugin that displays product notifications with customizable intervals and text variables.
 * Version: 1.0.0
 * Author: Joseph
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