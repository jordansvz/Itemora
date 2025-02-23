<?php
// Formulario frontend para agregar/editar productos
function itemora_formulario_productos_frontend() {
    if (!current_user_can('edit_posts')) {
        return 'No tienes permisos para gestionar productos.';
    }

    global $wpdb;
    $campos = $wpdb->get_results("SELECT option_name, option_value FROM $wpdb->options WHERE option_name LIKE 'campo_personalizado_%'");
    $tipos_producto = get_terms(array('taxonomy' => 'tipo_producto', 'hide_empty' => false));

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['agregar_producto'])) {
            $titulo = sanitize_text_field($_POST['titulo']);
            $contenido = sanitize_textarea_field($_POST['contenido']);
            $producto_id = wp_insert_post(array(
                'post_title'   => $titulo,
                'post_content' => $contenido,
                'post_type'    => 'producto',
                'post_status'  => 'publish',
            ));

            if ($producto_id) {
                foreach ($campos as $campo) {
                    $campo_id = str_replace('campo_personalizado_', '', $campo->option_name);
                    if (isset($_POST['campo_' . $campo_id])) {
                        update_post_meta($producto_id, '_campo_' . $campo_id, sanitize_text_field($_POST['campo_' . $campo_id]));
                    }
                }

                if (isset($_POST['tipo_producto']) && !empty($_POST['tipo_producto'])) {
                    $tipos_seleccionados = array_map('intval', $_POST['tipo_producto']);
                    wp_set_object_terms($producto_id, $tipos_seleccionados, 'tipo_producto');
                }

                echo '<p>Producto agregado correctamente.</p>';
            } else {
                echo '<p>Error al agregar el producto.</p>';
            }
        }
    }

    ob_start();
    ?>
    <form method="post">
        <label for="titulo">Título del producto:</label>
        <input type="text" name="titulo" id="titulo" required><br>

        <label for="contenido">Descripción:</label>
        <textarea name="contenido" id="contenido" required></textarea><br>

        <h3>Campos personalizados:</h3>
        <?php foreach ($campos as $campo): ?>
            <?php
            $campo_id = str_replace('campo_personalizado_', '', $campo->option_name);
            $campo_nombre = esc_html($campo->option_value);
            ?>
            <label for="campo_<?php echo $campo_id; ?>"><?php echo $campo_nombre; ?>:</label>
            <input type="text" name="campo_<?php echo $campo_id; ?>" id="campo_<?php echo $campo_id; ?>"><br>
        <?php endforeach; ?>

        <h3>Tipo de Producto:</h3>
        <?php if (!empty($tipos_producto)): ?>
            <?php foreach ($tipos_producto as $tipo): ?>
                <label>
                    <input type="checkbox" name="tipo_producto[]" value="<?php echo esc_attr($tipo->term_id); ?>">
                    <?php echo esc_html($tipo->name); ?>
                </label><br>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No hay tipos de producto disponibles.</p>
        <?php endif; ?>

        <button type="submit" name="agregar_producto">Agregar Producto</button>
    </form>
    <?php
    return ob_get_clean();
}