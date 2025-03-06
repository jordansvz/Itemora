<?php
/**
 * Plugin Name: Itemora
 * Description: Sistema avanzado para gestionar productos, categorías, sucursales y campos personalizados en WordPress.
 * Version: 1.0.4
 * Author: JordanSVz
 * Author URI: https://jordansvz.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: itemora
 * Domain Path: /languages
 */

// Prevent direct access to this file
if (!defined('ABSPATH')) {
    exit;
}

// Plugin constants
define('ITEMORA_VERSION', '1.0.3');
define('ITEMORA_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ITEMORA_PLUGIN_URL', plugin_dir_url(__FILE__));
define('ITEMORA_BASENAME', plugin_basename(__FILE__));

// Load plugin textdomain
function itemora_load_textdomain() {
    load_plugin_textdomain('itemora', false, dirname(ITEMORA_BASENAME) . '/languages');
}
add_action('plugins_loaded', 'itemora_load_textdomain');

// Required files check and include
$required_files = array(
    'includes/cpt-itemora.php',
    'includes/taxonomias.php',
    'includes/campos-personalizados.php',
    'includes/formularios.php',
    'includes/admin-menu.php',
    'includes/helpers.php'
);

foreach ($required_files as $file) {
    $file_path = ITEMORA_PLUGIN_DIR . $file;
    if (!file_exists($file_path)) {
        wp_die(sprintf(
            __('Error: Required file %s is missing. Please reinstall Itemora plugin.', 'itemora'),
            '<code>' . esc_html($file) . '</code>'
        ));
    }
    require_once $file_path;
}

// Frontend assets
function itemora_enqueue_scripts() {
    if (!is_admin()) {
        wp_enqueue_style(
            'itemora-style', 
            ITEMORA_PLUGIN_URL . 'assets/css/style.css',
            array(),
            ITEMORA_VERSION
        );

        wp_enqueue_script(
            'itemora-script',
            ITEMORA_PLUGIN_URL . 'assets/js/script.js',
            array('jquery'),
            ITEMORA_VERSION,
            true
        );

        wp_localize_script('itemora-script', 'itemora_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('itemora_nonce'),
            'is_user_logged_in' => is_user_logged_in()
        ));
    }
}
add_action('wp_enqueue_scripts', 'itemora_enqueue_scripts');

// Admin assets
function itemora_enqueue_admin_scripts($hook) {
    // Only load on Itemora admin pages
    if (strpos($hook, 'itemora') !== false) {
        wp_enqueue_style(
            'itemora-admin-style',
            ITEMORA_PLUGIN_URL . 'assets/css/admin-style.css',
            array(),
            ITEMORA_VERSION
        );

        wp_enqueue_script(
            'itemora-admin-script',
            ITEMORA_PLUGIN_URL . 'assets/js/admin-script.js',
            array('jquery'),
            ITEMORA_VERSION,
            true
        );

        wp_localize_script('itemora-admin-script', 'itemora_admin', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('itemora_admin_nonce'),
            'messages' => array(
                'save_success' => __('Changes saved successfully.', 'itemora'),
                'save_error' => __('Error saving changes.', 'itemora'),
                'confirm_delete' => __('Are you sure you want to delete this item?', 'itemora')
            )
        ));
    }
}
add_action('admin_enqueue_scripts', 'itemora_enqueue_admin_scripts');

// Plugin activation
function itemora_activate() {
    // Create necessary directories
    $upload_dir = wp_upload_dir();
    $itemora_dir = $upload_dir['basedir'] . '/itemora';
    
    if (!file_exists($itemora_dir)) {
        wp_mkdir_p($itemora_dir);
    }

    // Create or update database tables if needed
    itemora_create_tables();
    
    // Clear permalinks
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'itemora_activate');

// Plugin deactivation
function itemora_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'itemora_deactivate');

// Create plugin tables
function itemora_create_tables() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    // Example table creation (if needed)
    $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}itemora_logs (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        user_id bigint(20) NOT NULL,
        action varchar(50) NOT NULL,
        object_type varchar(50) NOT NULL,
        object_id bigint(20) NOT NULL,
        details longtext,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Plugin initialization
function itemora_init() {
    // Register post types and taxonomies
    if (function_exists('itemora_register_cpt')) {
        itemora_register_cpt();
    }
    if (function_exists('itemora_register_taxonomies')) {
        itemora_register_taxonomies();
    }
}
add_action('init', 'itemora_init');