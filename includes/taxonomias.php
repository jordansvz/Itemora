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

// Registrar Taxonomía "Sucursal" (opcional)
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