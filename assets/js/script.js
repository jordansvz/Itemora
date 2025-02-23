jQuery(document).ready(function($) {
    // Abrir modal al hacer clic en "Editar"
    $('.itemora-editar-producto').on('click', function(e) {
        e.preventDefault();
        var producto_id = $(this).data('id');

        // Cargar los datos del producto usando AJAX
        $.ajax({
            url: itemora_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'obtener_datos_producto',
                producto_id: producto_id
            },
            success: function(response) {
                var producto = JSON.parse(response);

                // Rellenar el formulario con los datos del producto
                $('#producto_id').val(producto.id);
                $('#titulo').val(producto.titulo);
                $('#descripcion').val(producto.descripcion);

                // Mostrar el modal
                $('#itemora-modal-editar').show();
            }
        });
    });

    // Cerrar el modal
    $('#itemora-modal-editar').on('click', function(e) {
        if ($(e.target).is('#itemora-modal-editar')) {
            $(this).hide();
        }
    });

    // Procesar el formulario de edición
    $('#itemora-formulario-editar-producto').on('submit', function(e) {
        e.preventDefault();

        var formData = $(this).serialize();

        $.ajax({
            url: itemora_ajax.ajax_url,
            type: 'POST',
            data: formData + '&action=editar_producto',
            success: function(response) {
                alert('Producto editado correctamente.');
                location.reload(); // Recargar la página para ver los cambios
            }
        });
    });

    // Eliminar producto
    $('.itemora-eliminar-producto').on('click', function(e) {
        e.preventDefault();
        var producto_id = $(this).data('id');

        if (confirm('¿Estás seguro de que deseas eliminar este producto?')) {
            $.ajax({
                url: itemora_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'eliminar_producto',
                    producto_id: producto_id
                },
                success: function(response) {
                    alert('Producto eliminado correctamente.');
                    location.reload(); // Recargar la página para ver los cambios
                }
            });
        }
    });
});