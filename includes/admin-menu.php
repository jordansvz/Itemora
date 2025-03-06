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

function itemora_admin_page() {
    $tab_actual = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'inicio';
    ?>
    <div class="wrap itemora-admin">
        <h1>Itemora</h1>
        
        <nav class="nav-tab-wrapper">
            <a href="?page=itemora&tab=inicio" class="nav-tab <?php echo $tab_actual == 'inicio' ? 'nav-tab-active' : ''; ?>">Inicio</a>
            <a href="?page=itemora&tab=sucursales" class="nav-tab <?php echo $tab_actual == 'sucursales' ? 'nav-tab-active' : ''; ?>">Sucursales</a>
            <a href="?page=itemora&tab=detalles" class="nav-tab <?php echo $tab_actual == 'detalles' ? 'nav-tab-active' : ''; ?>">Detalles</a>
            <a href="?page=itemora&tab=extras" class="nav-tab <?php echo $tab_actual == 'extras' ? 'nav-tab-active' : ''; ?>">Extras</a>
            <a href="?page=itemora&tab=importar" class="nav-tab <?php echo $tab_actual == 'importar' ? 'nav-tab-active' : ''; ?>">Importar</a>
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
            }
            ?>
        </div>
    </div>
    <?php
}

function itemora_tab_inicio() {
    $productos_count = wp_count_posts('itemora_producto')->publish;
    $categorias_count = wp_count_terms('itemora_categoria', ['taxonomy' => 'itemora_categoria']);
    $sucursales_count = wp_count_terms('itemora_sucursal', ['taxonomy' => 'itemora_sucursal']);
    ?>
    <div class="itemora-dashboard">
        <div class="itemora-stats">
            <div class="itemora-stat-card">
                <h3>Productos</h3>
                <div class="stat-number"><?php echo $productos_count; ?></div>
                <a href="<?php echo admin_url('edit.php?post_type=itemora_producto'); ?>" class="button">Ver Productos</a>
            </div>
            
            <div class="itemora-stat-card">
                <h3>Categorías</h3>
                <div class="stat-number"><?php echo $categorias_count; ?></div>
                <a href="<?php echo admin_url('edit-tags.php?taxonomy=itemora_categoria&post_type=itemora_producto'); ?>" class="button">Ver Categorías</a>
            </div>
            
            <div class="itemora-stat-card">
                <h3>Sucursales</h3>
                <div class="stat-number"><?php echo $sucursales_count; ?></div>
                <a href="<?php echo admin_url('edit-tags.php?taxonomy=itemora_sucursal&post_type=itemora_producto'); ?>" class="button">Ver Sucursales</a>
            </div>
        </div>
    </div>
    <?php
}

function itemora_tab_sucursales() {
    if (isset($_POST['itemora_guardar_sucursales'])) {
        update_option('itemora_activar_sucursal', isset($_POST['activar_sucursal']) ? 'yes' : 'no');
        echo '<div class="updated"><p>Configuración guardada.</p></div>';
    }
    
    $activar_sucursal = get_option('itemora_activar_sucursal', 'no');
    ?>
    <div class="itemora-card">
        <form method="post">
            <div class="itemora-field-row">
                <label>
                    <input type="checkbox" name="activar_sucursal" value="yes" <?php checked($activar_sucursal, 'yes'); ?>>
                    Activar gestión de sucursales
                </label>
            </div>
            <div class="itemora-submit-row">
                <input type="submit" name="itemora_guardar_sucursales" class="button button-primary" value="Guardar Cambios">
            </div>
        </form>
    </div>
    <?php
}

function itemora_tab_detalles() {
    if (isset($_POST['itemora_guardar_detalles'])) {
        update_option('itemora_activar_detalles', isset($_POST['activar_detalles']) ? 'yes' : 'no');
        update_option('itemora_label_detalles_01', sanitize_text_field($_POST['label_detalles_01']));
        update_option('itemora_label_detalles_02', sanitize_text_field($_POST['label_detalles_02']));
        update_option('itemora_label_detalles_03', sanitize_text_field($_POST['label_detalles_03']));
        echo '<div class="updated"><p>Configuración guardada.</p></div>';
    }
    
    $activar_detalles = get_option('itemora_activar_detalles', 'no');
    $label_detalles_01 = get_option('itemora_label_detalles_01', 'Detalle 1');
    $label_detalles_02 = get_option('itemora_label_detalles_02', 'Detalle 2');
    $label_detalles_03 = get_option('itemora_label_detalles_03', 'Detalle 3');
    ?>
    <div class="itemora-card">
        <form method="post">
            <div class="itemora-field-row">
                <label>
                    <input type="checkbox" name="activar_detalles" value="yes" <?php checked($activar_detalles, 'yes'); ?>>
                    Activar campos de detalles
                </label>
            </div>
            
            <div class="itemora-fields-section">
                <div class="itemora-field-row">
                    <label>Etiqueta 1:</label>
                    <input type="text" name="label_detalles_01" value="<?php echo esc_attr($label_detalles_01); ?>">
                </div>
                <div class="itemora-field-row">
                    <label>Etiqueta 2:</label>
                    <input type="text" name="label_detalles_02" value="<?php echo esc_attr($label_detalles_02); ?>">
                </div>
                <div class="itemora-field-row">
                    <label>Etiqueta 3:</label>
                    <input type="text" name="label_detalles_03" value="<?php echo esc_attr($label_detalles_03); ?>">
                </div>
            </div>
            
            <div class="itemora-submit-row">
                <input type="submit" name="itemora_guardar_detalles" class="button button-primary" value="Guardar Cambios">
            </div>
        </form>
    </div>
    <?php
}

function itemora_tab_extras() {
    if (isset($_POST['itemora_guardar_extras'])) {
        update_option('itemora_activar_extras', isset($_POST['activar_extras']) ? 'yes' : 'no');
        update_option('itemora_label_extra_01', sanitize_text_field($_POST['label_extra_01']));
        update_option('itemora_label_extra_02', sanitize_text_field($_POST['label_extra_02']));
        update_option('itemora_label_extra_03', sanitize_text_field($_POST['label_extra_03']));
        echo '<div class="updated"><p>Configuración guardada.</p></div>';
    }
    
    $activar_extras = get_option('itemora_activar_extras', 'no');
    $label_extra_01 = get_option('itemora_label_extra_01', 'Extra 1');
    $label_extra_02 = get_option('itemora_label_extra_02', 'Extra 2');
    $label_extra_03 = get_option('itemora_label_extra_03', 'Extra 3');
    ?>
    <div class="itemora-card">
        <form method="post">
            <div class="itemora-field-row">
                <label>
                    <input type="checkbox" name="activar_extras" value="yes" <?php checked($activar_extras, 'yes'); ?>>
                    Activar campos extras
                </label>
            </div>
            
            <div class="itemora-fields-section">
                <div class="itemora-field-row">
                    <label>Etiqueta 1:</label>
                    <input type="text" name="label_extra_01" value="<?php echo esc_attr($label_extra_01); ?>">
                </div>
                <div class="itemora-field-row">
                    <label>Etiqueta 2:</label>
                    <input type="text" name="label_extra_02" value="<?php echo esc_attr($label_extra_02); ?>">
                </div>
                <div class="itemora-field-row">
                    <label>Etiqueta 3:</label>
                    <input type="text" name="label_extra_03" value="<?php echo esc_attr($label_extra_03); ?>">
                </div>
            </div>
            
            <div class="itemora-submit-row">
                <input type="submit" name="itemora_guardar_extras" class="button button-primary" value="Guardar Cambios">
            </div>
        </form>
    </div>
    <?php
}

function itemora_tab_importar() {
    if (isset($_POST['itemora_importar_csv'])) {
        if (!empty($_FILES['archivo_csv']['tmp_name'])) {
            // Aquí iría el código para procesar el CSV
            echo '<div class="updated"><p>Archivo recibido. Procesando importación...</p></div>';
        } else {
            echo '<div class="error"><p>Por favor seleccione un archivo CSV.</p></div>';
        }
    }
    ?>
    <div class="itemora-card">
        <h2>Importar Productos</h2>
        <p>Seleccione un archivo CSV con los productos a importar.</p>
        
        <form method="post" enctype="multipart/form-data">
            <div class="itemora-field-row">
                <label>Archivo CSV:</label>
                <input type="file" name="archivo_csv" accept=".csv">
            </div>
            
            <div class="itemora-submit-row">
                <input type="submit" name="itemora_importar_csv" class="button button-primary" value="Importar Productos">
            </div>
        </form>
    </div>
    <?php
}