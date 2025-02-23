<?php
/**
 * Plugin Name: Itemora
 * Description: Sistema avanzado para gestionar productos, categorías, sucursales y campos personalizados en WordPress.
 * Version: 1.0.0
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
define('ITEMORA_VERSION', '1.0.0');
define('ITEMORA_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ITEMORA_PLUGIN_URL', plugin_dir_url(__FILE__));

// Incluir archivos necesarios
require_once ITEMORA_PLUGIN_DIR . 'includes/cpt-itemora.php'; // Custom Post Type
require_once ITEMORA_PLUGIN_DIR . 'includes/taxonomias.php'; // Taxonomías
require_once ITEMORA_PLUGIN_DIR . 'includes/campos-personalizados.php'; // Campos personalizados
require_once ITEMORA_PLUGIN_DIR . 'includes/formularios.php'; // Formularios frontend
require_once ITEMORA_PLUGIN_DIR . 'includes/admin-menu.php'; // Menú de administración
require_once ITEMORA_PLUGIN_DIR . 'includes/helpers.php'; // Funciones auxiliares

// Enqueue estilos y scripts
function itemora_enqueue_assets() {
    wp_enqueue_style('itemora-style', ITEMORA_PLUGIN_URL . 'assets/css/style.css', array(), ITEMORA_VERSION);
    wp_enqueue_script('itemora-script', ITEMORA_PLUGIN_URL . 'assets/js/script.js', array('jquery'), ITEMORA_VERSION, true);
}
add_action('wp_enqueue_scripts', 'itemora_enqueue_assets');