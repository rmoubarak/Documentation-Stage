/**
 * Recherche de commune belge
 *
 * L'input doit contenir la propiété "data-autocomplete-commune-belge=true"
 */

import Bloodhound from 'bloodhound-js';
import typeahead from 'typeahead.js';
import '../styles/typeahead.css';


let typeahead_communes_belges = new Bloodhound({
    datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    remote: {
        url: '/commune/belge',
        replace: function (url, q) {
            return url + '/' + q;
        }
    }
});

// On rend globales les variables de façon à les réutiliser dans une modal ajax
let typeahead_commune_belge_options = {
    hint: false,
    highlight: true,
    minLength: 3
};
window.typeahead_commune_belge_options = typeahead_commune_belge_options;

let typeahead_commune_belge_datasets = {
    name: 'commune_belge',
    display: 'nom',
    limit: 30,
    source: typeahead_communes_belges,
    templates: {
        suggestion: function (data) {
            return '<div>&bull; ' + data.nom + ' (' + data.code + ')</div>';
        },
        notFound: function () {
            return '&nbsp;&nbsp;<i class="fa-solid fa-exclamation-triangle fa-xl text-warning"></i>&nbsp;<em>Aucun résultat</em>&nbsp;';
        },
        pending: function () {
            return '&nbsp;&nbsp;<i class="fa-solid fa-sync fa-spin-pulse fa-xl text-primary"></i>&nbsp;<em>Recherche en cours ...</em>&nbsp;';
        }
    }
}
window.typeahead_commune_belge_datasets = typeahead_commune_belge_datasets;

// Initialisation du typeahead
$('input[data-autocomplete-commune-belge]').typeahead(typeahead_commune_belge_options, typeahead_commune_belge_datasets);
