import './js/typeahead_commune.js';
import './js/typeahead_adresse.js';
import './js/typeahead_commune_belge.js';
import './js/captcha_stats.js';
import './js/fullcalendar_events.js';


$('#commune').on('typeahead:selected', function (object, data) {
    $('#commune_code').val(data.code);
    $('#commune_sel').html('Code INSEE : ' + data.code + ' ; Département : ' + data.codeDepartement);
});

$('#adresse').on('typeahead:selected', function (object, data) {
    $('#adresse_sel').html('Label : ' + data.label + ' ; Context : ' + data.context);
});
