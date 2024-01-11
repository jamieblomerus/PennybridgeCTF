var scoreboard_chooseNickname = document.querySelector('#choose-nickname');
var scoreboard_closeOverlay = document.querySelector('#close-overlay');
var scoreboard_overlay = document.querySelector('#overlay');
var scoreboard_dialogContent = document.querySelector('#dialog-content');
var scoreboard_nicknameForm = document.querySelector('#nickname-form');
var scoreboard_nickname = document.querySelector('#nickname');

if (scoreboard_overlay) {
    scoreboard_chooseNickname.addEventListener('click', function() {
        scoreboard_overlay.classList.add('visible');
    });
    scoreboard_closeOverlay.addEventListener('click', function() {
        scoreboard_overlay.classList.remove('visible');
    });
    scoreboard_nicknameForm.addEventListener('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var nickname = scoreboard_nickname.value;
        fetch('/api/set_nickname', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                nickname: nickname
            })
        }).then(function(response) {
            return response.json();
        }).then(function(json) {
            if (json.error) {
                alert(json.error);
            } else {
                scoreboard_dialogContent.innerHTML = '<p>Ditt smeknamn har ändrats till ' + nickname + '</p><a id="close-overlay" href="/poangtavla">&times;</a>';
                document.querySelector('#close-overlay').addEventListener('click', function(event) {
                    scoreboard_overlay.classList.remove('visible');
                    asyncLoading(event);
                });
            }
        });
    });
}