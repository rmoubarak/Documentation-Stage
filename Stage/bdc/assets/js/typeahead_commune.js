/**
 * Recherche de commune via l'API geo
 * https://geo.api.gouv.fr/decoupage-administratif/communes
 *
 * L'input doit contenir la propiété "data-autocomplete-commune=true"
 */

import Bloodhound from 'bloodhound-js';
import typeahead from 'typeahead.js';
import '../styles/typeahead.css';


let typeahead_communes = new Bloodhound({
    datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    remote: {
        url: 'https://geo.api.gouv.fr/communes?nom=%QUERY',
        wildcard: '%QUERY'
    }
});

// On rend globales les variables de façon à les réutiliser dans une modal ajax
let typeahead_commune_options = {
    hint: false,
    highlight: true,
    minLength: 3
};
window.typeahead_commune_options = typeahead_commune_options;

let typeahead_commune_datasets = {
    name: 'commune',
    display: 'nom',
    limit: 30,
    source: typeahead_communes,
    templates: {
        suggestion: function (data) {
            return '<div>&bull; ' + data.nom + ' (' + data.codeDepartement + ')</div>';
        },
        notFound: function () {
            return '&nbsp;&nbsp;<i class="fa-solid fa-exclamation-triangle fa-xl text-warning"></i>&nbsp;<em>Aucun résultat</em>&nbsp;';
        },
        pending: function () {
            return '&nbsp;&nbsp;<i class="fa-solid fa-sync fa-spin-pulse fa-xl text-primary"></i>&nbsp;<em>Recherche en cours ...</em>&nbsp;';
        }
    }
};
window.typeahead_commune_datasets = typeahead_commune_datasets;

// Initialisation du typeahead
$('input[data-autocomplete-commune]').typeahead(typeahead_commune_options, typeahead_commune_datasets);
