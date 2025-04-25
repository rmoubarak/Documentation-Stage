import defaultAjaxOptions from './js/default_ajax_options.js';
import './js/zxcvbn.js';


// Génération du captcha
function captchaRefresh() {
    $.ajax($.extend({
        url: '/public/security/captcharefresh',
        type: 'POST',
        success: function(data) {
            $('#captcha').html(data);

            if (false == $('#captcha').data('label')) {
                $('#captcha_label').hide();
            }
        }
    }, defaultAjaxOptions('captcha')));
}

$(document).on('click', '#captcha_refresh', function (event) {
    captchaRefresh();
    return false;
});

$(document).ready(function() {
    captchaRefresh();
});


// Renvoi du code de double authentification
$(document).on('click', '#2fa_renvoi', function (event) {
    $.ajax({
        url: '/public/security/2faresend',
        type: 'POST',
        success: function (data) {
            if (data == 'ok') {
                $('#2fa_renvoi_icon').html("<i class='fas fa-check fa-lg text-success' title='Code envoyé par email'></i> Code envoyé par email");
            } else {
                $('#2fa_renvoi_icon').html("<i class='fas fa-ban fa-lg text-danger' title='Une erreur est survenue. Code non envoyé'></i> Echec de l'envoi du code");
            }
        },
        beforeSend: function () {
            $('#2fa_renvoi_icon').html("<i class='fa-solid fa-spinner fa-spin warning'></i>");
        }
    });

    event.preventDefault();
});
