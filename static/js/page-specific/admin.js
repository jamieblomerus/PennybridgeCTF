var admin_add_challenge = document.getElementById('add-challenge');

admin_add_challenge.addEventListener('submit', async e => {
    e.preventDefault();
    e.stopPropagation();

    const name = document.getElementById('name').value;
    const description = document.getElementById('description').value;
    const category = document.getElementById('category').value;
    const points = document.getElementById('points').value;
    const flag = document.getElementById('flag').value;

    const response = await fetch('/api/add_challenge', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            name,
            description,
            category,
            points,
            flag
        })
    });

    const json = await response.json();

    if (json.error) {
        alert(json.error);
    } else {
        alert('Utmaning skapad');
        window.location.reload();
    }
});

var admin_delete_challenge = document.getElementById('delete-challenge');

admin_delete_challenge.addEventListener('submit', async e => {
    e.preventDefault();

    const id = document.getElementById('challenge').value;

    const response = await fetch('/api/delete_challenge', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            id
        })
    });

    const json = await response.json();

    if (json.error) {
        alert(json.error);
    } else {
        alert('Utmaning raderad');
        window.location.reload();
    }
});