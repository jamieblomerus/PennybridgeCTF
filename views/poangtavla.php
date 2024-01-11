<?php
namespace PBCTF\Views;

use PBCTF\Actions;

class Scoreboard {
    public function title() {
        echo 'Poängtavla - Pennybridge CTF';
    }

    public function render() {
        global $method;
        $raw = $method === 'POST';

        if (!$raw){
            Actions::add_action('title', [$this, 'title']);
            Actions::add_action('head', function() {
                ?>
                <script src="/static/js/page-specific/scoreboard.js" class="page-specific-script" defer></script>
                <?php
            });
            get_header();
        }
        ?>
        <main class="scoreboard" data-title="<?php $this->title() ?>" data-script="scoreboard">
            <div class="scoreboard-intro">
                <h2>Poängtavla</h1>
                <p>Här är en lista över alla deltagare och deras poäng. Poängen uppdateras varje gång en utmaning löses.</p>
                <?php
                if (\PBCTF\LoginAPI::is_logged_in()) {
                    $user = \PBCTF\LoginAPI::get_user();
                    if (!isset($user['nickname'])) {
                        ?>
                        <p class="error">
                            Du måste välja ett smeknamn för att kunna se din plats på poängtavlan
                            <button id="choose-nickname">Välj smeknamn</button>
                        </p>
                        <?php
                    }
                }
                ?>
            </div>
            <div class="scoreboard-table">
                <table id="scoreboard">
                    <thead>
                        <tr>
                            <th>Plats</th>
                            <th>Namn</th>
                            <th>Poäng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $users = \PBCTF\Users::$store->findBy(["nickname", "EXISTS", true], ["points" => "DESC"]);
                        $i = 1;
                        foreach ($users as $user) {
                            ?>
                            <tr>
                                <td><?php echo $i ?></td>
                                <td>--</td>
                                <td><?php echo $user['points'] ?></td>
                            </tr>
                            <?php
                            $i++;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <?php
            if (\PBCTF\LoginAPI::is_logged_in()) {
                if (!isset($user['nickname'])) {
                    ?>
                    <div class="overlay dialog" id="overlay">
                        <div class="dialog-content">
                            <h2>Välj ett smeknamn</h2>
                            <p>För att kunna se din plats på poängtavlan måste du välja ett smeknamn. Detta smeknamn kommer att visas på poängtavlan.</p>
                            <form id="nickname-form">
                                <label for="nickname">Smeknamn</label><br>
                                <input type="text" name="nickname" id="nickname" placeholder="Smeknamn" required><br>
                                <input type="submit" value="Spara">
                            </form>
                            <button id="close-overlay" aria-label="Stäng">&times;</button>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
        </main>
        <?php
        if (!$raw) {
            get_footer();
        }
    }
}

$page = new Scoreboard();
$page->render();