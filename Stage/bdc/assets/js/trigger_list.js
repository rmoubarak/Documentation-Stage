import defaultAjaxOptions from './default_ajax_options.js';


// AJAX sur listing d'entités
$(document).on('change', '.js-list', function (event) {
    let form = $(this);

    $.ajax($.extend({
        url : form.data('action'),
        type: 'POST',
        data : form.serialize(),
        success: function(data) {
            $("#" + form.data('list')).html(data);
        }
    }, defaultAjaxOptions(form.data('list'))));
});

$('form[data-filtre]').change();

$(document).on('submit', '.js-list', function (event) {
    return false;
});

// Navigation en AJAX dans le listing : on récupère le lien href et on le passe à la requête AJAX
$(document).on('click', '.page-link, .sortable, .asc, .desc', function (event) {
    let list = $(this).closest(".js-list").data('list');

    $.ajax($.extend({
        url: this.href,
        type: 'POST',
        data: $('#frm_filtre').serialize(),
        success: function(data) {
            $('#' + list).html(data);
            window.scroll({
                behavior: 'smooth'
            });
        }
    }, defaultAjaxOptions(list)));

    return false;
});
