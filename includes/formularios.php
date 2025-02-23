<?php
// Formulario frontend para agregar/editar productos
function itemora_formulario_productos_frontend() {
    // Verificar si el usuario tiene permisos
    if (!current_user_can('edit_posts')) {
        return 'No tienes permisos para gestionar productos.';
    }

    // Obtener opciones de configuración
    $activar_sucursal = get_option('itemora_activar_sucursal', 'no');
    $activar_detalles = get_option('itemora_activar_detalles', 'no');
    $activar_extras = get_option('itemora_activar_extras', 'no');

    // Obtener todas las categorías/tipos de producto
    $tipos_producto = get_terms(array(
        'taxonomy'   => 'tipo_producto',
        'hide_empty' => false,
    ));

    // Obtener todas las sucursales (si están activadas)
    $sucursales = ($activar_sucursal === 'yes') ? get_terms(array(
        'taxonomy'   => 'sucursal',
        'hide_empty' => false,
    )) : array();

    // Procesar el formulario
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['agregar_producto'])) {
            // Agregar nuevo producto
            $titulo = sanitize_text_field($_POST['titulo']);
            $contenido = sanitize_textarea_field($_POST['descripcion']);
            $producto_id = wp_insert_post(array(
                'post_title'   => $titulo,
                'post_content' => $contenido,
                'post_type'    => 'producto',
                'post_status'  => 'publish',
            ));

            if ($producto_id) {
                // Asignar tipos de producto (categorías)
                if (isset($_POST['tipo_producto']) && !empty($_POST['tipo_producto'])) {
                    $tipos_seleccionados = array_map('intval', $_POST['tipo_producto']);
                    wp_set_object_terms($producto_id, $tipos_seleccionados, 'tipo_producto');
                }

                // Asignar sucursales (si están activadas)
                if ($activar_sucursal === 'yes' && isset($_POST['sucursal']) && !empty($_POST['sucursal'])) {
                    $sucursales_seleccionadas = array_map('intval', $_POST['sucursal']);
                    wp_set_object_terms($producto_id, $sucursales_seleccionadas, 'sucursal');
                }

                // Guardar campos personalizados (Detalles y Extras)
                if ($activar_detalles === 'yes') {
                    update_post_meta($producto_id, '_detalles_01', sanitize_text_field($_POST['detalles_01']));
                    update_post_meta($producto_id, '_detalles_02', sanitize_text_field($_POST['detalles_02']));
                    update_post_meta($producto_id, '_detalles_03', sanitize_text_field($_POST['detalles_03']));
                }
                if ($activar_extras === 'yes') {
                    update_post_meta($producto_id, '_extra_01', sanitize_text_field($_POST['extra_01']));
                    update_post_meta($producto_id, '_extra_02', sanitize_text_field($_POST['extra_02']));
                    update_post_meta($producto_id, '_extra_03', sanitize_text_field($_POST['extra_03']));
                }

                echo '<p>Producto agregado correctamente.</p>';
            } else {
                echo '<p>Error al agregar el producto.</p>';
            }
        } elseif (isset($_POST['editar_producto']) && isset($_POST['producto_id'])) {
            // Editar producto existente
            $producto_id = intval($_POST['producto_id']);
            $titulo = sanitize_text_field($_POST['titulo']);
            $contenido = sanitize_textarea_field($_POST['descripcion']);

            wp_update_post(array(
                'ID'           => $producto_id,
                'post_title'   => $titulo,
                'post_content' => $contenido,
            ));

            // Actualizar tipos de producto (categorías)
            if (isset($_POST['tipo_producto']) && !empty($_POST['tipo_producto'])) {
                $tipos_seleccionados = array_map('intval', $_POST['tipo_producto']);
                wp_set_object_terms($producto_id, $tipos_seleccionados, 'tipo_producto');
            } else {
                wp_set_object_terms($producto_id, array(), 'tipo_producto'); // Limpiar categorías si no se selecciona ninguna
            }

            // Actualizar sucursales (si están activadas)
            if ($activar_sucursal === 'yes') {
                if (isset($_POST['sucursal']) && !empty($_POST['sucursal'])) {
                    $sucursales_seleccionadas = array_map('intval', $_POST['sucursal']);
                    wp_set_object_terms($producto_id, $sucursales_seleccionadas, 'sucursal');
                } else {
                    wp_set_object_terms($producto_id, array(), 'sucursal'); // Limpiar sucursales si no se selecciona ninguna
                }
            }

            // Actualizar campos personalizados (Detalles y Extras)
            if ($activar_detalles === 'yes') {
                update_post_meta($producto_id, '_detalles_01', sanitize_text_field($_POST['detalles_01']));
                update_post_meta($producto_id, '_detalles_02', sanitize_text_field($_POST['detalles_02']));
                update_post_meta($producto_id, '_detalles_03', sanitize_text_field($_POST['detalles_03']));
            }
            if ($activar_extras === 'yes') {
                update_post_meta($producto_id, '_extra_01', sanitize_text_field($_POST['extra_01']));
                update_post_meta($producto_id, '_extra_02', sanitize_text_field($_POST['extra_02']));
                update_post_meta($producto_id, '_extra_03', sanitize_text_field($_POST['extra_03']));
            }

            echo '<p>Producto editado correctamente.</p>';
        } elseif (isset($_POST['eliminar_producto']) && isset($_POST['producto_id'])) {
            // Eliminar producto
            $producto_id = intval($_POST['producto_id']);
            wp_delete_post($producto_id, true); // true para eliminar permanentemente
            echo '<p>Producto eliminado correctamente.</p>';
        }
    }

    // Mostrar formulario
    ob_start();
    ?>
    <form method="post">
        <h3><?php echo isset($_GET['editar']) ? 'Editar Producto' : 'Agregar Producto'; ?></h3>

        <label for="titulo">Título:</label>
        <input type="text" name="titulo" id="titulo" value="<?php echo isset($_GET['editar']) ? get_the_title($_GET['editar']) : ''; ?>" required><br>

        <label for="descripcion">Descripción:</label>
        <textarea name="descripcion" id="descripcion"><?php echo isset($_GET['editar']) ? get_post_field('post_content', $_GET['editar']) : ''; ?></textarea><br>

        <label for="tipo_producto">Tipo de Producto:</label>
        <select name="tipo_producto[]" id="tipo_producto" multiple>
            <?php foreach ($tipos_producto as $tipo): ?>
                <option value="<?php echo esc_attr($tipo->term_id); ?>" <?php selected(isset($_GET['editar']) && has_term($tipo->term_id, 'tipo_producto', $_GET['editar'])); ?>>
                    <?php echo esc_html($tipo->name); ?>
                </option>
            <?php endforeach; ?>
        </select><br>

        <?php if ($activar_sucursal === 'yes'): ?>
            <label for="sucursal">Sucursal:</label>
            <select name="sucursal[]" id="sucursal" multiple>
                <?php foreach ($sucursales as $sucursal): ?>
                    <option value="<?php echo esc_attr($sucursal->term_id); ?>" <?php selected(isset($_GET['editar']) && has_term($sucursal->term_id, 'sucursal', $_GET['editar'])); ?>>
                        <?php echo esc_html($sucursal->name); ?>
                    </option>
                <?php endforeach; ?>
            </select><br>
        <?php endif; ?>

        <?php if ($activar_detalles === 'yes'): ?>
            <h4>Detalles</h4>
            <label for="detalles_01"><?php echo get_option('itemora_label_detalles_01', 'Detalle 1'); ?>:</label>
            <input type="text" name="detalles_01" id="detalles_01" value="<?php echo isset($_GET['editar']) ? get_post_meta($_GET['editar'], '_detalles_01', true) : ''; ?>"><br>

            <label for="detalles_02"><?php echo get_option('itemora_label_detalles_02', 'Detalle 2'); ?>:</label>
            <input type="text" name="detalles_02" id="detalles_02" value="<?php echo isset($_GET['editar']) ? get_post_meta($_GET['editar'], '_detalles_02', true) : ''; ?>"><br>

            <label for="detalles_03"><?php echo get_option('itemora_label_detalles_03', 'Detalle 3'); ?>:</label>
            <input type="text" name="detalles_03" id="detalles_03" value="<?php echo isset($_GET['editar']) ? get_post_meta($_GET['editar'], '_detalles_03', true) : ''; ?>"><br>
        <?php endif; ?>

        <?php if ($activar_extras === 'yes'): ?>
            <h4>Extras</h4>
            <label for="extra_01"><?php echo get_option('itemora_label_extra_01', 'Extra 1'); ?>:</label>
            <input type="text" name="extra_01" id="extra_01" value="<?php echo isset($_GET['editar']) ? get_post_meta($_GET['editar'], '_extra_01', true) : ''; ?>"><br>

            <label for="extra_02"><?php echo get_option('itemora_label_extra_02', 'Extra 2'); ?>:</label>
            <input type="text" name="extra_02" id="extra_02" value="<?php echo isset($_GET['editar']) ? get_post_meta($_GET['editar'], '_extra_02', true) : ''; ?>"><br>

            <label for="extra_03"><?php echo get_option('itemora_label_extra_03', 'Extra 3'); ?>:</label>
            <input type="text" name="extra_03" id="extra_03" value="<?php echo isset($_GET['editar']) ? get_post_meta($_GET['editar'], '_extra_03', true) : ''; ?>"><br>
        <?php endif; ?>

        <input type="hidden" name="producto_id" value="<?php echo isset($_GET['editar']) ? intval($_GET['editar']) : ''; ?>">
        <button type="submit" name="<?php echo isset($_GET['editar']) ? 'editar_producto' : 'agregar_producto'; ?>">
            <?php echo isset($_GET['editar']) ? 'Guardar Cambios' : 'Agregar Producto'; ?>
        </button>
    </form>

    <h3>Productos Existentes</h3>
    <?php
    $productos = get_posts(array(
        'post_type'      => 'producto',
        'posts_per_page' => -1,
    ));
    if (!empty($productos)): ?>
        <ul>
            <?php foreach ($productos as $producto): ?>
                <li>
                    <?php echo esc_html($producto->post_title); ?>
                    <a href="?editar=<?php echo esc_attr($producto->ID); ?>">Editar</a>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="producto_id" value="<?php echo esc_attr($producto->ID); ?>">
                        <button type="submit" name="eliminar_producto">Eliminar</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No hay productos disponibles.</p>
    <?php endif; ?>
    <?php
    return ob_get_clean();
}