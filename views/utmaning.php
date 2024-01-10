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
            <form action="/utmaningar/<?php echo $challenge_id ?>" method="POST">
                <h3>Lös utmaning</h3>
                <p>För att lösa utmaningen, skicka in flaggan nedan. Samtliga flaggor har formatet <code>PBCTF{flagga}</code>.</p>
                <label for="flag">Flagga</label><br>
                <input type="text" name="flag" id="flag" placeholder="PBCTF{...}" required>
                <input type="submit" value="Skicka">
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