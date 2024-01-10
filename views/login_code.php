<?php
namespace PBCTF\Views;

use PBCTF\Actions;

class LoginCode {
    public function title() {
        echo 'Logga in - Pennybridge CTF';
    }

    public function render() {
        global $method, $phone, $ignore_POST;

        if (!isset($phone)) {
            if (isset($_POST['phone'])) {
                $phone = $_POST['phone'];
            } else {
                header('Location: /inloggning');
                exit;
            }
        }

        if ('POST' === $method && !$ignore_POST) {
            if (!isset($_POST['verification_code'])) {
                header('Location: /inloggning');
                exit;
            }

            if (!preg_match('/^[0-9]{6}$/', $_POST['verification_code'])) {
                $invalid_code = true;
            }

            if (!isset($invalid_code) && \PBCTF\LoginAPI::verify_code($phone, $_POST['verification_code'])) {
                $_SESSION['phone'] = $phone;
                header('Location: /');
                exit;
            } else {
                $invalid_code = true;
            }
        }

        Actions::add_action('title', [$this, 'title']);
        get_header();
        ?>
        <main class="login" data-title="<?php $this->title() ?>">
            <div class="login-container">
                <h2>Logga in med telefonnummer</h2>
                <?php
                if (isset($invalid_code)) {
                    ?>
                    <p class="error">Ogiltig verifieringskod</p>
                    <?php
                }
                ?>
                <form action="/inloggning/kod" method="POST">
                    <label for="verification_code">Verifieringskod</label>
                    <input type="text" name="verification_code" id="verification_code" placeholder="Verifieringskod" required <?php echo isset($invalid_code) ? 'class="invalid"' : ''?> >
                    <input type="hidden" name="phone" value="<?php echo $phone ?>">
                    <input type="submit" value="Logga in">
                </form>
            </div>
        </main>
        <?php
        get_footer();
    }
}

$page = new LoginCode();
$page->render();