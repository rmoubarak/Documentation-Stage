/**
 * Recherche d'adresse postale via l'API IGN
 * https://geo.api.gouv.fr/adresse
 *
 * L'input doit contenir la propiété "data-autocomplete-adresse=true" et un input avec "data-autocomplete-commune-code"
 */

import Bloodhound from 'bloodhound-js';
import typeahead from 'typeahead.js';
import '../styles/typeahead.css';


let typeahead_adresses = new Bloodhound({
    datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    remote: {
        url: '/adressepostale',
        replace: function (url, q) {
            return url + '/' + q + '/' + $('input[data-autocomplete-commune-code]').val();
        }
    }
});

// On rend globales les variables de façon à les réutiliser dans une modal ajax
let typeahead_adresse_options = {
    hint: false,
    highlight: true,
    minLength: 3
};
window.typeahead_adresse_options = typeahead_adresse_options;

let typeahead_adresse_datasets = {
    name: 'adresse',
    display: 'name',
    limit: 30,
    source: typeahead_adresses,
    templates: {
        suggestion: function (data) {
            return '<div>&bull; ' + data.name + ' - ' + data.city + ' (' + data.postcode + ')</div>';
        },
        notFound: function () {
            return '&nbsp;&nbsp;<i class="fa-solid fa-exclamation-triangle fa-xl text-warning"></i>&nbsp;<em>Aucun résultat</em>&nbsp;';
        },
        pending: function () {
            return '&nbsp;&nbsp;<i class="fa-solid fa-sync fa-spin-pulse fa-xl text-primary"></i>&nbsp;<em>Recherche en cours ...</em>&nbsp;';
        }
    }
}
window.typeahead_adresse_datasets = typeahead_adresse_datasets;

// Initialisation du typeahead
$('input[data-autocomplete-adresse]').typeahead(typeahead_adresse_options, typeahead_adresse_datasets);
