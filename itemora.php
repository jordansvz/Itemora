<?php
/**
 * Plugin Name: Itemora
 * Description: Sistema avanzado para gestionar productos, categorías, sucursales y campos personalizados en WordPress.
 * Version: 1.0.3
 * Author: JordanSVz
 * Author URI: https://jordansvz.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: itemora
 */

// Evitar acceso directo al archivo
if (!defined('ABSPATH')) {
    exit;
}

// Definir constantes del plugin
define('ITEMORA_VERSION', '1.0.1'); // Actualizado para coincidir con la versión del plugin
define('ITEMORA_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ITEMORA_PLUGIN_URL', plugin_dir_url(__FILE__));

// Incluir archivos necesarios
require_once ITEMORA_PLUGIN_DIR . 'includes/cpt-itemora.php'; // Custom Post Type
require_once ITEMORA_PLUGIN_DIR . 'includes/taxonomias.php'; // Taxonomías
require_once ITEMORA_PLUGIN_DIR . 'includes/campos-personalizados.php'; // Campos personalizados
require_once ITEMORA_PLUGIN_DIR . 'includes/formularios.php'; // Formularios frontend
require_once ITEMORA_PLUGIN_DIR . 'includes/admin-menu.php'; // Menú de administración
require_once ITEMORA_PLUGIN_DIR . 'includes/helpers.php'; // Funciones auxiliares

// Enqueue scripts y estilos (versión unificada)
function itemora_enqueue_scripts() {
    // Estilos
    wp_enqueue_style('itemora-style', ITEMORA_PLUGIN_URL . 'assets/css/style.css', array(), ITEMORA_VERSION);

    // Scripts
    wp_enqueue_script('jquery');
    wp_enqueue_script('itemora-script', ITEMORA_PLUGIN_URL . 'assets/js/script.js', array('jquery'), ITEMORA_VERSION, true);

    // Localizar script para pasar variables PHP a JavaScript
    wp_localize_script('itemora-script', 'itemora_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('itemora_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'itemora_enqueue_scripts');

// Enqueue scripts y estilos para el admin
function itemora_enqueue_admin_scripts($hook) {
    // Solo cargar en páginas del plugin
    if (strpos($hook, 'itemora') === false) {
        return;
    }
    
    // Estilos admin
    wp_enqueue_style('itemora-admin-style', ITEMORA_PLUGIN_URL . 'assets/css/admin-style.css', array(), ITEMORA_VERSION);
    
    // Scripts admin
    wp_enqueue_script('itemora-admin-script', ITEMORA_PLUGIN_URL . 'assets/js/admin-script.js', array('jquery'), ITEMORA_VERSION, true);
    
    // Localizar script admin
    wp_localize_script('itemora-admin-script', 'itemora_admin', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('itemora_admin_nonce')
    ));
}
add_action('admin_enqueue_scripts', 'itemora_enqueue_admin_scripts');