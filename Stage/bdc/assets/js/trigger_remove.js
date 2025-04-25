import defaultAjaxOptions from './default_ajax_options.js';
import * as bootstrap from 'bootstrap';

// Modal de confirmation de suppression
$(document).on('submit', 'form[data-confirmation]', function (event) {
    let form = $(this);
    let confirm = $('#confirmationModal');
    let modalDelete = new bootstrap.Modal('#confirmationModal');
    let title = form.attr("data-title");

    if (title) {
        $('.modal-title-delete').html(title);
    }

    if (confirm.data('result') !== 'yes') {
        //cancel submit event
        event.preventDefault();
        modalDelete.show();

        confirm
            .off('click', '#btnYes')
            .on('click', '#btnYes', function () {
                confirm.data('result', 'yes');
                form.find('input[type="submit"]').attr('disabled', 'disabled');
                form.submit();
            });
    }
});

// Modal de confirmation de suppression en AJAX
$(document).on('submit', 'form[data-ajaxconfirmation]', function (event) {
    let form = $(this);
    let confirm = $('#confirmationModal');
    let modalDelete = new bootstrap.Modal('#confirmationModal');
    let modalForm = bootstrap.Modal.getInstance('#formModal');

    if (confirm.data('result') !== 'yes') {
        event.preventDefault();
        modalDelete.show();

        confirm
            .off('click', '#btnYes')
            .on('click', '#btnYes', function () {
                //confirm.data('result', 'yes');
                form.find('input[type="submit"]').attr('disabled', 'disabled');

                // Suppression via AJAX
                $.ajax($.extend({
                    url: form.attr('action'),
                    type: 'DELETE',
                    data : form.serialize(),
                    success: function(data) {
                        $('#formModalLabel').html('');
                        $('#formModalBody').html('');
                        modalDelete.hide();
                        modalForm.hide();

                        if (data == 'ok') {
                            $.ajax($.extend({
                                url: form.data('callback'),
                                type: 'POST',
                                success: function (data) {
                                    $("#" + form.data('list')).html(data);
                                }
                            }, defaultAjaxOptions()));
                        } else {
                            alert(data);
                        }
                    }
                }, defaultAjaxOptions()));
            });
    }
});
