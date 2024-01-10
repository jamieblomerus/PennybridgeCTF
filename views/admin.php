<?php
namespace PBCTF\Views;

use PBCTF\Actions;

class Admin {
    public function title() {
        echo 'Administratörsvy - Pennybridge CTF';
    }

    public function render() {
        global $method;
        $raw = $method === 'POST';

        if (!$raw){
            Actions::add_action('title', [$this, 'title']);
            get_header();
        }
        ?>
        <main class="admin" data-title="<?php $this->title() ?>">
            <h2>Administratörsvy</h2>
            <table id="users">
                <thead>
                    <tr>
                        <th>Telefonnummer</th>
                        <th>Poäng</th>
                        <th>Admin</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $users = \PBCTF\LoginAPI::get_users();
                    foreach ($users as $user) {
                        $phone = $user['phone'];
                        $phone = '0' . substr($phone, 4);
                        // Formattera telefonnummer som 070-123 45 67
                        $phone = substr($phone, 0, 3) . '-' . substr($phone, 3, 3) . ' ' . substr($phone, 6, 2) . ' ' . substr($phone, 8);
                        ?>
                        <tr>
                            <td><?php echo $phone ?></td>
                            <td><?php echo $user['points'] ?></td>
                            <td><input type="checkbox" disabled name="admin" data-userid="<?php echo $user['_id'] ?>" <?php echo $user['admin'] ? 'checked' : '' ?>></td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>

            <h3>Skapa utmaning</h3>
            <form id="add-challenge">
                <label for="name">Namn</label><br>
                <input type="text" name="name" id="name" placeholder="Namn" required><br>
                <label for="description">Beskrivning</label><br>
                <textarea name="description" id="description" placeholder="Beskrivning" required></textarea><br>
                <label for="category">Kategori</label><br>
                <select name="category" id="category">
                    <option value="webb">Webb</option>
                    <option value="forensik">Forensik</option>
                    <option value="reverse">Reverse</option>
                    <option value="misc">Misc</option>
                </select><br>
                <label for="points">Poäng</label><br>
                <input type="number" name="points" id="points" placeholder="Poäng" required><br>
                <label for="flag">Flagga</label><br>
                <input type="text" name="flag" id="flag" placeholder="Flagga" required><br>
                <input type="submit" value="Skapa"><br>
            </form>

            <h3>Radera utmaning</h3>
            <form id="delete-challenge">
                <label for="challenge">Utmaning</label><br>
                <select name="challenge" id="challenge">
                    <?php
                    $challenges = \PBCTF\Challenges::get_challenges();
                    foreach ($challenges as $challenge) {
                        ?>
                        <option value="<?php echo $challenge['_id'] ?>"><?php echo $challenge['name'] ?></option>
                        <?php
                    }
                    ?>
                </select><br>
                <input type="submit" value="Radera"><br>
            </form>

            <script>
                const add_challenge = document.getElementById('add-challenge');

                add_challenge.addEventListener('submit', async e => {
                    e.preventDefault();

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

                const delete_challenge = document.getElementById('delete-challenge');

                delete_challenge.addEventListener('submit', async e => {
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
            </script>
        </main>
        <?php
        if (!$raw) {
            get_footer();
        }
    }
}

$page = new Admin();
$page->render();