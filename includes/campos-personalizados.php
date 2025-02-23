<?php
// Agregar meta box para campos personalizados
function itemora_agregar_meta_box() {
    $activar_detalles = get_option('itemora_activar_detalles', 'no');
    $activar_extras = get_option('itemora_activar_extras', 'no');

    if ($activar_detalles === 'yes' || $activar_extras === 'yes') {
        add_meta_box(
            'itemora_campos_personalizados', // ID del meta box
            'Campos Personalizados',        // Título
            'itemora_renderizar_meta_box',  // Callback para renderizar el contenido
            'producto',                     // Tipo de post
            'normal',                       // Contexto (normal, side, advanced)
            'high'                          // Prioridad
        );
    }
}
add_action('add_meta_boxes', 'itemora_agregar_meta_box');

// Renderizar el contenido del meta box
function itemora_renderizar_meta_box($post) {
    // Obtener valores guardados
    $detalles_01 = get_post_meta($post->ID, '_detalles_01', true);
    $detalles_02 = get_post_meta($post->ID, '_detalles_02', true);
    $detalles_03 = get_post_meta($post->ID, '_detalles_03', true);
    $extra_01 = get_post_meta($post->ID, '_extra_01', true);
    $extra_02 = get_post_meta($post->ID, '_extra_02', true);
    $extra_03 = get_post_meta($post->ID, '_extra_03', true);

    // Labels editables (obtenidos desde las opciones)
    $label_detalles_01 = get_option('itemora_label_detalles_01', 'Detalle 1');
    $label_detalles_02 = get_option('itemora_label_detalles_02', 'Detalle 2');
    $label_detalles_03 = get_option('itemora_label_detalles_03', 'Detalle 3');
    $label_extra_01 = get_option('itemora_label_extra_01', 'Extra 1');
    $label_extra_02 = get_option('itemora_label_extra_02', 'Extra 2');
    $label_extra_03 = get_option('itemora_label_extra_03', 'Extra 3');

    // Verificar si los grupos están activados
    $activar_detalles = get_option('itemora_activar_detalles', 'no');
    $activar_extras = get_option('itemora_activar_extras', 'no');
    ?>
    <div>
        <?php if ($activar_detalles === 'yes'): ?>
            <h4>Detalles</h4>
            <p>
                <label for="detalles_01"><?php echo esc_html($label_detalles_01); ?>:</label>
                <input type="text" id="detalles_01" name="detalles_01" value="<?php echo esc_attr($detalles_01); ?>" style="width: 100%;">
            </p>
            <p>
                <label for="detalles_02"><?php echo esc_html($label_detalles_02); ?>:</label>
                <input type="text" id="detalles_02" name="detalles_02" value="<?php echo esc_attr($detalles_02); ?>" style="width: 100%;">
            </p>
            <p>
                <label for="detalles_03"><?php echo esc_html($label_detalles_03); ?>:</label>
                <input type="text" id="detalles_03" name="detalles_03" value="<?php echo esc_attr($detalles_03); ?>" style="width: 100%;">
            </p>
        <?php endif; ?>

        <?php if ($activar_extras === 'yes'): ?>
            <h4>Extras</h4>
            <p>
                <label for="extra_01"><?php echo esc_html($label_extra_01); ?>:</label>
                <input type="text" id="extra_01" name="extra_01" value="<?php echo esc_attr($extra_01); ?>" style="width: 100%;">
            </p>
            <p>
                <label for="extra_02"><?php echo esc_html($label_extra_02); ?>:</label>
                <input type="text" id="extra_02" name="extra_02" value="<?php echo esc_attr($extra_02); ?>" style="width: 100%;">
            </p>
            <p>
                <label for="extra_03"><?php echo esc_html($label_extra_03); ?>:</label>
                <input type="text" id="extra_03" name="extra_03" value="<?php echo esc_attr($extra_03); ?>" style="width: 100%;">
            </p>
        <?php endif; ?>
    </div>
    <?php
}

// Guardar los valores de los campos personalizados
function itemora_guardar_meta_box($post_id) {
    // Verificar nonce y permisos (opcional, pero recomendado)
    if (!isset($_POST['detalles_01']) && !isset($_POST['extra_01'])) {
        return;
    }

    // Guardar campos de Detalles
    if (isset($_POST['detalles_01'])) {
        update_post_meta($post_id, '_detalles_01', sanitize_text_field($_POST['detalles_01']));
    }
    if (isset($_POST['detalles_02'])) {
        update_post_meta($post_id, '_detalles_02', sanitize_text_field($_POST['detalles_02']));
    }
    if (isset($_POST['detalles_03'])) {
        update_post_meta($post_id, '_detalles_03', sanitize_text_field($_POST['detalles_03']));
    }

    // Guardar campos de Extras
    if (isset($_POST['extra_01'])) {
        update_post_meta($post_id, '_extra_01', sanitize_text_field($_POST['extra_01']));
    }
    if (isset($_POST['extra_02'])) {
        update_post_meta($post_id, '_extra_02', sanitize_text_field($_POST['extra_02']));
    }
    if (isset($_POST['extra_03'])) {
        update_post_meta($post_id, '_extra_03', sanitize_text_field($_POST['extra_03']));
    }
}
add_action('save_post', 'itemora_guardar_meta_box');

// Formulario frontend para gestionar campos personalizados
function itemora_formulario_campos_personalizados() {
    if (!current_user_can('manage_options')) {
        return 'No tienes permisos para gestionar campos personalizados.';
    }

    // Procesar el formulario
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Agregar nuevo campo
        if (isset($_POST['agregar_campo']) && isset($_POST['nombre_campo'])) {
            $nombre_campo = sanitize_text_field($_POST['nombre_campo']);
            if (!empty($nombre_campo)) {
                add_option('campo_personalizado_' . sanitize_key($nombre_campo), $nombre_campo);
                echo '<p>Campo agregado correctamente.</p>';
            }
        }
        // Editar campo existente
        elseif (isset($_POST['editar_campo']) && isset($_POST['campo_id']) && isset($_POST['nuevo_nombre_campo'])) {
            $campo_id = sanitize_text_field($_POST['campo_id']);
            $nuevo_nombre_campo = sanitize_text_field($_POST['nuevo_nombre_campo']);
            if (!empty($nuevo_nombre_campo)) {
                update_option('campo_personalizado_' . $campo_id, $nuevo_nombre_campo);
                echo '<p>Campo editado correctamente.</p>';
            }
        }
        // Eliminar campo
        elseif (isset($_POST['eliminar_campo']) && isset($_POST['campo_id'])) {
            $campo_id = sanitize_text_field($_POST['campo_id']);
            delete_option('campo_personalizado_' . $campo_id);
            echo '<p>Campo eliminado correctamente.</p>';
        }
    }

    // Obtener todos los campos personalizados existentes
    global $wpdb;
    $campos = $wpdb->get_results("SELECT option_name, option_value FROM $wpdb->options WHERE option_name LIKE 'campo_personalizado_%'");

    ob_start();
    ?>
    <form method="post">
        <label for="nombre_campo">Nombre del campo personalizado:</label>
        <input type="text" name="nombre_campo" id="nombre_campo" required>
        <button type="submit" name="agregar_campo">Agregar Campo</button>
    </form>

    <h3>Campos personalizados existentes:</h3>
    <?php if (!empty($campos)): ?>
        <ul>
            <?php foreach ($campos as $campo): ?>
                <?php
                $campo_id = str_replace('campo_personalizado_', '', $campo->option_name);
                $campo_nombre = esc_html($campo->option_value);
                ?>
                <li>
                    <strong><?php echo $campo_nombre; ?>:</strong>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="campo_id" value="<?php echo esc_attr($campo_id); ?>">
                        <button type="submit" name="eliminar_campo">Eliminar</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No hay campos personalizados disponibles.</p>
    <?php endif; ?>
    <?php
    return ob_get_clean();
}