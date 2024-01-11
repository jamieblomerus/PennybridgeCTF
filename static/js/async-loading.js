document.addEventListener('DOMContentLoaded', function () {
    // Lägg till eventlisteners på alla element med klassen async-loading
    var elements = document.getElementsByClassName('async-loading');
    for (var i = 0; i < elements.length; i++) {
        elements[i].addEventListener('click', asyncLoading);
    }
});

function asyncLoading(event) {
    // Hämta href från elementet som klickades på
    var href = event.target.closest("a").getAttribute('href');

    // Efterfråga sidan med hjälp av XMLHttpRequest
    var request = new XMLHttpRequest();
    request.open('POST', href, true);
    request.onload = function () {
        if (request.status == 200) {
            // Ta bort gamla JS-skript från <head>
            removeScript();

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

            // Exekvera JavaScript-skript önskade av main-elementet
            var scripts = document.getElementsByTagName('main')[0].getAttribute('data-script');
            if (scripts) {
                scripts = scripts.split(',');
                for (var i = 0; i < scripts.length; i++) {
                    loadScript('/static/js/page-specific/' + scripts[i] + '.js');
                }
            }
        } else {
            console.log('Förfrågan misslyckades.\n\nStatus: ' + request.status + '\nURL: ' + href);
        }
    }
    request.onerror = function () {
        window.location.href = href;
    }
    request.send();
    event.preventDefault();
}

// Credit to awpross on StackOverflow: https://stackoverflow.com/a/63634012/10879934
function loadScript(src) {
    return new Promise(function (resolve, reject) {
        if (document.querySelector("script[src='" + src + "']") === null) {
            var script = document.createElement('script');
            script.onload = function () {
                resolve();
            };
            script.onerror = function () {
                reject();
            };
            script.src = src;
            script.classList.add('page-specific-script');
            document.head.appendChild(script);
        } else {
            resolve();
        }
    });
}
function removeScript() {
    var scripts = document.getElementsByClassName('page-specific-script');
    while (scripts.length > 0) {
        scripts[0].parentNode.removeChild(scripts[0]);
    }
}