import $ from 'jquery';

// Propriétés par défaut pour les requêtes AJAX
export default function(list) {
    return {
        cache: false,
        beforeSend: function () {
            $('#loader').show();

            if (list) {
                $('#' + list).html('<i class="fa-solid fa-spin fa-lg fa-spinner fa-2xl text-primary text-center w-100"></i>')
            }
        },
        complete: function () {
            $('#loader').hide();
        },
        error: function (xhr, ajaxOptions, thrownError) {
            if (xhr.status === 401) {
                window.location.replace("/");
            } else if (xhr.status === 403) {
                alert('Vous ne disposez pas des autorisations nécessaires.');
            } else {
                alert('Une erreur est survenue.');
            }
        }
    };
}
