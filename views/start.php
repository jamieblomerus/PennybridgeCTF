<?php
namespace PBCTF\Views;

use PBCTF\Actions;

class Home {
    public function title() {
        echo 'Pennybridge CTF';
    }

    public function render() {
        global $method;
        $raw = $method === 'POST';

        if (!$raw){
            Actions::add_action('title', [$this, 'title']);
            get_header();
        }
        ?>
        <main class="home">
            <div>
                <h2>Välkommen till Pennybridge CTF!</h1>
                <p>Välkommen till Pennybridge CTF, en CTF som är skapad för att lära ut grunderna i IT-säkerhet. Till skillnad från andra CTFs är denna skapad att vara nybörjarvänlig och ej kräva några särskilda förkunskaper. Lycka till!</p>
            </div>
            <div>
                <h2>Frågor och svar</h2>
                <div class="faq-answer">
                    <h3>Vad är en CTF?</h3>
                    <blockquote>
                        <p>CTF står för Capture The Flag och är en tävlingsform där deltagarna får visa upp sina kunskaper inom de tekniska bitarna av IT-säkerhet för att lösa uppgifter och hacka system.<br><br>Det vanligaste formatet för en CTF är Jeopardy. I början på en CTF-tävling i Jeopardy-format får deltagarna tillgång till en mängd hacknings-relaterade uppgifter. Varje uppgift går ut på att hitta en så kallad "flagga", vilket är en textsträng som representerar någon typ av dold eller hemlig information. När man hittar flaggan till en uppgift skickas den in till CTF-plattformen och deltagaren får poäng. Det lag som har flest poäng i slutet av tävlingen vinner.</p>
                        <footer>— <cite><a href="https://capturetheflag.se/om/">capturetheflag.se</a></cite></footer>
                    </blockquote>
                </div>
                <div class="faq-answer" id="regler">
                    <h3>Finns det några regler?</h3>
                    <p>För att spelet ska vara rättvist och roligt för alla så finns det några regler som måste följas:</p>
                    <ul>
                        <li>Det är inte tillåtet att attackera CTF-plattformen eller andra deltagare.</li>
                        <li>Det är inte tillåtet att utföra några DoS eller bruteforce-attacker mot några utmaningar eller CTF-plattformen.</li>
                        <li>Det är inte tillåtet att modifiera eller dela lösningar till utmaningar.</li>
                        <li>Det är inte tillåtet att använda artificiell intelligens eller maskininlärning för att lösa utmaningar.</li>
                    </ul>
                    <p>Om någon av dessa regler bryts kan det leda till diskvalificering eller i värsta fall polisanmälan.</p>
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