import Bloodhound from 'bloodhound-js';
import typeahead from 'typeahead.js';
import '../styles/typeahead.css';


let users = new Bloodhound({
    datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    remote: {
        url: '/ldapsearch',
        replace: function (url, q) {
            return url + '/' + q;
        }
    }
});

// On rend globales les variables de façon à les réutiliser dans une modal ajax
let typeahead_ad_options = {
    hint: false,
    highlight: true,
    minLength: 3
};
window.typeahead_ad_options = typeahead_ad_options;

let typeahead_ad_datasets = {
    name: 'utilisateur',
    display: 'value',
    limit: 100,
    source: users,
    templates: {
        suggestion: function (data) {
            if (data.sigle != '') {
                return '<div><strong>&bull; ' + data.civilite + ' ' + data.value + ' (' + data.login + ')</strong><br /><em>' +
                    data.fonction + ' - ' + data.sigle + '</em></div>';
            } else {
                return '<div><strong>&bull; ' + data.civilite + ' ' + data.value + ' (' + data.login + ')</strong><br /><em>' +
                    data.fonction + '</em></div>';
            }
        },
        notFound: function () {
            return '&nbsp;&nbsp;<i class="fa-solid fa-exclamation-triangle fa-xl text-warning"></i>&nbsp;<em>Aucun résultat</em>&nbsp;';
        },
        pending: function () {
            return '&nbsp;&nbsp;<i class="fa-solid fa-sync fa-spin-pulse fa-xl text-primary"></i>&nbsp;<em>Recherche en cours ...</em>&nbsp;';
        }
    }
};
window.typeahead_ad_datasets = typeahead_ad_datasets;

// Initialisation du typeahead
$('input[ad]').typeahead(typeahead_ad_options, typeahead_ad_datasets);
