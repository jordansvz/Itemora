<?php
/**
 * Custom Post Type Registration for Itemora
 *
 * @package Itemora
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register the Producto custom post type
 */
function itemora_register_producto_cpt() {
    $labels = array(
        'name'                  => _x('Productos', 'Post type general name', 'itemora'),
        'singular_name'         => _x('Producto', 'Post type singular name', 'itemora'),
        'menu_name'             => _x('Productos', 'Admin Menu text', 'itemora'),
        'name_admin_bar'        => _x('Producto', 'Add New on Toolbar', 'itemora'),
        'add_new'               => __('Añadir Nuevo', 'itemora'),
        'add_new_item'          => __('Añadir Nuevo Producto', 'itemora'),
        'new_item'              => __('Nuevo Producto', 'itemora'),
        'edit_item'             => __('Editar Producto', 'itemora'),
        'view_item'             => __('Ver Producto', 'itemora'),
        'all_items'             => __('Todos los Productos', 'itemora'),
        'search_items'          => __('Buscar Productos', 'itemora'),
        'parent_item_colon'     => __('Productos Padre:', 'itemora'),
        'not_found'             => __('No se encontraron productos.', 'itemora'),
        'not_found_in_trash'    => __('No se encontraron productos en la papelera.', 'itemora'),
        'featured_image'        => __('Imagen Destacada', 'itemora'),
        'set_featured_image'    => __('Establecer imagen destacada', 'itemora'),
        'remove_featured_image' => __('Eliminar imagen destacada', 'itemora'),
        'use_featured_image'    => __('Usar como imagen destacada', 'itemora'),
        'archives'              => __('Archivos de Productos', 'itemora'),
        'insert_into_item'      => __('Insertar en producto', 'itemora'),
        'uploaded_to_this_item' => __('Subido a este producto', 'itemora'),
        'filter_items_list'     => __('Filtrar lista de productos', 'itemora'),
        'items_list_navigation' => __('Navegación de lista de productos', 'itemora'),
        'items_list'            => __('Lista de productos', 'itemora'),
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
        'menu_icon'             => 'dashicons-cart',
        'supports'              => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        'show_in_rest'          => true,
        'rest_base'             => 'productos',
        'rest_controller_class' => 'WP_REST_Posts_Controller',
    );

    register_post_type('itemora_producto', $args);
}
add_action('init', 'itemora_register_producto_cpt');

/**
 * Register meta boxes for the Producto custom post type
 */
function itemora_register_producto_meta_boxes() {
    add_meta_box(
        'itemora_producto_details',
        __('Detalles del Producto', 'itemora'),
        'itemora_producto_details_callback',
        'itemora_producto',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'itemora_register_producto_meta_boxes');

/**
 * Meta box display callback
 *
 * @param WP_Post $post Current post object.
 */
function itemora_producto_details_callback($post) {
    // Add nonce for security
    wp_nonce_field('itemora_save_producto_data', 'itemora_producto_nonce');

    // Get current values
    $precio = get_post_meta($post->ID, '_itemora_precio', true);
    $sku = get_post_meta($post->ID, '_itemora_sku', true);
    $stock = get_post_meta($post->ID, '_itemora_stock', true);
    
    // Get detalles if enabled
    $detalles = array();
    if (get_option('itemora_activar_detalles') === 'yes') {
        $detalles[1] = get_post_meta($post->ID, '_itemora_detalle_01', true);
        $detalles[2] = get_post_meta($post->ID, '_itemora_detalle_02', true);
        $detalles[3] = get_post_meta($post->ID, '_itemora_detalle_03', true);
    }
    
    // Get extras if enabled
    $extras = array();
    if (get_option('itemora_activar_extras') === 'yes') {
        $extras[1] = get_post_meta($post->ID, '_itemora_extra_01', true);
        $extras[2] = get_post_meta($post->ID, '_itemora_extra_02', true);
        $extras[3] = get_post_meta($post->ID, '_itemora_extra_03', true);
    }
    ?>
    <div class="itemora-meta-box">
        <div class="itemora-field-row">
            <label for="itemora_precio"><?php _e('Precio', 'itemora'); ?></label>
            <input type="text" id="itemora_precio" name="itemora_precio" value="<?php echo esc_attr($precio); ?>">
        </div>
        
        <div class="itemora-field-row">
            <label for="itemora_sku"><?php _e('SKU', 'itemora'); ?></label>
            <input type="text" id="itemora_sku" name="itemora_sku" value="<?php echo esc_attr($sku); ?>">
        </div>
        
        <div class="itemora-field-row">
            <label for="itemora_stock"><?php _e('Stock', 'itemora'); ?></label>
            <input type="number" id="itemora_stock" name="itemora_stock" value="<?php echo esc_attr($stock); ?>" min="0">
        </div>
        
        <?php if (get_option('itemora_activar_detalles') === 'yes') : ?>
            <div class="itemora-section">
                <h4><?php _e('Detalles', 'itemora'); ?></h4>
                
                <div class="itemora-field-row">
                    <label for="itemora_detalle_01"><?php echo esc_html(get_option('itemora_label_detalles_01', 'Detalle 1')); ?></label>
                    <input type="text" id="itemora_detalle_01" name="itemora_detalle_01" value="<?php echo esc_attr($detalles[1]); ?>">
                </div>
                
                <div class="itemora-field-row">
                    <label for="itemora_detalle_02"><?php echo esc_html(get_option('itemora_label_detalles_02', 'Detalle 2')); ?></label>
                    <input type="text" id="itemora_detalle_02" name="itemora_detalle_02" value="<?php echo esc_attr($detalles[2]); ?>">
                </div>
                
                <div class="itemora-field-row">
                    <label for="itemora_detalle_03"><?php echo esc_html(get_option('itemora_label_detalles_03', 'Detalle 3')); ?></label>
                    <input type="text" id="itemora_detalle_03" name="itemora_detalle_03" value="<?php echo esc_attr($detalles[3]); ?>">
                </div>
            </div>
        <?php endif; ?>
        
        <?php if (get_option('itemora_activar_extras') === 'yes') : ?>
            <div class="itemora-section">
                <h4><?php _e('Extras', 'itemora'); ?></h4>
                
                <div class="itemora-field-row">
                    <label for="itemora_extra_01"><?php echo esc_html(get_option('itemora_label_extra_01', 'Extra 1')); ?></label>
                    <input type="text" id="itemora_extra_01" name="itemora_extra_01" value="<?php echo esc_attr($extras[1]); ?>">
                </div>
                
                <div class="itemora-field-row">
                    <label for="itemora_extra_02"><?php echo esc_html(get_option('itemora_label_extra_02', 'Extra 2')); ?></label>
                    <input type="text" id="itemora_extra_02" name="itemora_extra_02" value="<?php echo esc_attr($extras[2]); ?>">
                </div>
                
                <div class="itemora-field-row">
                    <label for="itemora_extra_03"><?php echo esc_html(get_option('itemora_label_extra_03', 'Extra 3')); ?></label>
                    <input type="text" id="itemora_extra_03" name="itemora_extra_03" value="<?php echo esc_attr($extras[3]); ?>">
                </div>
            </div>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Save meta box content
 *
 * @param int $post_id Post ID
 */
function itemora_save_producto_data($post_id) {
    // Check if nonce is set
    if (!isset($_POST['itemora_producto_nonce'])) {
        return;
    }

    // Verify nonce
    if (!wp_verify_nonce($_POST['itemora_producto_nonce'], 'itemora_save_producto_data')) {
        return;
    }

    // If this is an autosave, don't do anything
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check user permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save precio
    if (isset($_POST['itemora_precio'])) {
        update_post_meta($post_id, '_itemora_precio', sanitize_text_field($_POST['itemora_precio']));
    }

    // Save SKU
    if (isset($_POST['itemora_sku'])) {
        update_post_meta($post_id, '_itemora_sku', sanitize_text_field($_POST['itemora_sku']));
    }

    // Save stock
    if (isset($_POST['itemora_stock'])) {
        update_post_meta($post_id, '_itemora_stock', absint($_POST['itemora_stock']));
    }

    // Save detalles if enabled
    if (get_option('itemora_activar_detalles') === 'yes') {
        if (isset($_POST['itemora_detalle_01'])) {
            update_post_meta($post_id, '_itemora_detalle_01', sanitize_text_field($_POST['itemora_detalle_01']));
        }
        if (isset($_POST['itemora_detalle_02'])) {
            update_post_meta($post_id, '_itemora_detalle_02', sanitize_text_field($_POST['itemora_detalle_02']));
        }
        if (isset($_POST['itemora_detalle_03'])) {
            update_post_meta($post_id, '_itemora_detalle_03', sanitize_text_field($_POST['itemora_detalle_03']));
        }
    }

    // Save extras if enabled
    if (get_option('itemora_activar_extras') === 'yes') {
        if (isset($_POST['itemora_extra_01'])) {
            update_post_meta($post_id, '_itemora_extra_01', sanitize_text_field($_POST['itemora_extra_01']));
        }
        if (isset($_POST['itemora_extra_02'])) {
            update_post_meta($post_id, '_itemora_extra_02', sanitize_text_field($_POST['itemora_extra_02']));
        }
        if (isset($_POST['itemora_extra_03'])) {
            update_post_meta($post_id, '_itemora_extra_03', sanitize_text_field($_POST['itemora_extra_03']));
        }
    }
}
add_action('save_post_itemora_producto', 'itemora_save_producto_data');

/**
 * Add custom columns to the product list
 *
 * @param array $columns Array of columns
 * @return array Modified array of columns
 */
function itemora_add_producto_columns($columns) {
    $new_columns = array();
    
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        
        // Add our columns after title
        if ($key === 'title') {
            $new_columns['precio'] = __('Precio', 'itemora');
            $new_columns['sku'] = __('SKU', 'itemora');
            $new_columns['stock'] = __('Stock', 'itemora');
        }
    }
    
    return $new_columns;
}
add_filter('manage_itemora_producto_posts_columns', 'itemora_add_producto_columns');

/**
 * Display data in custom columns
 *
 * @param string $column Column name
 * @param int $post_id Post ID
 */
function itemora_display_producto_columns($column, $post_id) {
    switch ($column) {
        case 'precio':
            $precio = get_post_meta($post_id, '_itemora_precio', true);
            echo esc_html($precio);
            break;
            
        case 'sku':
            $sku = get_post_meta($post_id, '_itemora_sku', true);
            echo esc_html($sku);
            break;
            
        case 'stock':
            $stock = get_post_meta($post_id, '_itemora_stock', true);
            echo esc_html($stock);
            break;
    }
}
add_action('manage_itemora_producto_posts_custom_column', 'itemora_display_producto_columns', 10, 2);

/**
 * Make custom columns sortable
 *
 * @param array $columns Array of sortable columns
 * @return array Modified array of sortable columns
 */
function itemora_sortable_producto_columns($columns) {
    $columns['precio'] = 'precio';
    $columns['sku'] = 'sku';
    $columns['stock'] = 'stock';
    
    return $columns;
}
add_filter('manage_edit-itemora_producto_sortable_columns', 'itemora_sortable_producto_columns');

/**
 * Handle custom sorting
 *
 * @param WP_Query $query The WordPress query object
 */
function itemora_producto_orderby($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }

    $orderby = $query->get('orderby');

    if ('precio' === $orderby) {
        $query->set('meta_key', '_itemora_precio');
        $query->set('orderby', 'meta_value_num');
    } elseif ('sku' === $orderby) {
        $query->set('meta_key', '_itemora_sku');
        $query->set('orderby', 'meta_value');
    } elseif ('stock' === $orderby) {
        $query->set('meta_key', '_itemora_stock');
        $query->set('orderby', 'meta_value_num');
    }
}
add_action('pre_get_posts', 'itemora_producto_orderby');

/**
 * Add filter dropdowns to the products list
 */
function itemora_add_producto_filters() {
    global $typenow;
    
    if ('itemora_producto' === $typenow) {
        // Filter by tipo_producto taxonomy
        $tipo_producto_taxonomy = 'tipo_producto';
        $selected_tipo = isset($_GET[$tipo_producto_taxonomy]) ? $_GET[$tipo_producto_taxonomy] : '';
        $tipo_args = array(
            'show_option_all' => __('Todos los tipos', 'itemora'),
            'taxonomy' => $tipo_producto_taxonomy,
            'name' => $tipo_producto_taxonomy,
            'orderby' => 'name',
            'selected' => $selected_tipo,
            'hierarchical' => true,
            'show_count' => true,
            'hide_empty' => false,
        );
        wp_dropdown_categories($tipo_args);
        
        // Filter by sucursal taxonomy if enabled
        if (get_option('itemora_activar_sucursal') === 'yes') {
            $sucursal_taxonomy = 'sucursal';
            $selected_sucursal = isset($_GET[$sucursal_taxonomy]) ? $_GET[$sucursal_taxonomy] : '';
            $sucursal_args = array(
                'show_option_all' => __('Todas las sucursales', 'itemora'),
                'taxonomy' => $sucursal_taxonomy,
                'name' => $sucursal_taxonomy,
                'orderby' => 'name',
                'selected' => $selected_sucursal,
                'hierarchical' => true,
                'show_count' => true,
                'hide_empty' => false,
            );
            wp_dropdown_categories($sucursal_args);
        }
    }
}
add_action('restrict_manage_posts', 'itemora_add_producto_filters');

/**
 * Modify the query for taxonomy filters
 *
 * @param WP_Query $query The WordPress query object
 */
function itemora_producto_filter_query($query) {
    global $pagenow, $typenow;
    
    if ('edit.php' !== $pagenow || 'itemora_producto' !== $typenow || !is_admin()) {
        return;
    }
    
    // Convert tipo_producto term ID to taxonomy term in query
    if (isset($_GET['tipo_producto']) && $_GET['tipo_producto'] > 0) {
        $term = get_term_by('id', $_GET['tipo_producto'], 'tipo_producto');
        $query->query_vars['tax_query'][] = array(
            'taxonomy' => 'tipo_producto',
            'field' => 'slug',
            'terms' => $term->slug,
        );
    }
    
    // Convert sucursal term ID to taxonomy term in query
    if (isset($_GET['sucursal']) && $_GET['sucursal'] > 0) {
        $term = get_term_by('id', $_GET['sucursal'], 'sucursal');
        $query->query_vars['tax_query'][] = array(
            'taxonomy' => 'sucursal',
            'field' => 'slug',
            'terms' => $term->slug,
        );
    }
}
add_action('pre_get_posts', 'itemora_producto_filter_query');

/**
 * Add custom meta boxes for product details
 */
function itemora_add_producto_meta_boxes() {
    add_meta_box(
        'itemora_producto_precio',
        __('Precio y Detalles', 'itemora'),
        'itemora_producto_precio_callback',
        'itemora_producto',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'itemora_add_producto_meta_boxes');

/**
 * Callback function for the precio meta box
 *
 * @param WP_Post $post Current post object
 */
function itemora_producto_precio_callback($post) {
    // Add nonce for security
    wp_nonce_field('itemora_save_producto_meta', 'itemora_producto_meta_nonce');
    
    // Get current values
    $precio = get_post_meta($post->ID, '_itemora_precio', true);
    $sku = get_post_meta($post->ID, '_itemora_sku', true);
    $stock = get_post_meta($post->ID, '_itemora_stock', true);
    
    // Output fields
    ?>
    <div class="itemora-meta-box">
        <div class="itemora-field-row">
            <label for="itemora_precio"><?php _e('Precio:', 'itemora'); ?></label>
            <input type="text" id="itemora_precio" name="itemora_precio" value="<?php echo esc_attr($precio); ?>">
        </div>
        
        <div class="itemora-field-row">
            <label for="itemora_sku"><?php _e('SKU:', 'itemora'); ?></label>
            <input type="text" id="itemora_sku" name="itemora_sku" value="<?php echo esc_attr($sku); ?>">
        </div>
        
        <div class="itemora-field-row">
            <label for="itemora_stock"><?php _e('Stock:', 'itemora'); ?></label>
            <input type="number" id="itemora_stock" name="itemora_stock" value="<?php echo esc_attr($stock); ?>" min="0">
        </div>
        
        <?php
        // Add detalles fields if enabled
        if (get_option('itemora_activar_detalles') === 'yes') {
            $detalle_1 = get_post_meta($post->ID, '_itemora_detalle_1', true);
            $detalle_2 = get_post_meta($post->ID, '_itemora_detalle_2', true);
            $detalle_3 = get_post_meta($post->ID, '_itemora_detalle_3', true);
            
            $label_1 = get_option('itemora_label_detalles_01', 'Detalle 1');
            $label_2 = get_option('itemora_label_detalles_02', 'Detalle 2');
            $label_3 = get_option('itemora_label_detalles_03', 'Detalle 3');
            ?>
            <hr>
            <h4><?php _e('Detalles', 'itemora'); ?></h4>
            
            <div class="itemora-field-row">
                <label for="itemora_detalle_1"><?php echo esc_html($label_1); ?>:</label>
                <input type="text" id="itemora_detalle_1" name="itemora_detalle_1" value="<?php echo esc_attr($detalle_1); ?>">
            </div>
            
            <div class="itemora-field-row">
                <label for="itemora_detalle_2"><?php echo esc_html($label_2); ?>:</label>
                <input type="text" id="itemora_detalle_2" name="itemora_detalle_2" value="<?php echo esc_attr($detalle_2); ?>">
            </div>
            
            <div class="itemora-field-row">
                <label for="itemora_detalle_3"><?php echo esc_html($label_3); ?>:</label>
                <input type="text" id="itemora_detalle_3" name="itemora_detalle_3" value="<?php echo esc_attr($detalle_3); ?>">
            </div>
            <?php
        }
        
        // Add extras fields if enabled
        if (get_option('itemora_activar_extras') === 'yes') {
            $extra_1 = get_post_meta($post->ID, '_itemora_extra_1', true);
            $extra_2 = get_post_meta($post->ID, '_itemora_extra_2', true);
            $extra_3 = get_post_meta($post->ID, '_itemora_extra_3', true);
            
            $label_1 = get_option('itemora_label_extra_01', 'Extra 1');
            $label_2 = get_option('itemora_label_extra_02', 'Extra 2');
            $label_3 = get_option('itemora_label_extra_03', 'Extra 3');
            ?>
            <hr>
            <h4><?php _e('Extras', 'itemora'); ?></h4>
            
            <div class="itemora-field-row">
                <label for="itemora_extra_1"><?php echo esc_html($label_1); ?>:</label>
                <input type="text" id="itemora_extra_1" name="itemora_extra_1" value="<?php echo esc_attr($extra_1); ?>">
            </div>
            
            <div class="itemora-field-row">
                <label for="itemora_extra_2"><?php echo esc_html($label_2); ?>:</label>
                <input type="text" id="itemora_extra_2" name="itemora_extra_2" value="<?php echo esc_attr($extra_2); ?>">
            </div>
            
            <div class="itemora-field-row">
                <label for="itemora_extra_3"><?php echo esc_html($label_3); ?>:</label>
                <input type="text" id="itemora_extra_3" name="itemora_extra_3" value="<?php echo esc_attr($extra_3); ?>">
            </div>
            <?php
        }
        ?>
    </div>
    <?php
}

/**
 * Save product meta data
 *
 * @param int $post_id Post ID
 */
function itemora_save_producto_meta($post_id) {
    // Check if nonce is set
    if (!isset($_POST['itemora_producto_meta_nonce'])) {
        return;
    }
    
    // Verify nonce
    if (!wp_verify_nonce($_POST['itemora_producto_meta_nonce'], 'itemora_save_producto_meta')) {
        return;
    }
    
    // If this is an autosave, don't do anything
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Check user permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Save precio
    if (isset($_POST['itemora_precio'])) {
        update_post_meta($post_id, '_itemora_precio', sanitize_text_field($_POST['itemora_precio']));
    }
    
    // Save SKU
    if (isset($_POST['itemora_sku'])) {
        update_post_meta($post_id, '_itemora_sku', sanitize_text_field($_POST['itemora_sku']));
    }
    
    // Save stock
    if (isset($_POST['itemora_stock'])) {
        update_post_meta($post_id, '_itemora_stock', absint($_POST['itemora_stock']));
    }
    
    // Save detalles if enabled
    if (get_option('itemora_activar_detalles') === 'yes') {
        if (isset($_POST['itemora_detalle_1'])) {
            update_post_meta($post_id, '_itemora_detalle_1', sanitize_text_field($_POST['itemora_detalle_1']));
        }
        if (isset($_POST['itemora_detalle_2'])) {
            update_post_meta($post_id, '_itemora_detalle_2', sanitize_text_field($_POST['itemora_detalle_2']));
        }
        if (isset($_POST['itemora_detalle_3'])) {
            update_post_meta($post_id, '_itemora_detalle_3', sanitize_text_field($_POST['itemora_detalle_3']));
        }
    }
    
    // Save extras if enabled
    if (get_option('itemora_activar_extras') === 'yes') {
        if (isset($_POST['itemora_extra_1'])) {
            update_post_meta($post_id, '_itemora_extra_1', sanitize_text_field($_POST['itemora_extra_1']));
        }
        if (isset($_POST['itemora_extra_2'])) {
            update_post_meta($post_id, '_itemora_extra_2', sanitize_text_field($_POST['itemora_extra_2']));
        }
        if (isset($_POST['itemora_extra_3'])) {
            update_post_meta($post_id, '_itemora_extra_3', sanitize_text_field($_POST['itemora_extra_3']));
        }
    }
}
add_action('save_post_itemora_producto', 'itemora_save_producto_meta');