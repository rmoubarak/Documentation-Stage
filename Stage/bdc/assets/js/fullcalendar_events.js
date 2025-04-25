// Utilisation des CDN car l'import via importmap ne fonctionne pas (TypeError: class constructors must be invoked with 'new')
import { Calendar } from 'https://cdn.skypack.dev/@fullcalendar/core@6.1.10';
import dayGridPlugin from 'https://cdn.skypack.dev/@fullcalendar/daygrid@6.1.10';
import timeGridPlugin from 'https://cdn.skypack.dev/@fullcalendar/timegrid@6.1.10';
import listPlugin from 'https://cdn.skypack.dev/@fullcalendar/list@6.1.10';
import interactionPlugin from 'https://cdn.skypack.dev/@fullcalendar/interaction@6.1.10';
import frLocale from 'https://cdn.skypack.dev/@fullcalendar/core/locales/fr';
import * as bootstrap from 'bootstrap';

document.addEventListener('DOMContentLoaded', function() {
    let calendarEl = document.getElementById('calendar');

    let calendar = new Calendar(calendarEl, {
        plugins: [ interactionPlugin, dayGridPlugin, timeGridPlugin, listPlugin ],
        locale: frLocale,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        navLinks: true, // can click day/week names to navigate views
        editable: true,
        selectable: true,
        dayMaxEvents: true, // allow "more" link when too many events
        events: {
            url: '/demo/calendar',
            cache: true,
            type: 'POST',
            data: {},
            error: function () {
                alert('Une erreur est survenue.');
            }
        },
        eventClick: function (e) {
            $('#formModalLabel').html("<i class='fas fa-lg fa-glass-cheers'></i> Résumé d'un événement");
            //$("#formModalBody").load(Routing.generate('evenement_show', {'id': e.id}));
            $("#formModalBody").html("Contenu de l'événement " + e.event.title);

            let modal = new bootstrap.Modal(document.getElementById('formModal'));
            modal.show();
        }
    });

    calendar.render();
});
