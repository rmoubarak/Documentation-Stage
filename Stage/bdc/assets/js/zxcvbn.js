import 'zxcvbn';

$('#security_password_password_first').keyup(function() {
    var result = zxcvbn(this.value);
    var bar = $('#strengthBar');
    var barTxt = $('#progress-bar-txt');

    switch (result.score) {
        case 0:
            bar.attr('class', 'progress-bar progress-bar-striped bg-danger').css('width', '10%');
            barTxt.html('Trop prédictible : mauvaise protection');
            break;
        case 1:
            bar.attr('class', 'progress-bar progress-bar-striped bg-danger').css('width', '25%');
            barTxt.html('Toujours prédictible : mauvaise protection');
            break;
        case 2:
            bar.attr('class', 'progress-bar progress-bar-striped bg-danger').css('width', '50%');
            barTxt.html('Relativement prédictible : mauvaise protection');
            break;
        case 3:
            bar.attr('class', 'progress-bar progress-bar-striped bg-warning').css('width', '75%');
            barTxt.html('Sécurisé : protection moyenne');
            break;
        case 4:
            bar.attr('class', 'progress-bar progress-bar-striped bg-success').css('width', '100%');
            barTxt.html('Très peu prédictible : protection solide');
            break;
    }
});

$('.password_view').click(function () {
    var input = $('#' + $(this).data('id'));

    if (input.attr('type') === "password") {
        input.attr('type', 'text');
    } else {
        input.attr('type', 'password');
    }
});
