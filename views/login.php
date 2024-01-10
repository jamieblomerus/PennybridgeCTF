<?php
namespace PBCTF\Views;

use PBCTF\Actions;

class Login {
    public function title() {
        echo 'Logga in - Pennybridge CTF';
    }

    public function render() {
        global $method;

        if ('POST' === $method) {
            if (\PBCTF\LoginAPI::api_callback()) {
                global $ignore_POST;
                $ignore_POST = true;
                require_once __DIR__ . '/login_code.php';
                exit;
            }
        }

        Actions::add_action('title', [$this, 'title']);
        Actions::add_action('body_end', [$this, 'JS_validation']);
        get_header();
        ?>
        <main class="login" data-title="<?php $this->title() ?>">
            <div class="login-container">
                <h2>Logga in med telefonnummer</h2>
                <form action="/inloggning" method="POST">
                    <label for="phone">Telefonnummer</label><br>
                    <input type="text" name="phone" id="phone" placeholder="Telefonnummer" required>
                    <input type="submit" value="Logga in">
                </form>
            </div>
        </main>
        <?php
        get_footer();
    }

    public function JS_validation() {
        ?>
        <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('form');
            const phone = document.querySelector('#phone');
            const phoneRegex = /^07[0-9]{8}$/;

            form.addEventListener('submit', (e) => {
                if (!phoneRegex.test(phone.value)) {
                    e.preventDefault();
                    phone.classList.add('invalid');
                }
            });
        });
        </script>
        <?php
    }
}

$page = new Login();
$page->render();