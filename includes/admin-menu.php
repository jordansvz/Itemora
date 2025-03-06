<?php
// Agregar menú de configuración en el dashboard
function itemora_agregar_menu_admin() {
    // Página principal
    add_menu_page(
        'Itemora - Panel de Control', // Título de la página
        'Itemora',                    // Nombre del menú
        'manage_options',             // Capacidad requerida
        'itemora',                    // Slug
        'itemora_admin_home_page',    // Función callback
        'dashicons-store',            // Icono
        6                             // Posición en el menú
    );
    
    // Submenús
    add_submenu_page(
        'itemora',
        __('Inicio', 'itemora'),
        __('Inicio', 'itemora'),
        'manage_options',
        'itemora',
        'itemora_admin_home_page'
    );
    
    add_submenu_page(
        'itemora',
        __('Sucursales', 'itemora'),
        __('Sucursales', 'itemora'),
        'manage_options',
        'itemora-sucursales',
        'itemora_admin_sucursales_page'
    );
    
    add_submenu_page(
        'itemora',
        __('Info Complementaria', 'itemora'),
        __('Info Complementaria', 'itemora'),
        'manage_options',
        'itemora-info-complementaria',
        'itemora_admin_info_complementaria_page'
    );
    
    add_submenu_page(
        'itemora',
        __('Integraciones', 'itemora'),
        __('Integraciones', 'itemora'),
        'manage_options',
        'itemora-integraciones',
        'itemora_admin_integraciones_page'
    );
}
add_action('admin_menu', 'itemora_agregar_menu_admin');

// Página de inicio
function itemora_admin_home_page() {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    // Obtener conteos
    $productos_count = wp_count_posts('itemora_producto')->publish;
    $categorias_count = wp_count_terms('itemora_categoria');
    $sucursales_count = wp_count_terms('itemora_sucursal');
    
    ?>
    <div class="wrap itemora-admin">
        <h1><?php _e('Itemora - Panel de Control', 'itemora'); ?></h1>
        
        <div class="itemora-dashboard">
            <div class="itemora-card">
                <h2><?php _e('Información del Plugin', 'itemora'); ?></h2>
                <p><?php _e('Versión:', 'itemora'); ?> <?php echo ITEMORA_VERSION; ?></p>
                <p><?php _e('Sistema avanzado para gestionar productos, categorías, sucursales y campos personalizados en WordPress.', 'itemora'); ?></p>
            </div>
            
            <div class="itemora-stats">
                <div class="itemora-stat-card">
                    <h3><?php _e('Productos', 'itemora'); ?></h3>
                    <div class="stat-number"><?php echo $productos_count; ?></div>
                    <a href="<?php echo admin_url('edit.php?post_type=itemora_producto'); ?>" class="button"><?php _e('Ver Productos', 'itemora'); ?></a>
                </div>
                
                <div class="itemora-stat-card">
                    <h3><?php _e('Categorías', 'itemora'); ?></h3>
                    <div class="stat-number"><?php echo $categorias_count; ?></div>
                    <a href="<?php echo admin_url('edit-tags.php?taxonomy=itemora_categoria&post_type=itemora_producto'); ?>" class="button"><?php _e('Ver Categorías', 'itemora'); ?></a>
                </div>
                
                <div class="itemora-stat-card">
                    <h3><?php _e('Sucursales', 'itemora'); ?></h3>
                    <div class="stat-number"><?php echo $sucursales_count; ?></div>
                    <a href="<?php echo admin_url('admin.php?page=itemora-sucursales'); ?>" class="button"><?php _e('Administrar Sucursales', 'itemora'); ?></a>
                </div>
            </div>
        </div>
    </div>
    <?php
}

// Página de sucursales
function itemora_admin_sucursales_page() {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    // Procesar cambios si se envió el formulario
    if (isset($_POST['itemora_sucursal_action']) && $_POST['itemora_sucursal_action'] == 'update_estado') {
        $term_id = intval($_POST['itemora_sucursal_id']);
        $estado = sanitize_text_field($_POST['itemora_sucursal_estado']);
        
        update_term_meta($term_id, 'itemora_sucursal_estado', $estado);
        echo '<div class="updated"><p>Estado de sucursal actualizado correctamente.</p></div>';
    }
    
    // Obtener sucursales
    $sucursales = get_terms([
        'taxonomy' => 'itemora_sucursal',
        'hide_empty' => false,
    ]);
    
    // Obtener opción global
    $activar_sucursal = get_option('itemora_activar_sucursal', 'no');
    
    ?>
    <div class="wrap itemora-admin">
        <h1><?php _e('Administración de Sucursales', 'itemora'); ?></h1>
        
        <div class="itemora-global-setting">
            <form method="post">
                <label>
                    <input type="checkbox" name="activar_sucursal" value="yes" <?php checked($activar_sucursal, 'yes'); ?>>
                    <?php _e('Activar funcionalidad de sucursales', 'itemora'); ?>
                </label>
                <input type="hidden" name="itemora_guardar_opciones" value="1">
                <button type="submit" class="button"><?php _e('Guardar', 'itemora'); ?></button>
            </form>
        </div>
        
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('Nombre', 'itemora'); ?></th>
                    <th><?php _e('Estado', 'itemora'); ?></th>
                    <th><?php _e('Acciones', 'itemora'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if (!empty($sucursales)):
                    foreach ($sucursales as $sucursal): 
                        $estado = get_term_meta($sucursal->term_id, 'itemora_sucursal_estado', true);
                        $estado = $estado ? $estado : 'activo'; // Por defecto activo
                ?>
                    <tr>
                        <td><?php echo $sucursal->name; ?></td>
                        <td>
                            <form method="post" class="itemora-estado-form">
                                <select name="itemora_sucursal_estado" class="itemora-estado-select">
                                    <option value="activo" <?php selected($estado, 'activo'); ?>><?php _e('Activo', 'itemora'); ?></option>
                                    <option value="inactivo" <?php selected($estado, 'inactivo'); ?>><?php _e('Inactivo', 'itemora'); ?></option>
                                </select>
                                <input type="hidden" name="itemora_sucursal_id" value="<?php echo $sucursal->term_id; ?>">
                                <input type="hidden" name="itemora_sucursal_action" value="update_estado">
                                <button type="submit" class="button button-small"><?php _e('Guardar', 'itemora'); ?></button>
                            </form>
                        </td>
                        <td>
                            <a href="<?php echo admin_url('term.php?taxonomy=itemora_sucursal&tag_ID=' . $sucursal->term_id); ?>" class="button button-small"><?php _e('Editar', 'itemora'); ?></a>
                        </td>
                    </tr>
                <?php 
                    endforeach;
                else:
                ?>
                    <tr>
                        <td colspan="3"><?php _e('No hay sucursales definidas.', 'itemora'); ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}

// Página de información complementaria
function itemora_admin_info_complementaria_page() {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    // Procesar el formulario de configuración
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['itemora_guardar_opciones'])) {
        // Guardar opciones generales
        update_option('itemora_activar_detalles', isset($_POST['activar_detalles']) ? 'yes' : 'no');
        update_option('itemora_activar_extras', isset($_POST['activar_extras']) ? 'yes' : 'no');

        // Guardar labels personalizados
        update_option('itemora_label_detalles_01', sanitize_text_field($_POST['label_detalles_01']));
        update_option('itemora_label_detalles_02', sanitize_text_field($_POST['label_detalles_02']));
        update_option('itemora_label_detalles_03', sanitize_text_field($_POST['label_detalles_03']));
        update_option('itemora_label_extra_01', sanitize_text_field($_POST['label_extra_01']));
        update_option('itemora_label_extra_02', sanitize_text_field($_POST['label_extra_02']));
        update_option('itemora_label_extra_03', sanitize_text_field($_POST['label_extra_03']));

        echo '<div class="updated"><p>Opciones guardadas correctamente.</p></div>';
    }

    // Obtener opciones actuales
    $activar_detalles = get_option('itemora_activar_detalles', 'no');
    $activar_extras = get_option('itemora_activar_extras', 'no');

    // Obtener labels actuales
    $label_detalles_01 = get_option('itemora_label_detalles_01', 'Detalle 1');
    $label_detalles_02 = get_option('itemora_label_detalles_02', 'Detalle 2');
    $label_detalles_03 = get_option('itemora_label_detalles_03', 'Detalle 3');
    $label_extra_01 = get_option('itemora_label_extra_01', 'Extra 1');
    $label_extra_02 = get_option('itemora_label_extra_02', 'Extra 2');
    $label_extra_03 = get_option('itemora_label_extra_03', 'Extra 3');
    
    ?>
    <div class="wrap itemora-admin">
        <h1><?php _e('Información Complementaria', 'itemora'); ?></h1>
        
        <form method="post">
            <div class="itemora-card">
                <h2><?php _e('Configuración de Detalles', 'itemora'); ?></h2>
                <div class="itemora-toggle-section">
                    <label class="itemora-toggle">
                        <input type="checkbox" name="activar_detalles" value="yes" <?php checked($activar_detalles, 'yes'); ?>>
                        <span class="itemora-toggle-label"><?php _e('Activar Detalles', 'itemora'); ?></span>
                    </label>
                </div>
                
                <div class="itemora-fields-section">
                    <h3><?php _e('Etiquetas de Detalles', 'itemora'); ?></h3>
                    <div class="itemora-field-row">
                        <label for="label_detalles_01"><?php _e('Detalle 1:', 'itemora'); ?></label>
                        <input type="text" name="label_detalles_01" id="label_detalles_01" value="<?php echo esc_attr($label_detalles_01); ?>">
                    </div>
                    <div class="itemora-field-row">
                        <label for="label_detalles_02"><?php _e('Detalle 2:', 'itemora'); ?></label>
                        <input type="text" name="label_detalles_02" id="label_detalles_02" value="<?php echo esc_attr($label_detalles_02); ?>">
                    </div>
                    <div class="itemora-field-row">
                        <label for="label_detalles_03"><?php _e('Detalle 3:', 'itemora'); ?></label>
                        <input type="text" name="label_detalles_03" id="label_detalles_03" value="<?php echo esc_attr($label_detalles_03); ?>">
                    </div>
                </div>
            </div>
            
            <div class="itemora-card">
                <h2><?php _e('Configuración de Extras', 'itemora'); ?></h2>
                <div class="itemora-toggle-section">
                    <label class="itemora-toggle">
                        <input type="checkbox" name="activar_extras" value="yes" <?php checked($activar_extras, 'yes'); ?>>
                        <span class="itemora-toggle-label"><?php _e('Activar Extras', 'itemora'); ?></span>
                    </label>
                </div>
                
                <div class="itemora-fields-section">
                    <h3><?php _e('Etiquetas de Extras', 'itemora'); ?></h3>
                    <div class="itemora-field-row">
                        <label for="label_extra_01"><?php _e('Extra 1:', 'itemora'); ?></label>
                        <input type="text" name="label_extra_01" id="label_extra_01" value="<?php echo esc_attr($label_extra_01); ?>">
                    </div>
                    <div class="itemora-field-row">
                        <label for="label_extra_02"><?php _e('Extra 2:', 'itemora'); ?></label>
                        <input type="text" name="label_extra_02" id="label_extra_02" value="<?php echo esc_attr($label_extra_02); ?>">
                    </div>
                    <div class="itemora-field-row">
                        <label for="label_extra_03"><?php _e('Extra 3:', 'itemora'); ?></label>
                        <input type="text" name="label_extra_03" id="label_extra_03" value="<?php echo esc_attr($label_extra_03); ?>">
</div>
                </div>
            </div>
                    
            
            
            <input type="hidden" name="itemora_guardar_opciones" value="1">
            <p class="submit">
                <input type="submit" name="submit" class="button button-primary" value="<?php _e('Guardar Cambios', 'itemora'); ?>">
            </p>
        </form>
    </div>
    <?php
}

// Página de integraciones
function itemora_admin_integraciones_page() {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    // Procesar importación de CSV si se envió el formulario
    if (isset($_POST['itemora_integration_action']) && $_POST['itemora_integration_action'] == 'import_csv') {
        if (!empty($_FILES['itemora_csv_file']['tmp_name'])) {
            // Aquí iría el código para procesar el archivo CSV
            echo '<div class="updated"><p>Archivo CSV recibido. La importación se procesará en breve.</p></div>';
        } else {
            echo '<div class="error"><p>Por favor seleccione un archivo CSV para importar.</p></div>';
        }
    }
    
    ?>
    <div class="wrap itemora-admin">
        <h1><?php _e('Integraciones', 'itemora'); ?></h1>
        
        <div class="itemora-integraciones">
            <div class="itemora-card">
                <h2><?php _e('Importar desde CSV', 'itemora'); ?></h2>
                <p><?php _e('Sube un archivo CSV para importar productos masivamente.', 'itemora'); ?></p>
                <form method="post" enctype="multipart/form-data">
                    <input type="file" name="itemora_csv_file" accept=".csv">
                    <button type="submit" class="button"><?php _e('Importar', 'itemora'); ?></button>
                    <input type="hidden" name="itemora_integration_action" value="import_csv">
                </form>
                
                <div class="itemora-csv-template">
                    <h3><?php _e('Plantilla CSV', 'itemora'); ?></h3>
                    <p><?php _e('Descarga nuestra plantilla para asegurarte de que tu CSV tiene el formato correcto.', 'itemora'); ?></p>
                    <a href="<?php echo ITEMORA_PLUGIN_URL . 'assets/templates/itemora-template.csv'; ?>" class="button" download><?php _e('Descargar Plantilla', 'itemora'); ?></a>
                </div>
            </div>
            
            <div class="itemora-card">
                <h2><?php _e('Integraciones API', 'itemora'); ?></h2>
                <p><?php _e('Próximamente: Conecta con APIs externas para sincronizar datos.', 'itemora'); ?></p>
                <div class="itemora-coming-soon">
                    <span><?php _e('Próximamente', 'itemora'); ?></span>
                </div>
            </div>
        </div>
    </div>
    <?php
}

// Registrar estilos para el admin
function itemora_admin_styles() {
    $screen = get_current_screen();
    
    // Solo cargar en páginas de Itemora
    if (strpos($screen->id, 'itemora') !== false) {
        wp_enqueue_style('itemora-admin-style', ITEMORA_PLUGIN_URL . 'assets/css/admin-style.css', array(), ITEMORA_VERSION);
    }
}
add_action('admin_enqueue_scripts', 'itemora_admin_styles');