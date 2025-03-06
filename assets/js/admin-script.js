jQuery(document).ready(function($) {
    // Estado select change
    $('.itemora-estado-select').on('change', function() {
        $(this).closest('form').find('button').addClass('button-primary');
    });
    
    // Mostrar/ocultar secciones según checkboxes
    $('input[name="activar_detalles"]').on('change', function() {
        if ($(this).is(':checked')) {
            $('.itemora-fields-section').fadeIn();
        } else {
            $('.itemora-fields-section').fadeOut();
        }
    });
    
    // Inicializar estado de secciones
    if (!$('input[name="activar_detalles"]').is(':checked')) {
        $('.itemora-fields-section').hide();
    }
    
    // Importación CSV
    $('form[name="itemora_csv_import"]').on('submit', function(e) {
        var fileInput = $(this).find('input[type="file"]');
        if (fileInput.val() === '') {
            e.preventDefault();
            alert('Por favor selecciona un archivo CSV para importar.');
            return false;
        }
    });
});