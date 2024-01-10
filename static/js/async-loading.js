document.addEventListener('DOMContentLoaded', function() {
    // Lägg till eventlisteners på alla element med klassen async-loading
    var elements = document.getElementsByClassName('async-loading');
    for (var i = 0; i < elements.length; i++) {
        elements[i].addEventListener('click', asyncLoading);
    }
});

function asyncLoading(event) {
    // Hämta href från elementet som klickades på
    var href = event.target.getAttribute('href');

    // Efterfråga sidan med hjälp av XMLHttpRequest
    var request = new XMLHttpRequest();
    request.open('POST', href, true);
    request.onload = function() {
        if (request.status == 200) {
            // Uppdatera main-elementet
            document.getElementsByTagName('main')[0].outerHTML = request.responseText;
            // Uppdatera URL:en i webbläsaren
            window.history.pushState(null, null, href);
            // Uppdatera titeln
            document.title = document.getElementsByTagName('main')[0].getAttribute('data-title');
            // Lägg till eventlisteners på alla element med klassen async-loading
            var elements = document.getElementsByClassName('async-loading');
            for (var i = 0; i < elements.length; i++) {
                elements[i].addEventListener('click', asyncLoading);
            }
        } else {
            console.log('Förfrågan misslyckades.\n\nStatus: ' + request.status + '\nURL: ' + href);
        }
    }
    request.onerror = function() {
        window.location.href = href;
    }
    request.send();
    event.preventDefault();
}
