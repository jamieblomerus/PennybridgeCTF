<?php
namespace PBCTF\Views;

use PBCTF\Actions;

class Utmaning {
    private array|null $challenge;

    public function title() {
        echo $this->challenge['name'] . ' - Pennybridge CTF';
    }

    public function render() {
        global $method, $route;
        $raw = $method === 'POST';

        // Hämta utmaningsid från $route
        $challenge_id = explode('/', $route)[3];

        // Hämta utmaning från databasen
        $this->challenge = \PBCTF\Challenges::get_challenge($challenge_id);

        // Om utmaningen inte finns, skicka tillbaka 404
        if (!isset($this->challenge)) {
            require_once __DIR__ . '/404.php';
            exit;
        }

        // Om användaren inte är inloggad, skicka tillbaka 401 och omdirigera till inloggningssidan
        if (!\PBCTF\LoginAPI::is_logged_in()) {
            http_response_code(401);
            header('Location: /inloggning');
            exit;
        }

        // Kolla om användaren redan löst utmaningen
        $user = \PBCTF\LoginAPI::get_user();
        $solved = in_array($challenge_id, $user['solved']);

        if (!$raw){
            Actions::add_action('title', [$this, 'title']);
            get_header();
        }
        ?>
        <main class="challenge challenge-<?php echo $challenge_id ?>" data-title="<?php $this->title() ?>">
            <div>
                <h2><?php echo $this->challenge['name'] ?></h2>
                <p class="challenge-points"><?php echo $this->challenge['points'] ?> poäng</p>
                <p class="challenge-description"><strong>Beskrivning</strong><br><?php echo $this->challenge['description'] ?></p>
            </div>
            <form action="/utmaningar/<?php echo $challenge_id ?>" method="POST"<?php echo $solved ? ' class="solved"' : '' ?>>
                <h3>Lös utmaning</h3>
                <p>För att lösa utmaningen, skicka in flaggan nedan. Samtliga flaggor har formatet <code>PBCTF{flagga}</code>.</p>
                <label for="flag">Flagga</label><br>
                <input type="text" name="flag" id="flag" placeholder="<?php echo $solved ? 'Du har redan löst denna utmaning' : 'PBCTF{...}' ?>"<?php echo $solved ? ' disabled' : '' ?>>
                <input type="submit" value="Skicka" <?php echo $solved ? 'disabled' : '' ?>>
            </form>
        </main>
        <?php
        if (!$raw) {
            Actions::add_action('body_end', [$this, 'JS']);
            get_footer();
        }
    }

    public function JS() {
        ?>
        <script>
            const form = document.querySelector('form');
            const flag = document.querySelector('#flag');
            const submit = document.querySelector('input[type="submit"]');

            form.addEventListener('submit', (e) => {
                e.preventDefault();
                submit.disabled = true;
                submit.value = 'Skickar...';
                if (document.querySelector('.error')) {
                    document.querySelector('.error').remove();
                }
                fetch('/api/solve_challenge', {
                    method: 'POST',
                    body: JSON.stringify({
                        flag: flag.value,
                        challenge_id: <?php echo $this->challenge['_id'] ?>
                    })
                }).then(res => res.json()).then(data => {
                    if (data.error) {
                        flag.classList.add('invalid');
                        submit.disabled = false;
                        submit.value = 'Skicka';

                        flag.addEventListener('input', () => {
                            flag.classList.remove('invalid');
                        });

                        // Lägg till felmeddelande ovan formuläret
                        var error = document.createElement('p');
                        error.classList.add('error');
                        error.innerText = data.error;
                        form.insertBefore(error, flag);
                    } else {
                        flag.disabled = true;
                        flag.value = '';
                        submit.value = 'Flagga skickad';
                    }
                });
            });
        </script>
        <?php
    }
}

$page = new Utmaning();
$page->render();