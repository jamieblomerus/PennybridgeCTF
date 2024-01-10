<?php
namespace PBCTF\Views;

use PBCTF\Actions;

class Home {
    public function title() {
        echo 'Utmaningar - Pennybridge CTF';
    }

    public function render() {
        global $method;
        $raw = $method === 'POST';

        if (!$raw){
            Actions::add_action('title', [$this, 'title']);
            get_header();
        }

        $loggedin = \PBCTF\LoginAPI::is_logged_in();
        ?>
        <main class="challenges<?php echo !$loggedin ? ' disabled' : '' ?>" data-title="<?php $this->title() ?>">
            <h2>Utmaningar</h2>
            <?php
            if (!$loggedin) {
                ?>
                <p class="error">Du måste logga in för att komma åt utmaningarna</p>
                <?php
            }
            ?>
            <div class="challenge-categories">
                <div class="challenge-category">
                    <h3>Webb</h3>
                    <ul>
                        <li>
                            <a href="/utmaningar/webb/1">
                                <p class="challenge-name">Hello World</p>
                                <p class="challenge-points">100 poäng</p>
                            </a>
                        </li>
                        <li>
                            <a href="/utmaningar/webb/2">
                                <p class="challenge-name">Hello World 2</p>
                                <p class="challenge-points">200 poäng</p>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="challenge-category">
                    <h3>Forensik</h3>
                    <ul>
                        <li>
                            <a href="/utmaningar/webb/1">
                                <p class="challenge-name">Hello World</p>
                                <p class="challenge-points">100 poäng</p>
                            </a>
                        </li>
                        <li>
                            <a href="/utmaningar/webb/2">
                                <p class="challenge-name">Hello World 2</p>
                                <p class="challenge-points">200 poäng</p>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </main>
        <?php
        if (!$raw) {
            get_footer();
        }
    }
}

$page = new Home();
$page->render();