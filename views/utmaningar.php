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
                <?php
                $categories = [
                    'webb' => 'Webb',
                    'forensik' => 'Forensik',
                    'reverse' => 'Reverse',
                    'misc' => 'Misc'
                ];

                foreach ($categories as $category => $name) {
                    $challenges = \PBCTF\Challenges::get_challenges_by_category($category);

                    if (empty($challenges)) {
                        continue;
                    }
                    ?>
                    <div class="challenge-category">
                        <h3><?php echo $name ?></h3>
                        <ul>
                            <?php
                            foreach ($challenges as $challenge) {
                                ?>
                                <li>
                                    <?php
                                    if ($loggedin) {
                                        ?>
                                        <a class="async-loading" href="/utmaningar/<?php echo $category ?>/<?php echo $challenge['_id'] ?>">
                                        <?php
                                    }
                                    ?>
                                            <p class="challenge-name"><?php echo $challenge['name'] ?></p>
                                            <p class="challenge-points"><?php echo $challenge['points'] ?> poäng</p>
                                    <?php
                                    if ($loggedin) {
                                        ?>
                                        </a>
                                        <?php
                                    }
                                    ?>
                                </li>
                                <?php
                            }
                            ?>
                        </ul>
                    </div>
                    <?php
                }
                ?>
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