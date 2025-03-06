/**
 * Itemora Admin JavaScript
 * Handles dynamic functionality in the admin interface
 */
(function($) {
    'use strict';

    // Initialize when document is ready
    $(document).ready(function() {
        // Toggle sections visibility based on checkbox state
        initToggleSections();
        
        // Initialize tabs if they exist
        initTabs();
        
        // Initialize form validation
        initFormValidation();
        
        // Initialize CSV preview
        initCSVPreview();
    });

    /**
     * Toggle sections visibility based on checkbox state
     */
    function initToggleSections() {
        // Toggle details section
        $('#detalles-fields').toggle($('input[name="activar_detalles"]').is(':checked'));
        $('input[name="activar_detalles"]').on('change', function() {
            $('#detalles-fields').slideToggle(300);
        });

        // Toggle extras section
        $('#extras-fields').toggle($('input[name="activar_extras"]').is(':checked'));
        $('input[name="activar_extras"]').on('change', function() {
            $('#extras-fields').slideToggle(300);
        });
    }

    /**
     * Initialize tabs functionality
     */
    function initTabs() {
        // If URL contains tab parameter, activate that tab
        var urlParams = new URLSearchParams(window.location.search);
        var currentTab = urlParams.get('tab');
        
        if (currentTab) {
            $('.nav-tab-wrapper .nav-tab').removeClass('nav-tab-active');
            $('.nav-tab-wrapper .nav-tab[href*="tab=' + currentTab + '"]').addClass('nav-tab-active');
        }
        
        // Add smooth transition when clicking tabs
        $('.nav-tab-wrapper .nav-tab').on('click', function() {
            $('.nav-tab-wrapper .nav-tab').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
        });
    }

    /**
     * Form validation for all admin forms
     */
    function initFormValidation() {
        // Validate forms before submission
        $('form').on('submit', function(e) {
            var isValid = true;
            
            // Check required fields
            $(this).find('input[required]').each(function() {
                if (!$(this).val()) {
                    isValid = false;
                    $(this).addClass('error');
                    
                    // Add error message if not exists
                    if (!$(this).next('.error-message').length) {
                        $(this).after('<span class="error-message">' + itemora_admin.messages.required_field + '</span>');
                    }
                } else {
                    $(this).removeClass('error');
                    $(this).next('.error-message').remove();
                }
            });
            
            // Prevent submission if validation fails
            if (!isValid) {
                e.preventDefault();
                
                // Show error notice
                if (!$('.notice-error').length) {
                    $('.itemora-card').prepend(
                        '<div class="notice notice-error is-dismissible">' +
                        '<p>' + itemora_admin.messages.form_errors + '</p>' +
                        '</div>'
                    );
                }
                
                // Scroll to first error
                $('html, body').animate({
                    scrollTop: $('.error').first().offset().top - 100
                }, 500);
            }
        });
        
        // Remove error class on input
        $('input').on('input', function() {
            $(this).removeClass('error');
            $(this).next('.error-message').remove();
        });
    }

    /**
     * CSV file preview functionality
     */
    function initCSVPreview() {
        $('input[name="archivo_csv"]').on('change', function(e) {
            var file = e.target.files[0];
            
            if (file) {
                // Check file type
                if (file.type !== 'text/csv' && !file.name.endsWith('.csv')) {
                    alert(itemora_admin.messages.invalid_file_type);
                    $(this).val('');
                    return;
                }
                
                // Check file size (max 5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert(itemora_admin.messages.file_too_large);
                    $(this).val('');
                    return;
                }
                
                // Show file name
                var fileName = file.name;
                if (fileName.length > 30) {
                    fileName = fileName.substring(0, 27) + '...';
                }
                
                // Add file info
                if (!$('.file-info').length) {
                    $(this).after('<div class="file-info">' + fileName + ' (' + formatFileSize(file.size) + ')</div>');
                } else {
                    $('.file-info').text(fileName + ' (' + formatFileSize(file.size) + ')');
                }
            }
        });
    }

    /**
     * Format file size in human-readable format
     */
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        
        var k = 1024;
        var sizes = ['Bytes', 'KB', 'MB', 'GB'];
        var i = Math.floor(Math.log(bytes) / Math.log(k));
        
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    /**
     * Show dismissible notices
     */
    function showNotice(message, type) {
        var noticeClass = 'notice-' + (type || 'info');
        
        var $notice = $(
            '<div class="notice ' + noticeClass + ' is-dismissible">' +
            '<p>' + message + '</p>' +
            '<button type="button" class="notice-dismiss">' +
            '<span class="screen-reader-text">Dismiss this notice.</span>' +
            '</button>' +
            '</div>'
        );
        
        $('.itemora-card').prepend($notice);
        
        // Handle dismiss button
        $notice.find('.notice-dismiss').on('click', function() {
            $notice.fadeOut(300, function() {
                $(this).remove();
            });
        });
        
        // Auto dismiss after 5 seconds
        setTimeout(function() {
            $notice.fadeOut(300, function() {
                $(this).remove();
            });
        }, 5000);
    }

})(jQuery);