<?php
// Registrar Taxonomía "Tipo de Producto"
function itemora_registrar_taxonomia_tipo_producto() {
    $labels = array(
        'name'                  => __('Tipos de Producto', 'itemora'),
        'singular_name'         => __('Tipo de Producto', 'itemora'),
        'search_items'          => __('Buscar Tipos', 'itemora'),
        'all_items'             => __('Todos los Tipos', 'itemora'),
        'parent_item'           => __('Tipo Padre', 'itemora'),
        'parent_item_colon'     => __('Tipo Padre:', 'itemora'),
        'edit_item'             => __('Editar Tipo', 'itemora'),
        'update_item'           => __('Actualizar Tipo', 'itemora'),
        'add_new_item'          => __('Agregar Nuevo Tipo', 'itemora'),
        'new_item_name'         => __('Nombre del Nuevo Tipo', 'itemora'),
        'menu_name'             => __('Tipos de Producto', 'itemora'),
    );

    $args = array(
        'hierarchical'          => true,
        'labels'                => $labels,
        'show_ui'               => true,
        'show_admin_column'     => true,
        'query_var'             => true,
        'rewrite'               => array('slug' => 'tipo-producto'),
        'show_in_rest'          => true,
    );

    register_taxonomy('tipo_producto', array('producto'), $args);
}
add_action('init', 'itemora_registrar_taxonomia_tipo_producto');

// Registrar Taxonomía "Sucursal"
function itemora_registrar_taxonomia_sucursal() {
    if (get_option('itemora_activar_sucursal') !== 'yes') {
        return; // No registrar si no está activada
    }

    $labels = array(
        'name'                  => __('Sucursales', 'itemora'),
        'singular_name'         => __('Sucursal', 'itemora'),
        'search_items'          => __('Buscar Sucursales', 'itemora'),
        'all_items'             => __('Todas las Sucursales', 'itemora'),
        'parent_item'           => __('Sucursal Padre', 'itemora'),
        'parent_item_colon'     => __('Sucursal Padre:', 'itemora'),
        'edit_item'             => __('Editar Sucursal', 'itemora'),
        'update_item'           => __('Actualizar Sucursal', 'itemora'),
        'add_new_item'          => __('Agregar Nueva Sucursal', 'itemora'),
        'new_item_name'         => __('Nombre de la Nueva Sucursal', 'itemora'),
        'menu_name'             => __('Sucursales', 'itemora'),
    );

    $args = array(
        'hierarchical'          => true,
        'labels'                => $labels,
        'show_ui'               => true,
        'show_admin_column'     => true,
        'query_var'             => true,
        'rewrite'               => array('slug' => 'sucursal'),
        'show_in_rest'          => true,
    );

    register_taxonomy('sucursal', array('producto'), $args);
}
add_action('init', 'itemora_registrar_taxonomia_sucursal');

// Agregar campos adicionales a la taxonomía "Sucursal"
function itemora_agregar_campos_sucursal($term) {
    // Verificar si estamos editando o agregando un nuevo término
    $is_edit = isset($term->term_id); // True si estamos editando, false si estamos agregando

    // Obtener valores actuales de los campos adicionales
    $ubicacion = $is_edit ? get_term_meta($term->term_id, '_ubicacion', true) : '';
    $telefono = $is_edit ? get_term_meta($term->term_id, '_telefono', true) : '';

    // HTML para el formulario
    ?>
    <div class="form-field">
        <label for="ubicacion">Ubicación (Google Maps)</label>
        <input type="text" name="ubicacion" id="ubicacion" value="<?php echo esc_attr($ubicacion); ?>">
        <p>Ingresa la URL o coordenadas de Google Maps.</p>
    </div>
    <div class="form-field">
        <label for="telefono">Teléfono</label>
        <input type="text" name="telefono" id="telefono" value="<?php echo esc_attr($telefono); ?>">
        <p>Ingresa el número de teléfono de la sucursal.</p>
    </div>
    <?php
}
add_action('sucursal_add_form_fields', 'itemora_agregar_campos_sucursal');
add_action('sucursal_edit_form_fields', 'itemora_agregar_campos_sucursal');

// Guardar campos adicionales de la taxonomía "Sucursal"
function itemora_guardar_campos_sucursal($term_id) {
    if (!$term_id) {
        return; // Salir si no hay un ID válido
    }

    // Guardar campo "Ubicación"
    if (isset($_POST['ubicacion'])) {
        update_term_meta($term_id, '_ubicacion', sanitize_text_field($_POST['ubicacion']));
    } else {
        delete_term_meta($term_id, '_ubicacion'); // Eliminar si no se proporciona un valor
    }

    // Guardar campo "Teléfono"
    if (isset($_POST['telefono'])) {
        update_term_meta($term_id, '_telefono', sanitize_text_field($_POST['telefono']));
    } else {
        delete_term_meta($term_id, '_telefono'); // Eliminar si no se proporciona un valor
    }
}
add_action('created_sucursal', 'itemora_guardar_campos_sucursal');
add_action('edited_sucursal', 'itemora_guardar_campos_sucursal');