<?php
// Agregar menú de configuración en el dashboard
function itemora_agregar_menu_admin() {
    add_menu_page(
        'Configuración de Itemora', // Título de la página
        'Itemora',                 // Nombre del menú
        'manage_options',          // Capacidad requerida
        'itemora-configuracion',   // Slug
        'itemora_pagina_configuracion', // Función callback
        'dashicons-admin-generic', // Icono
        6                          // Posición en el menú
    );
}
add_action('admin_menu', 'itemora_agregar_menu_admin');

// Callback para mostrar la página de configuración
function itemora_pagina_configuracion() {
    if (!current_user_can('manage_options')) {
        return;
    }

    // Procesar el formulario de configuración
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['itemora_guardar_opciones'])) {
        // Guardar opciones generales
        update_option('itemora_activar_sucursal', isset($_POST['activar_sucursal']) ? 'yes' : 'no');
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
    $activar_sucursal = get_option('itemora_activar_sucursal', 'no');
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
    <div class="wrap">
        <h1>Configuración de Itemora</h1>
        <form method="post">
            <table class="form-table">
                <tr>
                    <th scope="row">Activar Sucursal</th>
                    <td>
                        <label><input type="checkbox" name="activar_sucursal" value="yes" <?php checked($activar_sucursal, 'yes'); ?>> Activar</label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Activar Detalles</th>
                    <td>
                        <label><input type="checkbox" name="activar_detalles" value="yes" <?php checked($activar_detalles, 'yes'); ?>> Activar</label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Activar Extras</th>
                    <td>
                        <label><input type="checkbox" name="activar_extras" value="yes" <?php checked($activar_extras, 'yes'); ?>> Activar</label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Labels de Detalles</th>
                    <td>
                        <label for="label_detalles_01">Detalle 1:</label>
                        <input type="text" name="label_detalles_01" id="label_detalles_01" value="<?php echo esc_attr($label_detalles_01); ?>"><br>

                        <label for="label_detalles_02">Detalle 2:</label>
                        <input type="text" name="label_detalles_02" id="label_detalles_02" value="<?php echo esc_attr($label_detalles_02); ?>"><br>

                        <label for="label_detalles_03">Detalle 3:</label>
                        <input type="text" name="label_detalles_03" id="label_detalles_03" value="<?php echo esc_attr($label_detalles_03); ?>"><br>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Labels de Extras</th>
                    <td>
                        <label for="label_extra_01">Extra 1:</label>
                        <input type="text" name="label_extra_01" id="label_extra_01" value="<?php echo esc_attr($label_extra_01); ?>"><br>

                        <label for="label_extra_02">Extra 2:</label>
                        <input type="text" name="label_extra_02" id="label_extra_02" value="<?php echo esc_attr($label_extra_02); ?>"><br>

                        <label for="label_extra_03">Extra 3:</label>
                        <input type="text" name="label_extra_03" id="label_extra_03" value="<?php echo esc_attr($label_extra_03); ?>"><br>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" name="itemora_guardar_opciones" class="button-primary" value="Guardar Cambios">
            </p>
        </form>
    </div>
    <?php
}