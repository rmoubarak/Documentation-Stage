import defaultAjaxOptions from './js/default_ajax_options.js';
import './js/trigger_list.js';
import './js/trigger_remove.js';
import './js/typeahead_ad.js';
import './js/form.js';

$(document).on('typeahead:selected', '#utilisateur_ad_adUser', function (object, data) {
    $('#utilisateur_ad_adUserHidden').val(data.login);
});

// MAJ du select direction en fonction du pôle
$(document).on('change', '#utilisateur_pole', function () {
    let utilisateur_id = $('#utilisateur_id').val() != '' ? $('#utilisateur_id').val() : 0;

    $.ajax($.extend({
        url : '/admin/direction/select/' + $('#utilisateur_pole').val() + '/' + utilisateur_id,
        type: 'GET',
        success: function(data) {
            $('#utilisateur_direction').html(data);
        }
    }, defaultAjaxOptions()));
});
