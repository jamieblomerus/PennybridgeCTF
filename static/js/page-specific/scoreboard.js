var scoreboard_chooseNickname = document.querySelector('#choose-nickname');
var scoreboard_closeOverlay = document.querySelector('#close-overlay');
var scoreboard_overlay = document.querySelector('#overlay');
var scoreboard_nicknameForm = document.querySelector('#nickname-form');
var scoreboard_nickname = document.querySelector('#nickname');

scoreboard_chooseNickname.addEventListener('click', scoreboard_showOverlay);
scoreboard_closeOverlay.addEventListener('click', scoreboard_hideOverlay);

function scoreboard_showOverlay() {
    scoreboard_overlay.classList.add('visible');
}

function scoreboard_hideOverlay() {
    scoreboard_overlay.classList.remove('visible');
}