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
            Actions::add_action('head', function() {
                ?>
                <script src="/static/js/page-specific/challenge.js" class="page-specific-script" defer></script>
                <?php
            });
            get_header();
        }
        ?>
        <main class="challenge" data-title="<?php $this->title() ?>" data-script="challenge">
            <div>
                <h2><?php echo $this->challenge['name'] ?></h2>
                <p class="challenge-points"><?php echo $this->challenge['points'] ?> poäng</p>
                <p class="challenge-description"><strong>Beskrivning</strong><br><?php echo $this->challenge['description'] ?></p>
            </div>
            <form <?php echo $solved ? 'class="solved"' : '' ?> data-challenge-id="<?php echo $challenge_id ?>">
                <h3>Lös utmaning</h3>
                <p>För att lösa utmaningen, skicka in flaggan nedan. Samtliga flaggor har formatet <code>PBCTF{flagga}</code>.</p>
                <label for="flag">Flagga</label><br>
                <input type="text" name="flag" id="flag" placeholder="<?php echo $solved ? 'Du har redan löst denna utmaning' : 'PBCTF{...}' ?>"<?php echo $solved ? ' disabled' : '' ?>>
                <input type="submit" value="Skicka" <?php echo $solved ? 'disabled' : '' ?>>
            </form>
        </main>
        <?php
        if (!$raw) {
            get_footer();
        }
    }
}

$page = new Utmaning();
$page->render();