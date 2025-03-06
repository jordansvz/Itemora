<?php
/**
 * Taxonomías para Itemora
 *
 * Registra y gestiona las taxonomías para los productos de Itemora
 *
 * @package Itemora
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Registra las taxonomías para los productos
 */
function itemora_register_taxonomies() {
    // Taxonomía Tipo de Producto
    $tipo_labels = array(
        'name'                       => _x('Tipos de Producto', 'taxonomy general name', 'itemora'),
        'singular_name'              => _x('Tipo de Producto', 'taxonomy singular name', 'itemora'),
        'search_items'               => __('Buscar Tipos', 'itemora'),
        'popular_items'              => __('Tipos Populares', 'itemora'),
        'all_items'                  => __('Todos los Tipos', 'itemora'),
        'parent_item'                => __('Tipo Padre', 'itemora'),
        'parent_item_colon'          => __('Tipo Padre:', 'itemora'),
        'edit_item'                  => __('Editar Tipo', 'itemora'),
        'update_item'                => __('Actualizar Tipo', 'itemora'),
        'add_new_item'               => __('Añadir Nuevo Tipo', 'itemora'),
        'new_item_name'              => __('Nombre del Nuevo Tipo', 'itemora'),
        'separate_items_with_commas' => __('Separar tipos con comas', 'itemora'),
        'add_or_remove_items'        => __('Añadir o eliminar tipos', 'itemora'),
        'choose_from_most_used'      => __('Elegir de los más usados', 'itemora'),
        'menu_name'                  => __('Tipos de Producto', 'itemora'),
    );

    $tipo_args = array(
        'labels'            => $tipo_labels,
        'hierarchical'      => true,
        'public'            => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_nav_menus' => true,
        'show_tagcloud'     => false,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'tipo-producto'),
        'show_in_rest'      => true,
        'show_in_menu'      => true,
    );

    register_taxonomy('tipo_producto', 'itemora_producto', $tipo_args);

    // Taxonomía Categoría de Producto
    $categoria_labels = array(
        'name'                       => _x('Categorías', 'taxonomy general name', 'itemora'),
        'singular_name'              => _x('Categoría', 'taxonomy singular name', 'itemora'),
        'search_items'               => __('Buscar Categorías', 'itemora'),
        'popular_items'              => __('Categorías Populares', 'itemora'),
        'all_items'                  => __('Todas las Categorías', 'itemora'),
        'parent_item'                => __('Categoría Padre', 'itemora'),
        'parent_item_colon'          => __('Categoría Padre:', 'itemora'),
        'edit_item'                  => __('Editar Categoría', 'itemora'),
        'update_item'                => __('Actualizar Categoría', 'itemora'),
        'add_new_item'               => __('Añadir Nueva Categoría', 'itemora'),
        'new_item_name'              => __('Nombre de la Nueva Categoría', 'itemora'),
        'separate_items_with_commas' => __('Separar categorías con comas', 'itemora'),
        'add_or_remove_items'        => __('Añadir o eliminar categorías', 'itemora'),
        'choose_from_most_used'      => __('Elegir de las más usadas', 'itemora'),
        'menu_name'                  => __('Categorías', 'itemora'),
    );

    $categoria_args = array(
        'labels'            => $categoria_labels,
        'hierarchical'      => true,
        'public'            => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_nav_menus' => true,
        'show_tagcloud'     => false,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'categoria-producto'),
        'show_in_rest'      => true,
        'show_in_menu'      => true,
    );

    register_taxonomy('categoria_producto', 'itemora_producto', $categoria_args);

    // Taxonomía Sucursal (si está activada)
    if (get_option('itemora_activar_sucursal', 'yes') === 'yes') {
        $sucursal_labels = array(
            'name'                       => _x('Sucursales', 'taxonomy general name', 'itemora'),
            'singular_name'              => _x('Sucursal', 'taxonomy singular name', 'itemora'),
            'search_items'               => __('Buscar Sucursales', 'itemora'),
            'popular_items'              => __('Sucursales Populares', 'itemora'),
            'all_items'                  => __('Todas las Sucursales', 'itemora'),
            'parent_item'                => __('Sucursal Padre', 'itemora'),
            'parent_item_colon'          => __('Sucursal Padre:', 'itemora'),
            'edit_item'                  => __('Editar Sucursal', 'itemora'),
            'update_item'                => __('Actualizar Sucursal', 'itemora'),
            'add_new_item'               => __('Añadir Nueva Sucursal', 'itemora'),
            'new_item_name'              => __('Nombre de la Nueva Sucursal', 'itemora'),
            'separate_items_with_commas' => __('Separar sucursales con comas', 'itemora'),
            'add_or_remove_items'        => __('Añadir o eliminar sucursales', 'itemora'),
            'choose_from_most_used'      => __('Elegir de las más usadas', 'itemora'),
            'menu_name'                  => __('Sucursales', 'itemora'),
        );

        $sucursal_args = array(
            'labels'            => $sucursal_labels,
            'hierarchical'      => true,
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'show_tagcloud'     => false,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'sucursal'),
            'show_in_rest'      => true,
            'show_in_menu'      => true,
        );

        register_taxonomy('sucursal', 'itemora_producto', $sucursal_args);
    }
}
add_action('init', 'itemora_register_taxonomies', 11); // Prioridad 11 para asegurar que se ejecute después del CPT

/**
 * Añade columnas de taxonomías a la lista de productos
 *
 * @param array $columns Columnas existentes
 * @return array Columnas modificadas
 */
function itemora_add_taxonomy_columns($columns) {
    $new_columns = array();
    
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        
        // Añadir columnas después del título
        if ($key === 'title') {
            $new_columns['categoria_producto'] = __('Categoría', 'itemora');
            $new_columns['tipo_producto'] = __('Tipo', 'itemora');
            
            if (get_option('itemora_activar_sucursal', 'yes') === 'yes') {
                $new_columns['sucursal'] = __('Sucursal', 'itemora');
            }
        }
    }
    
    return $new_columns;
}
add_filter('manage_itemora_producto_posts_columns', 'itemora_add_taxonomy_columns');

/**
 * Muestra el contenido de las columnas de taxonomías
 *
 * @param string $column Nombre de la columna
 * @param int $post_id ID del post
 */
function itemora_display_taxonomy_columns($column, $post_id) {
    switch ($column) {
        case 'categoria_producto':
            $terms = get_the_terms($post_id, 'categoria_producto');
            if (!empty($terms) && !is_wp_error($terms)) {
                $term_links = array();
                foreach ($terms as $term) {
                    $term_links[] = sprintf(
                        '<a href="%s">%s</a>',
                        esc_url(add_query_arg(array('post_type' => 'itemora_producto', 'categoria_producto' => $term->slug), 'edit.php')),
                        esc_html($term->name)
                    );
                }
                echo implode(', ', $term_links);
            } else {
                echo '—';
            }
            break;
            
        case 'tipo_producto':
            $terms = get_the_terms($post_id, 'tipo_producto');
            if (!empty($terms) && !is_wp_error($terms)) {
                $term_links = array();
                foreach ($terms as $term) {
                    $term_links[] = sprintf(
                        '<a href="%s">%s</a>',
                        esc_url(add_query_arg(array('post_type' => 'itemora_producto', 'tipo_producto' => $term->slug), 'edit.php')),
                        esc_html($term->name)
                    );
                }
                echo implode(', ', $term_links);
            } else {
                echo '—';
            }
            break;
            
        case 'sucursal':
            if (get_option('itemora_activar_sucursal', 'yes') === 'yes') {
                $terms = get_the_terms($post_id, 'sucursal');
                if (!empty($terms) && !is_wp_error($terms)) {
                    $term_links = array();
                    foreach ($terms as $term) {
                        $term_links[] = sprintf(
                            '<a href="%s">%s</a>',
                            esc_url(add_query_arg(array('post_type' => 'itemora_producto', 'sucursal' => $term->slug), 'edit.php')),
                            esc_html($term->name)
                        );
                    }
                    echo implode(', ', $term_links);
                } else {
                    echo '—';
                }
            }
            break;
    }
}
add_action('manage_itemora_producto_posts_custom_column', 'itemora_display_taxonomy_columns', 10, 2);

/**
 * Añade filtros de taxonomías en la lista de productos
 */
function itemora_add_taxonomy_filters() {
    global $typenow;
    
    if ($typenow === 'itemora_producto') {
        // Filtro para Categoría
        $categoria_taxonomy = 'categoria_producto';
        $categoria_terms = get_terms(array(
            'taxonomy' => $categoria_taxonomy,
            'hide_empty' => false,
        ));
        
        if (!empty($categoria_terms) && !is_wp_error($categoria_terms)) {
            $selected_categoria = isset($_GET[$categoria_taxonomy]) ? $_GET[$categoria_taxonomy] : '';
            echo '<select name="' . esc_attr($categoria_taxonomy) . '" id="' . esc_attr($categoria_taxonomy) . '" class="postform">';
            echo '<option value="">' . esc_html__('Todas las categorías', 'itemora') . '</option>';
            
            foreach ($categoria_terms as $term) {
                printf(
                    '<option value="%s" %s>%s (%d)</option>',
                    esc_attr($term->slug),
                    selected($selected_categoria, $term->slug, false),
                    esc_html($term->name),
                    esc_html($term->count)
                );
            }
            
            echo '</select>';
        }
        
        // Filtro para Tipo de Producto
        $tipo_taxonomy = 'tipo_producto';
        $tipo_terms = get_terms(array(
            'taxonomy' => $tipo_taxonomy,
            'hide_empty' => false,
        ));
        
        if (!empty($tipo_terms) && !is_wp_error($tipo_terms)) {
            $selected_tipo = isset($_GET[$tipo_taxonomy]) ? $_GET[$tipo_taxonomy] : '';
            echo '<select name="' . esc_attr($tipo_taxonomy) . '" id="' . esc_attr($tipo_taxonomy) . '" class="postform">';
            echo '<option value="">' . esc_html__('Todos los tipos', 'itemora') . '</option>';
            
            foreach ($tipo_terms as $term) {
                printf(
                    '<option value="%s" %s>%s (%d)</option>',
                    esc_attr($term->slug),
                    selected($selected_tipo, $term->slug, false),
                    esc_html($term->name),
                    esc_html($term->count)
                );
            }
            
            echo '</select>';
        }
        
        // Filtro para Sucursal (si está activada)
        if (get_option('itemora_activar_sucursal', 'yes') === 'yes') {
            $sucursal_taxonomy = 'sucursal';
            $sucursal_terms = get_terms(array(
                'taxonomy' => $sucursal_taxonomy,
                'hide_empty' => false,
            ));
            
            if (!empty($sucursal_terms) && !is_wp_error($sucursal_terms)) {
                $selected_sucursal = isset($_GET[$sucursal_taxonomy]) ? $_GET[$sucursal_taxonomy] : '';
                echo '<select name="' . esc_attr($sucursal_taxonomy) . '" id="' . esc_attr($sucursal_taxonomy) . '" class="postform">';
                echo '<option value="">' . esc_html__('Todas las sucursales', 'itemora') . '</option>';
                
                foreach ($sucursal_terms as $term) {
                    printf(
                        '<option value="%s" %s>%s (%d)</option>',
                        esc_attr($term->slug),
                        selected($selected_sucursal, $term->slug, false),
                        esc_html($term->name),
                        esc_html($term->count)
                    );
                }
                
                echo '</select>';
            }
        }
    }
}
add_action('restrict_manage_posts', 'itemora_add_taxonomy_filters');

/**
 * Registra términos predeterminados para las taxonomías
 */
function itemora_register_default_terms() {
    // Solo ejecutar en la activación del plugin
    if (!get_option('itemora_default_terms_created')) {
        // Términos predeterminados para Tipo de Producto
        $default_tipos = array(
            'Producto Físico' => 'Productos tangibles que requieren envío',
            'Producto Digital' => 'Productos descargables o de acceso digital',
            'Servicio' => 'Servicios ofrecidos por la empresa'
        );
        
        foreach ($default_tipos as $name => $description) {
            if (!term_exists($name, 'tipo_producto')) {
                wp_insert_term(
                    $name,
                    'tipo_producto',
                    array(
                        'description' => $description,
                        'slug' => sanitize_title($name)
                    )
                );
            }
        }
        
        // Términos predeterminados para Categoría de Producto
        $default_categorias = array(
            'Destacados' => 'Productos destacados',
            'Ofertas' => 'Productos en oferta',
            'Nuevos' => 'Productos recién añadidos'
        );
        
        foreach ($default_categorias as $name => $description) {
            if (!term_exists($name, 'categoria_producto')) {
                wp_insert_term(
                    $name,
                    'categoria_producto',
                    array(
                        'description' => $description,
                        'slug' => sanitize_title($name)
                    )
                );
            }
        }
        
        // Si las sucursales están activadas, crear algunas por defecto
        if (get_option('itemora_activar_sucursal', 'yes') === 'yes') {
            $default_sucursales = array(
                'Sucursal Principal' => 'Sede central de la empresa',
                'Sucursal Norte' => 'Ubicada en la zona norte',
                'Sucursal Sur' => 'Ubicada en la zona sur'
            );
            
            foreach ($default_sucursales as $name => $description) {
                if (!term_exists($name, 'sucursal')) {
                    $term_id = wp_insert_term(
                        $name,
                        'sucursal',
                        array(
                            'description' => $description,
                            'slug' => sanitize_title($name)
                        )
                    );
                    
                    // Añadir metadatos a las sucursales
                    if (!is_wp_error($term_id)) {
                        update_term_meta($term_id['term_id'], '_ubicacion', 'https://maps.google.com/');
                        update_term_meta($term_id['term_id'], '_telefono', '(123) 456-7890');
                    }
                }
            }
        }
        
        // Marcar que ya se han creado los términos predeterminados
        update_option('itemora_default_terms_created', true);
    }
}
add_action('admin_init', 'itemora_register_default_terms');

/**
 * Añade campos adicionales a la taxonomía "Sucursal"
 */
function itemora_agregar_campos_sucursal($term) {
    // Verificar si estamos editando o agregando un nuevo término
    $term_id = isset($term->term_id) ? $term->term_id : 0;

    // Obtener valores actuales de los campos adicionales
    $ubicacion = get_term_meta($term_id, '_ubicacion', true);
    $telefono = get_term_meta($term_id, '_telefono', true);
    $horario = get_term_meta($term_id, '_horario', true);

    // HTML para el formulario
    ?>
    <div class="form-field term-group">
        <label for="ubicacion"><?php _e('Ubicación (Google Maps)', 'itemora'); ?></label>
        <input type="text" name="ubicacion" id="ubicacion" value="<?php echo esc_attr($ubicacion); ?>">
        <p><?php _e('Ingresa la URL o coordenadas de Google Maps.', 'itemora'); ?></p>
    </div>
    <div class="form-field term-group">
        <label for="telefono"><?php _e('Teléfono', 'itemora'); ?></label>
        <input type="text" name="telefono" id="telefono" value="<?php echo esc_attr($telefono); ?>">
        <p><?php _e('Ingresa el número de teléfono de la sucursal.', 'itemora'); ?></p>
    </div>
    <div class="form-field term-group">
        <label for="horario"><?php _e('Horario de atención', 'itemora'); ?></label>
        <textarea name="horario" id="horario" rows="5"><?php echo esc_textarea($horario); ?></textarea>
        <p><?php _e('Ingresa el horario de atención de la sucursal.', 'itemora'); ?></p>
    </div>
    <?php
}
add_action('sucursal_add_form_fields', 'itemora_agregar_campos_sucursal');
add_action('sucursal_edit_form_fields', 'itemora_agregar_campos_sucursal');

/**
 * Guarda los campos adicionales de la taxonomía "Sucursal"
 */
function itemora_guardar_campos_sucursal($term_id) {
    if (isset($_POST['ubicacion'])) {
        update_term_meta($term_id, '_ubicacion', sanitize_text_field($_POST['ubicacion']));
    }
    
    if (isset($_POST['telefono'])) {
        update_term_meta($term_id, '_telefono', sanitize_text_field($_POST['telefono']));
    }
    
    if (isset($_POST['horario'])) {
        update_term_meta($term_id, '_horario', sanitize_textarea_field($_POST['horario']));
    }
}
add_action('created_sucursal', 'itemora_guardar_campos_sucursal');
add_action('edited_sucursal', 'itemora_guardar_campos_sucursal');

/**
 * Añade columnas personalizadas a la lista de términos de Sucursal
 *
 * @param array $columns Columnas existentes
 * @return array Columnas modificadas
 */
function itemora_sucursal_columns($columns) {
    $new_columns = array();
    
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        
        // Añadir columnas después de la descripción
        if ($key === 'description') {
            $new_columns['ubicacion'] = __('Ubicación', 'itemora');
            $new_columns['telefono'] = __('Teléfono', 'itemora');
            $new_columns['horario'] = __('Horario', 'itemora');
        }
    }
    
    return $new_columns;
}
add_filter('manage_edit-sucursal_columns', 'itemora_sucursal_columns');

/**
 * Muestra el contenido de las columnas personalizadas para Sucursal
 *
 * @param string $content Contenido actual
 * @param string $column_name Nombre de la columna
 * @param int $term_id ID del término
 * @return string Contenido modificado
 */
function itemora_sucursal_column_content($content, $column_name, $term_id) {
    switch ($column_name) {
        case 'ubicacion':
            $ubicacion = get_term_meta($term_id, '_ubicacion', true);
            if (!empty($ubicacion)) {
                $content = '<a href="' . esc_url($ubicacion) . '" target="_blank">' . __('Ver en mapa', 'itemora') . '</a>';
            } else {
                $content = '—';
            }
            break;
            
        case 'telefono':
            $telefono = get_term_meta($term_id, '_telefono', true);
            $content = !empty($telefono) ? esc_html($telefono) : '—';
            break;
            
        case 'horario':
            $horario = get_term_meta($term_id, '_horario', true);
            $content = !empty($horario) ? '<span title="' . esc_attr($horario) . '">' . __('Ver horario', 'itemora') . '</span>' : '—';
            break;
    }
    
    return $content;
}
add_filter('manage_sucursal_custom_column', 'itemora_sucursal_column_content', 10, 3);

/**
 * Registra los scripts y estilos para la página de taxonomías
 */
function itemora_taxonomias_admin_scripts($hook) {
    $screens = array('edit-tags.php', 'term.php');
    
    if (in_array($hook, $screens) && isset($_GET['taxonomy']) && in_array($_GET['taxonomy'], array('tipo_producto', 'categoria_producto', 'sucursal'))) {
        wp_enqueue_style('itemora-admin-style');
        wp_enqueue_script('itemora-admin-script');
    }
}
add_action('admin_enqueue_scripts', 'itemora_taxonomias_admin_scripts');