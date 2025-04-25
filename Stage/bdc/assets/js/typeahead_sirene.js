/**
 * Recherche d'entreprise via l'API SIRENE
 */

import Bloodhound from 'bloodhound-js';
import typeahead from 'typeahead.js';
import '../styles/typeahead.css';


let input = $('input[data-autocomplete-sirene]');

let sirenes = new Bloodhound({
    datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    remote: {
        url: '/sirene',
        replace: function (url, q) {
            return url + '/' + q;
        }
    }
});

input.typeahead({
    hint: false,
    highlight: true,
    minLength: 3
}, {
    name: 'sirene',
    display: 'denomination',
    limit: 30,
    source: sirenes,
    templates: {
        suggestion: function (data) {
            return '<div>&bull; ' + data.denomination + ', ' + data.commune + ' (' + data.codepostal + ')</div>';
        },
        notFound: function () {
            return '&nbsp;&nbsp;<i class="fa-solid fa-exclamation-triangle fa-xl text-warning"></i>&nbsp;<em>Aucun résultat</em>&nbsp;';
        },
        pending: function () {
            return '&nbsp;&nbsp;<i class="fa-solid fa-sync fa-spin-pulse fa-xl text-primary"></i>&nbsp;<em>Recherche en cours ...</em>&nbsp;';
        }
    }
});
