var challenge_form = document.querySelector('form');
var challenge_flag = document.querySelector('#flag');
var challenge_submit = document.querySelector('input[type="submit"]');

challenge_form.addEventListener('submit', (e) => {
    e.preventDefault();
    e.stopPropagation();
    challenge_submit.disabled = true;
    challenge_submit.value = 'Skickar...';
    if (document.querySelector('.error')) {
        document.querySelector('.error').remove();
    }
    fetch('/api/solve_challenge', {
        method: 'POST',
        body: JSON.stringify({
            flag: challenge_flag.value,
            challenge_id: challenge_form.attributes['data-challenge-id'].value
        })
    }).then(res => res.json()).then(data => {
        if (data.error) {
            challenge_flag.classList.add('invalid');
            challenge_submit.disabled = false;
            challenge_submit.value = 'Skicka';

            challenge_flag.addEventListener('input', () => {
                challenge_flag.classList.remove('invalid');
            });

            // Lägg till felmeddelande ovan formuläret
            var error = document.createElement('p');
            error.classList.add('error');
            error.innerText = data.error;
            form.insertBefore(error, flag);
        } else {
            challenge_flag.disabled = true;
            challenge_flag.value = '';
            challenge_flag.attributes['placeholder'].value = 'Du har redan löst denna utmaning';
            challenge_form.classList.add('solved');
            challenge_submit.value = 'Skicka';
        }
    });
});