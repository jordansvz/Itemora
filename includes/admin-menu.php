<?php
/**
 * Admin Menu and Dashboard functionality
 *
 * @package Itemora
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add admin menu items
 */
function itemora_agregar_menu_admin() {
    add_menu_page(
        'Itemora',
        'Itemora',
        'manage_options',
        'itemora',
        'itemora_admin_page',
        'dashicons-store',
        30
    );
}
add_action('admin_menu', 'itemora_agregar_menu_admin');

/**
 * Main admin page with tabs
 */
function itemora_admin_page() {
    // Security check
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.', 'itemora'));
    }

    $tab_actual = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'inicio';
    ?>
    <div class="wrap itemora-admin">
        <h1><?php _e('Itemora', 'itemora'); ?></h1>
        
        <nav class="nav-tab-wrapper">
            <a href="?page=itemora&tab=inicio" class="nav-tab <?php echo $tab_actual == 'inicio' ? 'nav-tab-active' : ''; ?>"><?php _e('Inicio', 'itemora'); ?></a>
            <a href="?page=itemora&tab=sucursales" class="nav-tab <?php echo $tab_actual == 'sucursales' ? 'nav-tab-active' : ''; ?>"><?php _e('Sucursales', 'itemora'); ?></a>
            <a href="?page=itemora&tab=detalles" class="nav-tab <?php echo $tab_actual == 'detalles' ? 'nav-tab-active' : ''; ?>"><?php _e('Detalles', 'itemora'); ?></a>
            <a href="?page=itemora&tab=extras" class="nav-tab <?php echo $tab_actual == 'extras' ? 'nav-tab-active' : ''; ?>"><?php _e('Extras', 'itemora'); ?></a>
            <a href="?page=itemora&tab=importar" class="nav-tab <?php echo $tab_actual == 'importar' ? 'nav-tab-active' : ''; ?>"><?php _e('Importar', 'itemora'); ?></a>
        </nav>
        
        <div class="itemora-content">
            <?php
            switch($tab_actual) {
                case 'inicio':
                    itemora_tab_inicio();
                    break;
                case 'sucursales':
                    itemora_tab_sucursales();
                    break;
                case 'detalles':
                    itemora_tab_detalles();
                    break;
                case 'extras':
                    itemora_tab_extras();
                    break;
                case 'importar':
                    itemora_tab_importar();
                    break;
                default:
                    itemora_tab_inicio();
                    break;
            }
            ?>
        </div>
    </div>
    <?php
}

/**
 * Dashboard tab content
 */
function itemora_tab_inicio() {
    // Get counts with error handling
    $productos_count = is_object(wp_count_posts('itemora_producto')) ? wp_count_posts('itemora_producto')->publish : 0;
    
    $categorias_args = array('taxonomy' => 'itemora_categoria', 'hide_empty' => false);
    $categorias_count = is_wp_error(wp_count_terms($categorias_args)) ? 0 : wp_count_terms($categorias_args);
    
    $sucursales_args = array('taxonomy' => 'itemora_sucursal', 'hide_empty' => false);
    $sucursales_count = is_wp_error(wp_count_terms($sucursales_args)) ? 0 : wp_count_terms($sucursales_args);
    ?>
    <div class="itemora-dashboard">
        <div class="itemora-stats">
            <div class="itemora-stat-card">
                <h3><?php _e('Productos', 'itemora'); ?></h3>
                <div class="stat-number"><?php echo intval($productos_count); ?></div>
                <a href="<?php echo esc_url(admin_url('edit.php?post_type=itemora_producto')); ?>" class="button"><?php _e('Ver Productos', 'itemora'); ?></a>
            </div>
            
            <div class="itemora-stat-card">
                <h3><?php _e('Categorías', 'itemora'); ?></h3>
                <div class="stat-number"><?php echo intval($categorias_count); ?></div>
                <a href="<?php echo esc_url(admin_url('edit-tags.php?taxonomy=itemora_categoria&post_type=itemora_producto')); ?>" class="button"><?php _e('Ver Categorías', 'itemora'); ?></a>
            </div>
            
            <div class="itemora-stat-card">
                <h3><?php _e('Sucursales', 'itemora'); ?></h3>
                <div class="stat-number"><?php echo intval($sucursales_count); ?></div>
                <a href="<?php echo esc_url(admin_url('edit-tags.php?taxonomy=itemora_sucursal&post_type=itemora_producto')); ?>" class="button"><?php _e('Ver Sucursales', 'itemora'); ?></a>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Sucursales tab content
 */
function itemora_tab_sucursales() {
    // Security check for form submission
    if (isset($_POST['itemora_guardar_sucursales'])) {
        check_admin_referer('itemora_sucursales_nonce', 'itemora_sucursales_nonce');
        update_option('itemora_activar_sucursal', isset($_POST['activar_sucursal']) ? 'yes' : 'no');
        echo '<div class="notice notice-success is-dismissible"><p>' . __('Configuración guardada.', 'itemora') . '</p></div>';
    }
    
    $activar_sucursal = get_option('itemora_activar_sucursal', 'no');
    ?>
    <div class="itemora-card">
        <form method="post">
            <?php wp_nonce_field('itemora_sucursales_nonce', 'itemora_sucursales_nonce'); ?>
            <div class="itemora-field-row">
                <label>
                    <input type="checkbox" name="activar_sucursal" value="yes" <?php checked($activar_sucursal, 'yes'); ?>>
                    <?php _e('Activar gestión de sucursales', 'itemora'); ?>
                </label>
            </div>
            <div class="itemora-submit-row">
                <input type="submit" name="itemora_guardar_sucursales" class="button button-primary" value="<?php _e('Guardar Cambios', 'itemora'); ?>">
            </div>
        </form>
    </div>
    <?php
}

/**
 * Detalles tab content
 */
function itemora_tab_detalles() {
    // Security check for form submission
    if (isset($_POST['itemora_guardar_detalles'])) {
        check_admin_referer('itemora_detalles_nonce', 'itemora_detalles_nonce');
        update_option('itemora_activar_detalles', isset($_POST['activar_detalles']) ? 'yes' : 'no');
        update_option('itemora_label_detalles_01', sanitize_text_field($_POST['label_detalles_01']));
        update_option('itemora_label_detalles_02', sanitize_text_field($_POST['label_detalles_02']));
        update_option('itemora_label_detalles_03', sanitize_text_field($_POST['label_detalles_03']));
        echo '<div class="notice notice-success is-dismissible"><p>' . __('Configuración guardada.', 'itemora') . '</p></div>';
    }
    
    $activar_detalles = get_option('itemora_activar_detalles', 'no');
    $label_detalles_01 = get_option('itemora_label_detalles_01', 'Detalle 1');
    $label_detalles_02 = get_option('itemora_label_detalles_02', 'Detalle 2');
    $label_detalles_03 = get_option('itemora_label_detalles_03', 'Detalle 3');
    ?>
    <div class="itemora-card">
        <form method="post">
            <?php wp_nonce_field('itemora_detalles_nonce', 'itemora_detalles_nonce'); ?>
            <div class="itemora-field-row">
                <label>
                    <input type="checkbox" name="activar_detalles" value="yes" <?php checked($activar_detalles, 'yes'); ?>>
                    <?php _e('Activar campos de detalles', 'itemora'); ?>
                </label>
            </div>
            
            <div class="itemora-fields-section" id="detalles-fields" <?php echo $activar_detalles !== 'yes' ? 'style="display:none;"' : ''; ?>>
                <div class="itemora-field-row">
                    <label for="label_detalles_01"><?php _e('Etiqueta 1:', 'itemora'); ?></label>
                    <input type="text" id="label_detalles_01" name="label_detalles_01" value="<?php echo esc_attr($label_detalles_01); ?>">
                </div>
                <div class="itemora-field-row">
                    <label for="label_detalles_02"><?php _e('Etiqueta 2:', 'itemora'); ?></label>
                    <input type="text" id="label_detalles_02" name="label_detalles_02" value="<?php echo esc_attr($label_detalles_02); ?>">
                </div>
                <div class="itemora-field-row">
                    <label for="label_detalles_03"><?php _e('Etiqueta 3:', 'itemora'); ?></label>
                    <input type="text" id="label_detalles_03" name="label_detalles_03" value="<?php echo esc_attr($label_detalles_03); ?>">
                </div>
            </div>
            
            <div class="itemora-submit-row">
                <input type="submit" name="itemora_guardar_detalles" class="button button-primary" value="<?php _e('Guardar Cambios', 'itemora'); ?>">
            </div>
        </form>
    </div>
    <?php
}

/**
 * Extras tab content
 */
function itemora_tab_extras() {
    // Security check for form submission
    if (isset($_POST['itemora_guardar_extras'])) {
        check_admin_referer('itemora_extras_nonce', 'itemora_extras_nonce');
        update_option('itemora_activar_extras', isset($_POST['activar_extras']) ? 'yes' : 'no');
        update_option('itemora_label_extra_01', sanitize_text_field($_POST['label_extra_01']));
        update_option('itemora_label_extra_02', sanitize_text_field($_POST['label_extra_02']));
        update_option('itemora_label_extra_03', sanitize_text_field($_POST['label_extra_03']));
        echo '<div class="notice notice-success is-dismissible"><p>' . __('Configuración guardada.', 'itemora') . '</p></div>';
    }
    
    $activar_extras = get_option('itemora_activar_extras', 'no');
    $label_extra_01 = get_option('itemora_label_extra_01', 'Extra 1');
    $label_extra_02 = get_option('itemora_label_extra_02', 'Extra 2');
    $label_extra_03 = get_option('itemora_label_extra_03', 'Extra 3');
    ?>
    <div class="itemora-card">
        <form method="post">
            <?php wp_nonce_field('itemora_extras_nonce', 'itemora_extras_nonce'); ?>
            <div class="itemora-field-row">
                <label>
                    <input type="checkbox" name="activar_extras" value="yes" <?php checked($activar_extras, 'yes'); ?>>
                    <?php _e('Activar campos extras', 'itemora'); ?>
                </label>
            </div>
            
            <div class="itemora-fields-section" id="extras-fields" <?php echo $activar_extras !== 'yes' ? 'style="display:none;"' : ''; ?>>
                <div class="itemora-field-row">
                    <label for="label_extra_01"><?php _e('Etiqueta 1:', 'itemora'); ?></label>
                    <input type="text" id="label_extra_01" name="label_extra_01" value="<?php echo esc_attr($label_extra_01); ?>">
                </div>
                <div class="itemora-field-row">
                    <label for="label_extra_02"><?php _e('Etiqueta 2:', 'itemora'); ?></label>
                    <input type="text" id="label_extra_02" name="label_extra_02" value="<?php echo esc_attr($label_extra_02); ?>">
                </div>
                <div class="itemora-field-row">
                    <label for="label_extra_03"><?php _e('Etiqueta 3:', 'itemora'); ?></label>
                    <input type="text" id="label_extra_03" name="label_extra_03" value="<?php echo esc_attr($label_extra_03); ?>">
                </div>
            </div>
            
            <div class="itemora-submit-row">
                <input type="submit" name="itemora_guardar_extras" class="button button-primary" value="<?php _e('Guardar Cambios', 'itemora'); ?>">
            </div>
        </form>
    </div>
    <?php
}

/**
 * Import tab content
 */
function itemora_tab_importar() {
    // Security check for form submission
    if (isset($_POST['itemora_importar_csv'])) {
        check_admin_referer('itemora_importar_nonce', 'itemora_importar_nonce');
        
        if (!empty($_FILES['archivo_csv']['tmp_name'])) {
            $file = $_FILES['archivo_csv']['tmp_name'];
            $handle = fopen($file, 'r');
            $success_count = 0;
            $error_count = 0;
            $row = 1;

            if ($handle !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    // Skip header row
                    if ($row === 1) {
                        $row++;
                        continue;
                    }

                    // Validate data
                    if (count($data) < 3) {
                        $error_count++;
                        continue;
                    }

                    // Create product
                    $product_data = array(
                        'post_title'    => sanitize_text_field($data[0]),
                        'post_content'  => sanitize_textarea_field($data[1]),
                        'post_status'   => 'publish',
                        'post_type'     => 'itemora_producto'
                    );

                    $product_id = wp_insert_post($product_data);

                    if (!is_wp_error($product_id)) {
                        // Add price as meta
                        update_post_meta($product_id, '_itemora_precio', sanitize_text_field($data[2]));
                        
                        // Add category if exists
                        if (!empty($data[3])) {
                            wp_set_object_terms($product_id, sanitize_text_field($data[3]), 'itemora_categoria');
                        }
                        
                        // Add branch if exists
                        if (!empty($data[4])) {
                            wp_set_object_terms($product_id, sanitize_text_field($data[4]), 'itemora_sucursal');
                        }

                        // Add details if enabled
                        if (get_option('itemora_activar_detalles') === 'yes' && !empty($data[5])) {
                            update_post_meta($product_id, '_itemora_detalles', sanitize_text_field($data[5]));
                        }

                        // Add extras if enabled
                        if (get_option('itemora_activar_extras') === 'yes' && !empty($data[6])) {
                            update_post_meta($product_id, '_itemora_extras', sanitize_text_field($data[6]));
                        }
                        
                        $success_count++;
                    } else {
                        $error_count++;
                    }
                }
                fclose($handle);
                
                echo '<div class="notice notice-success is-dismissible"><p>' . 
                    sprintf(
                        __('Importación completada. %d productos importados correctamente. %d errores.', 'itemora'),
                        $success_count,
                        $error_count
                    ) . '</p></div>';
            }
        } else {
            echo '<div class="notice notice-error is-dismissible"><p>' . __('Por favor seleccione un archivo CSV.', 'itemora') . '</p></div>';
        }
    }
    ?>
    <div class="itemora-card">
        <h2><?php _e('Importar Productos', 'itemora'); ?></h2>
        <p><?php _e('Seleccione un archivo CSV con los productos a importar.', 'itemora'); ?></p>
        
        <div class="itemora-csv-format">
            <h3><?php _e('Formato del CSV', 'itemora'); ?></h3>
            <p><?php _e('El archivo CSV debe tener las siguientes columnas:', 'itemora'); ?></p>
            <ul>
                <li><?php _e('Nombre del producto (requerido)', 'itemora'); ?></li>
                <li><?php _e('Descripción (requerido)', 'itemora'); ?></li>
                <li><?php _e('Precio (requerido)', 'itemora'); ?></li>
                <li><?php _e('Categoría (opcional)', 'itemora'); ?></li>
                <li><?php _e('Sucursal (opcional)', 'itemora'); ?></li>
                <li><?php _e('Detalles (opcional)', 'itemora'); ?></li>
                <li><?php _e('Extras (opcional)', 'itemora'); ?></li>
            </ul>
            <p class="description"><?php _e('Nota: El archivo debe estar en formato CSV y usar comas como separador.', 'itemora'); ?></p>
        </div>
        
        <form method="post" enctype="multipart/form-data">
            <?php wp_nonce_field('itemora_importar_nonce', 'itemora_importar_nonce'); ?>
            <div class="itemora-field-row">
                <label><?php _e('Archivo CSV:', 'itemora'); ?></label>
                <input type="file" name="archivo_csv" accept=".csv" required>
            </div>
            
            <div class="itemora-submit-row">
                <input type="submit" name="itemora_importar_csv" class="button button-primary" value="<?php _e('Importar Productos', 'itemora'); ?>">
                <a href="<?php echo esc_url(ITEMORA_PLUGIN_URL . 'assets/templates/itemora-template.csv'); ?>" class="button" download>
                    <?php _e('Descargar Plantilla CSV', 'itemora'); ?>
                </a>
            </div>
        </form>
    </div>
    <?php
}