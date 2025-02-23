<?php
// Registrar Custom Post Type "Producto"
function itemora_registrar_cpt_producto() {
    $labels = array(
        'name'                  => __('Productos', 'itemora'),
        'singular_name'         => __('Producto', 'itemora'),
        'menu_name'             => __('Productos', 'itemora'),
        'add_new'               => __('Agregar Nuevo', 'itemora'),
        'add_new_item'          => __('Agregar Nuevo Producto', 'itemora'),
        'edit_item'             => __('Editar Producto', 'itemora'),
        'new_item'              => __('Nuevo Producto', 'itemora'),
        'view_item'             => __('Ver Producto', 'itemora'),
        'search_items'          => __('Buscar Productos', 'itemora'),
        'not_found'             => __('No se encontraron productos', 'itemora'),
        'not_found_in_trash'    => __('No se encontraron productos en la papelera', 'itemora'),
    );

    $args = array(
        'labels'                => $labels,
        'public'                => true,
        'publicly_queryable'    => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'query_var'             => true,
        'rewrite'               => array('slug' => 'productos'),
        'capability_type'       => 'post',
        'has_archive'           => true,
        'hierarchical'          => false,
        'menu_position'         => 5,
        'supports'              => array('title', 'editor', 'thumbnail', 'excerpt'),
        'show_in_rest'          => true,
    );

    register_post_type('producto', $args);
}
add_action('init', 'itemora_registrar_cpt_producto');