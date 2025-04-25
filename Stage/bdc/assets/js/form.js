import defaultAjaxOptions from './default_ajax_options.js';
import * as bootstrap from 'bootstrap';

// Initialisation des var et chargement du modal pour formulaire AJAX
$("#formModal").on("show.bs.modal", function(e) {
    let title = $(e.relatedTarget).attr("data-title");
    let href = $(e.relatedTarget).attr("data-href");
    let size = $(e.relatedTarget).attr("data-size");

    if (size == 'xl') {
        $(this).find('.modal-dialog').removeClass("modal-lg");
        $(this).find('.modal-dialog').addClass("modal-xl");
    } else {
        $(this).find('.modal-dialog').removeClass("modal-xl");
        $(this).find('.modal-dialog').addClass("modal-lg");
    }

    if (title && href) {
        $("#formModalBody").load(href, function(response, status, xhr) {
            if (xhr.status === 403) {
                window.location.reload();
            }
        });

        $('#formModalLabel').html("<i class='fas fa-pencil-alt'></i> " + title);
    }
});

// Déclenchement des requêtes AJAX de soumission de formulaires modal (hors fichiers)
$(document).on('submit', 'form[data-async]', function (event) {
    let $form = $(this);

    $.ajax($.extend({
        url: $form.attr('action'),
        data: $form.serialize(),
        method: $form.attr('method'),
        success: function (data) {
            if (data == 'ok') {
                $('#formModalLabel').html('');
                $('#formModalBody').html('');

                let modal = bootstrap.Modal.getInstance('#formModal');
                modal.hide();

                // Raffraîchissement AJAX
                if ($form.data('callback')) {
                    $.ajax($.extend({
                        url: $form.data('callback'),
                        type: 'POST',
                        success: function (data) {
                            $("#" + $form.data('list')).html(data);
                        }
                    }, defaultAjaxOptions()));
                }

                if ($form.data('callback2')) {
                    $.ajax($.extend({
                        url: $form.data('callback2'),
                        type: 'POST',
                        success: function (data) {
                            $("#" + $form.data('list2')).html(data);
                        }
                    }, defaultAjaxOptions()));
                }
            // Ou redirection de la page entière, ou reload
            } else if (data.substr(0, 8) == 'redirect') {
                window.location.assign(data.substr(9));
            } else if (data.substr(0, 7) == 'reload') {
                window.location.reload();
            } else {
                $('#formModalBody').html(data);
            }
        }
    }, defaultAjaxOptions()));

    event.preventDefault();
});

// Vidage de champs de formulaire
$.fn.clear = function()
{
    $(this).find('input')
        .filter(':text, :password, :file').val('')
        .end()
        .filter(':checkbox, :radio')
        .removeAttr('checked')
        .end()
        .end()
        .find('textarea').val('')
        .end()
        .find('select').prop("selectedIndex", -1).trigger('change')
        .find('option:selected').removeAttr('selected')
    ;
    return this;
};
$('.button_clear').on('click', function (event) {
    $('form').clear();
});
