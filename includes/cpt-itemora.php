<?php
/**
 * Custom Post Type para Itemora
 *
 * Registra y gestiona el CPT de productos para Itemora
 *
 * @package Itemora
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Registra el Custom Post Type para productos
 */
function itemora_register_cpt() {
    $labels = array(
        'name'               => _x('Productos', 'post type general name', 'itemora'),
        'singular_name'      => _x('Producto', 'post type singular name', 'itemora'),
        'menu_name'          => _x('Itemora', 'admin menu', 'itemora'),
        'name_admin_bar'     => _x('Producto', 'add new on admin bar', 'itemora'),
        'add_new'            => _x('Añadir Nuevo', 'producto', 'itemora'),
        'add_new_item'       => __('Añadir Nuevo Producto', 'itemora'),
        'new_item'           => __('Nuevo Producto', 'itemora'),
        'edit_item'          => __('Editar Producto', 'itemora'),
        'view_item'          => __('Ver Producto', 'itemora'),
        'all_items'          => __('Todos los Productos', 'itemora'),
        'search_items'       => __('Buscar Productos', 'itemora'),
        'parent_item_colon'  => __('Productos Padre:', 'itemora'),
        'not_found'          => __('No se encontraron productos.', 'itemora'),
        'not_found_in_trash' => __('No se encontraron productos en la papelera.', 'itemora')
    );

    $args = array(
        'labels'              => $labels,
        'public'              => true,
        'publicly_queryable'  => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'query_var'           => true,
        'rewrite'             => array('slug' => 'productos'),
        'capability_type'     => 'post',
        'has_archive'         => true,
        'hierarchical'        => false,
        'menu_position'       => 5,
        'menu_icon'           => 'dashicons-products',
        'supports'            => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        'show_in_rest'        => true,
        'taxonomies'          => array('categoria_producto', 'tipo_producto', 'sucursal'),
    );

    register_post_type('itemora_producto', $args);
}
add_action('init', 'itemora_register_cpt', 10);

/**
 * Añade metaboxes para los detalles del producto
 */
function itemora_add_product_metaboxes() {
    add_meta_box(
        'itemora_product_details',
        __('Detalles del Producto', 'itemora'),
        'itemora_product_details_callback',
        'itemora_producto',
        'normal',
        'high'
    );
    
    // Metabox para precio y stock solo si está activado
    if (get_option('itemora_activar_detalles', 'yes') === 'yes') {
        add_meta_box(
            'itemora_product_price',
            __('Precio y Stock', 'itemora'),
            'itemora_product_price_callback',
            'itemora_producto',
            'side',
            'default'
        );
    }
    
    // Metabox para características adicionales solo si está activado
    if (get_option('itemora_activar_extras', 'no') === 'yes') {
        add_meta_box(
            'itemora_product_extras',
            __('Características Adicionales', 'itemora'),
            'itemora_product_extras_callback',
            'itemora_producto',
            'normal',
            'default'
        );
    }
}
add_action('add_meta_boxes', 'itemora_add_product_metaboxes');

/**
 * Callback para el metabox de detalles del producto
 */
function itemora_product_details_callback($post) {
    // Añadir nonce para verificación
    wp_nonce_field('itemora_product_details_nonce', 'itemora_product_details_nonce');
    
    // Obtener valores actuales
    $sku = get_post_meta($post->ID, '_itemora_sku', true);
    $codigo = get_post_meta($post->ID, '_itemora_codigo', true);
    $marca = get_post_meta($post->ID, '_itemora_marca', true);
    $modelo = get_post_meta($post->ID, '_itemora_modelo', true);
    
    // Formulario para los campos
    ?>
    <div class="itemora-metabox">
        <div class="itemora-field">
            <label for="itemora_sku"><?php _e('SKU:', 'itemora'); ?></label>
            <input type="text" id="itemora_sku" name="itemora_sku" value="<?php echo esc_attr($sku); ?>" />
        </div>
        
        <div class="itemora-field">
            <label for="itemora_codigo"><?php _e('Código:', 'itemora'); ?></label>
            <input type="text" id="itemora_codigo" name="itemora_codigo" value="<?php echo esc_attr($codigo); ?>" />
        </div>
        
        <div class="itemora-field">
            <label for="itemora_marca"><?php _e('Marca:', 'itemora'); ?></label>
            <input type="text" id="itemora_marca" name="itemora_marca" value="<?php echo esc_attr($marca); ?>" />
        </div>
        
        <div class="itemora-field">
            <label for="itemora_modelo"><?php _e('Modelo:', 'itemora'); ?></label>
            <input type="text" id="itemora_modelo" name="itemora_modelo" value="<?php echo esc_attr($modelo); ?>" />
        </div>
    </div>
    <?php
}

/**
 * Callback para el metabox de precio y stock
 */
function itemora_product_price_callback($post) {
    // Añadir nonce para verificación
    wp_nonce_field('itemora_product_price_nonce', 'itemora_product_price_nonce');
    
    // Obtener valores actuales
    $precio = get_post_meta($post->ID, '_itemora_precio', true);
    $precio_oferta = get_post_meta($post->ID, '_itemora_precio_oferta', true);
    $stock = get_post_meta($post->ID, '_itemora_stock', true);
    $stock_status = get_post_meta($post->ID, '_itemora_stock_status', true);
    
    // Valores por defecto
    if (empty($stock_status)) {
        $stock_status = 'instock';
    }
    
    // Formulario para los campos
    ?>
    <div class="itemora-metabox">
        <div class="itemora-field">
            <label for="itemora_precio"><?php _e('Precio Regular ($):', 'itemora'); ?></label>
            <input type="text" id="itemora_precio" name="itemora_precio" value="<?php echo esc_attr($precio); ?>" />
        </div>
        
        <div class="itemora-field">
            <label for="itemora_precio_oferta"><?php _e('Precio Oferta ($):', 'itemora'); ?></label>
            <input type="text" id="itemora_precio_oferta" name="itemora_precio_oferta" value="<?php echo esc_attr($precio_oferta); ?>" />
            <p class="description"><?php _e('Dejar en blanco si no hay oferta', 'itemora'); ?></p>
        </div>
        
        <div class="itemora-field">
            <label for="itemora_stock"><?php _e('Cantidad en Stock:', 'itemora'); ?></label>
            <input type="number" id="itemora_stock" name="itemora_stock" value="<?php echo esc_attr($stock); ?>" min="0" />
        </div>
        
        <div class="itemora-field">
            <label><?php _e('Estado del Stock:', 'itemora'); ?></label>
            <select name="itemora_stock_status" id="itemora_stock_status">
                <option value="instock" <?php selected($stock_status, 'instock'); ?>><?php _e('En Stock', 'itemora'); ?></option>
                <option value="outofstock" <?php selected($stock_status, 'outofstock'); ?>><?php _e('Agotado', 'itemora'); ?></option>
                <option value="onbackorder" <?php selected($stock_status, 'onbackorder'); ?>><?php _e('Por Encargo', 'itemora'); ?></option>
            </select>
        </div>
    </div>
    <?php
}

/**
 * Callback para el metabox de características adicionales
 */
function itemora_product_extras_callback($post) {
    // Añadir nonce para verificación
    wp_nonce_field('itemora_product_extras_nonce', 'itemora_product_extras_nonce');
    
    // Obtener valores actuales
    $dimensiones = get_post_meta($post->ID, '_itemora_dimensiones', true);
    $peso = get_post_meta($post->ID, '_itemora_peso', true);
    $color = get_post_meta($post->ID, '_itemora_color', true);
    $material = get_post_meta($post->ID, '_itemora_material', true);
    $garantia = get_post_meta($post->ID, '_itemora_garantia', true);
    $caracteristicas = get_post_meta($post->ID, '_itemora_caracteristicas', true);
    
    // Formulario para los campos
    ?>
    <div class="itemora-metabox">
        <div class="itemora-field">
            <label for="itemora_dimensiones"><?php _e('Dimensiones:', 'itemora'); ?></label>
            <input type="text" id="itemora_dimensiones" name="itemora_dimensiones" value="<?php echo esc_attr($dimensiones); ?>" />
            <p class="description"><?php _e('Formato: Alto x Ancho x Profundidad (cm)', 'itemora'); ?></p>
        </div>
        
        <div class="itemora-field">
            <label for="itemora_peso"><?php _e('Peso (kg):', 'itemora'); ?></label>
            <input type="text" id="itemora_peso" name="itemora_peso" value="<?php echo esc_attr($peso); ?>" />
        </div>
        
        <div class="itemora-field">
            <label for="itemora_color"><?php _e('Color:', 'itemora'); ?></label>
            <input type="text" id="itemora_color" name="itemora_color" value="<?php echo esc_attr($color); ?>" />
        </div>
        
        <div class="itemora-field">
            <label for="itemora_material"><?php _e('Material:', 'itemora'); ?></label>
            <input type="text" id="itemora_material" name="itemora_material" value="<?php echo esc_attr($material); ?>" />
        </div>
        
        <div class="itemora-field">
            <label for="itemora_garantia"><?php _e('Garantía:', 'itemora'); ?></label>
            <input type="text" id="itemora_garantia" name="itemora_garantia" value="<?php echo esc_attr($garantia); ?>" />
            <p class="description"><?php _e('Ejemplo: 1 año, 6 meses, etc.', 'itemora'); ?></p>
        </div>
        
        <div class="itemora-field">
            <label for="itemora_caracteristicas"><?php _e('Características Adicionales:', 'itemora'); ?></label>
            <textarea id="itemora_caracteristicas" name="itemora_caracteristicas" rows="5"><?php echo esc_textarea($caracteristicas); ?></textarea>
            <p class="description"><?php _e('Ingresa una característica por línea', 'itemora'); ?></p>
        </div>
    </div>
    <?php
}

/**
 * Guarda los metadatos del producto
 */
function itemora_save_product_meta($post_id) {
    // Verificar si es autoguardado
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Verificar el tipo de post
    if (get_post_type($post_id) !== 'itemora_producto') {
        return;
    }
    
    // Verificar permisos
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Guardar detalles del producto
    if (isset($_POST['itemora_product_details_nonce']) && wp_verify_nonce($_POST['itemora_product_details_nonce'], 'itemora_product_details_nonce')) {
        if (isset($_POST['itemora_sku'])) {
            update_post_meta($post_id, '_itemora_sku', sanitize_text_field($_POST['itemora_sku']));
        }
        
        if (isset($_POST['itemora_codigo'])) {
            update_post_meta($post_id, '_itemora_codigo', sanitize_text_field($_POST['itemora_codigo']));
        }
        
        if (isset($_POST['itemora_marca'])) {
            update_post_meta($post_id, '_itemora_marca', sanitize_text_field($_POST['itemora_marca']));
        }
        
        if (isset($_POST['itemora_modelo'])) {
            update_post_meta($post_id, '_itemora_modelo', sanitize_text_field($_POST['itemora_modelo']));
        }
    }
    
    // Guardar precio y stock
    if (isset($_POST['itemora_product_price_nonce']) && wp_verify_nonce($_POST['itemora_product_price_nonce'], 'itemora_product_price_nonce')) {
        if (isset($_POST['itemora_precio'])) {
            update_post_meta($post_id, '_itemora_precio', sanitize_text_field($_POST['itemora_precio']));
        }
        
        if (isset($_POST['itemora_precio_oferta'])) {
            update_post_meta($post_id, '_itemora_precio_oferta', sanitize_text_field($_POST['itemora_precio_oferta']));
        }
        
        if (isset($_POST['itemora_stock'])) {
            update_post_meta($post_id, '_itemora_stock', absint($_POST['itemora_stock']));
        }
        
        if (isset($_POST['itemora_stock_status'])) {
            update_post_meta($post_id, '_itemora_stock_status', sanitize_text_field($_POST['itemora_stock_status']));
        }
    }
    
    // Guardar características adicionales
    if (isset($_POST['itemora_product_extras_nonce']) && wp_verify_nonce($_POST['itemora_product_extras_nonce'], 'itemora_product_extras_nonce')) {
        if (isset($_POST['itemora_dimensiones'])) {
            update_post_meta($post_id, '_itemora_dimensiones', sanitize_text_field($_POST['itemora_dimensiones']));
        }
        
        if (isset($_POST['itemora_peso'])) {
            update_post_meta($post_id, '_itemora_peso', sanitize_text_field($_POST['itemora_peso']));
        }
        
        if (isset($_POST['itemora_color'])) {
            update_post_meta($post_id, '_itemora_color', sanitize_text_field($_POST['itemora_color']));
        }
        
        if (isset($_POST['itemora_material'])) {
            update_post_meta($post_id, '_itemora_material', sanitize_text_field($_POST['itemora_material']));
        }
        
        if (isset($_POST['itemora_garantia'])) {
            update_post_meta($post_id, '_itemora_garantia', sanitize_text_field($_POST['itemora_garantia']));
        }
        
        if (isset($_POST['itemora_caracteristicas'])) {
            update_post_meta($post_id, '_itemora_caracteristicas', sanitize_textarea_field($_POST['itemora_caracteristicas']));
        }
    }
}
add_action('save_post_itemora_producto', 'itemora_save_product_meta');

/**
 * Añade columnas personalizadas a la lista de productos
 *
 * @param array $columns Columnas existentes
 * @return array Columnas modificadas
 */
function itemora_add_product_columns($columns) {
    $new_columns = array();
    
    foreach ($columns as $key => $value) {
        if ($key === 'title') {
            $new_columns[$key] = $value;
            $new_columns['sku'] = __('SKU', 'itemora');
            $new_columns['precio'] = __('Precio', 'itemora');
            $new_columns['stock'] = __('Stock', 'itemora');
        } elseif ($key === 'date') {
            $new_columns['marca'] = __('Marca', 'itemora');
            $new_columns[$key] = $value;
        } else {
            $new_columns[$key] = $value;
        }
    }
    
    return $new_columns;
}
add_filter('manage_itemora_producto_posts_columns', 'itemora_add_product_columns');

/**
 * Muestra el contenido de las columnas personalizadas
 *
 * @param string $column Nombre de la columna
 * @param int $post_id ID del post
 */
function itemora_display_product_columns($column, $post_id) {
    switch ($column) {
        case 'sku':
            $sku = get_post_meta($post_id, '_itemora_sku', true);
            echo !empty($sku) ? esc_html($sku) : '—';
            break;
            
        case 'precio':
            $precio = get_post_meta($post_id, '_itemora_precio', true);
            $precio_oferta = get_post_meta($post_id, '_itemora_precio_oferta', true);
            
            if (!empty($precio)) {
                echo '<span class="itemora-precio">$' . esc_html($precio) . '</span>';
                
                if (!empty($precio_oferta)) {
                    echo ' <span class="itemora-precio-oferta">$' . esc_html($precio_oferta) . '</span>';
                }
            } else {
                echo '—';
            }
            break;
            
        case 'stock':
            $stock = get_post_meta($post_id, '_itemora_stock', true);
            $stock_status = get_post_meta($post_id, '_itemora_stock_status', true);
            
            if ($stock_status === 'instock') {
                echo '<span class="itemora-stock-status instock">' . __('En Stock', 'itemora') . '</span>';
                if (!empty($stock)) {
                    echo ' (' . esc_html($stock) . ')';
                }
            } elseif ($stock_status === 'outofstock') {
                echo '<span class="itemora-stock-status outofstock">' . __('Agotado', 'itemora') . '</span>';
            } elseif ($stock_status === 'onbackorder') {
                echo '<span class="itemora-stock-status onbackorder">' . __('Por Encargo', 'itemora') . '</span>';
            } else {
                echo '—';
            }
            break;
            
        case 'marca':
            $marca = get_post_meta($post_id, '_itemora_marca', true);
            $modelo = get_post_meta($post_id, '_itemora_modelo', true);
            
            if (!empty($marca)) {
                echo esc_html($marca);
                
                if (!empty($modelo)) {
                    echo ' / ' . esc_html($modelo);
                }
            } else {
                echo '—';
            }
            break;
    }
}
add_action('manage_itemora_producto_posts_custom_column', 'itemora_display_product_columns', 10, 2);

/**
 * Hace que las columnas personalizadas sean ordenables
 *
 * @param array $columns Columnas existentes
 * @return array Columnas modificadas
 */
function itemora_sortable_product_columns($columns) {
    $columns['sku'] = 'sku';
    $columns['precio'] = 'precio';
    $columns['stock'] = 'stock';
    $columns['marca'] = 'marca';
    
    return $columns;
}
add_filter('manage_edit-itemora_producto_sortable_columns', 'itemora_sortable_product_columns');

/**
 * Gestiona la ordenación de las columnas personalizadas
 *
 * @param WP_Query $query Objeto de consulta
 */
function itemora_product_columns_orderby($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }
    
    if ($query->get('post_type') !== 'itemora_producto') {
        return;
    }
    
    $orderby = $query->get('orderby');
    
    switch ($orderby) {
        case 'sku':
            $query->set('meta_key', '_itemora_sku');
            $query->set('orderby', 'meta_value');
            break;
            
        case 'precio':
            $query->set('meta_key', '_itemora_precio');
            $query->set('orderby', 'meta_value_num');
            break;
            
        case 'stock':
            $query->set('meta_key', '_itemora_stock');
            $query->set('orderby', 'meta_value_num');
            break;
            
        case 'marca':
            $query->set('meta_key', '_itemora_marca');
            $query->set('orderby', 'meta_value');
            break;
    }
}
add_action('pre_get_posts', 'itemora_product_columns_orderby');

/**
 * Registra los scripts y estilos para el CPT
 */
function itemora_cpt_admin_scripts($hook) {
    global $post_type;
    
    if (($hook === 'post.php' || $hook === 'post-new.php') && $post_type === 'itemora_producto') {
        wp_enqueue_style('itemora-admin-style');
        wp_enqueue_script('itemora-admin-script');
    }
}
add_action('admin_enqueue_scripts', 'itemora_cpt_admin_scripts');