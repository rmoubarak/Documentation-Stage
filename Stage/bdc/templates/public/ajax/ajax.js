
$(document).ready(function () {
    $('form').on('submit', function (e) {
        e.preventDefault(); // Empêcher le rechargement de la page

        $.ajax({
            url: $(this).attr('action'), // URL définie dans le formulaire
            type: 'POST', // ou 'GET'
            data: $(this).serialize(), // Sérialise les données du formulaire
            dataType: 'json', // Format de réponse attendu
            success: function (response) {
                $('#results-container').empty();
                if (response.articles.length) {
                    response.articles.forEach(article => {
                        $('#results-container').append(`
                            <div>
                                <h3>${article.title}</h3>
                                <p>${article.content.slice(0, 100)}...</p>
                                <p>Catégorie : ${article.categorie}</p>
                            </div>
                        `);
                    });
                } else {
                    $('#results-container').append('<p>Aucun article trouvé</p>');
                }
            },
            error: function (xhr) {
                console.error('Erreur AJAX :', xhr.responseText);
            },
        });
    });
});
