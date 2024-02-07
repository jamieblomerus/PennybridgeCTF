<?php
namespace PBCTF;

use SleekDB\Store;

/**
 * LoginAPI hanterar inloggning och utloggning av användare, samt erbjuder stödfunktioner kring nuvarande användare.
 * 
 * @package PBCTF
 */
class LoginAPI {

    /**
     * Hanterar inloggningsförfrågan.
     */
    public static function api_callback() {
        global $method;

        if ($method !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Metoden är ej tillåten']);
            exit;
        }

        global $phone;
        if (isset($_POST['phone'])) {
            $phone = $_POST['phone'];
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Inget telefonnummer skickat']);
            exit;
        }

        if (!self::login($phone)) {
            http_response_code(400);
            echo json_encode(['error' => 'Ogiltigt telefonnummer']);
            exit;
        }

        return true;
        exit;
    }

    /**
     * Verifierar en inloggningskod som skickats per SMS.
     * 
     * @param string $phone Telefonnummer i formatet 00467...
     * @param string $code Inloggningskod
     * @return bool True om inloggningskoden var giltig, annars false
     */
    public static function verify_code(string $phone, string $code): bool {
        $user = Users::get_user_by_phone($phone);

        if (!$user) {
            return false;
        }

        if ($user['code'] != $code) {
            return false;
        }

        if ($user['code_expires'] < time()) {
            return false;
        }

        $user = Users::$store->updateById($user['_id'], [
            'code' => null,
            'code_expires' => null
        ]);

        if (!$user) {
            return false;
        }

        return true;
    }

    /**
     * Hanterar inloggningen av användare.
     * 
     * @param string $phone Telefonnummer i formatet 00467...
     * @return bool True om inloggningen lyckades, annars false
     */
    public static function login(string $phone): bool {
        global $phone;
        // Kontrollera format
        if (!preg_match('/^07[0-9]{8}$/', $phone)) {
            return false;
        }

        // Konvertera till format 00467...
        $phone = '0046' . substr($phone, 1);

        $user = Users::get_user_by_phone($phone);

        if (!$user) {
            $user = Users::new_user($phone);

            if (!$user) {
                return false;
            }
        }

        $code = rand(100000, 999999); // rand() är tyvärr ibland förutsägbar men jag har inte tid för en bättre lösning just nu

        $user = Users::$store->updateById($user['_id'], [
            'code' => $code,
            'code_expires' => time() + 60 * 5
        ]);

        if (!$user) {
            return false;
        }

        $message = "Hej! Din inloggningskod är $code. Den är giltig i 5 minuter.";

        if (!Cellsynt::send_sms($phone, $message)) {
            return false;
        }

        return true;
    }

    /**
     * Kollar om användaren är inloggad.
     */
    public static function is_logged_in() {
        return isset($_SESSION['phone']);
    }

    /**
     * Returnerar den inloggade användaren.
     * 
     * @return array|false Användaren som en array eller false om användaren inte är inloggad
     */
    public static function get_user(): array|false {
        if (!self::is_logged_in()) {
            return false;
        }

        $user = Users::$store->findOneBy(['phone', '=', $_SESSION['phone']]);

        if (!$user) {
            return false;
        }

        return $user;
    }

    /**
     * Kontrollerar om användaren är administratör.
     * 
     * @return bool True om användaren är administratör, annars false
     */
    public static function is_admin(): bool {
        $user = self::get_user();

        if (!$user) {
            return false;
        }

        return $user['admin'] === true;
    }

    /**
     * Hämtar alla användare.
     * 
     * @return array Alla användare som en array
     */
    public static function get_users(): array {
        return Users::$store->findAll();
    }
}

/**
 * Users hanterar användare i databasen.
 * 
 * @package PBCTF
 */
class Users {

    /**
     * Databasen för användare
     */
    public static Store $store;

    /**
     * Initierar SleekDB-databasen för användare.
     * 
     * @return void
     */
    public static function init(): void {
        self::$store = new Store('users', __DIR__ . '/../data', ["timeout" => false, "auto_cache" => false]);
    }

    /**
     * Denna funktion hämtar en användare från databasen baserat på dess telefonnummer.
     * 
     * @param string $phone Telefonnummer i formatet 00467...
     * @return array|false Användaren som en array eller false om användaren inte finns
     */
    public static function get_user_by_phone(string $phone) {
        $user = self::$store->findOneBy(['phone', '=', $phone]);

        if (!$user) {
            return false;
        }

        return $user;
    }

    /**
     * Denna funktion skapar en ny användare i databasen.
     * 
     * @param string $phone Telefonnummer i formatet 00467...
     * @return array|false Användaren som en array eller false om användaren inte kunde skapas
     */
    public static function new_user(string $phone) {
        $user = self::$store->insert([
            'phone' => $phone,
            'admin' => false,
            'points' => 0,
            'solved' => [],
            'code' => null,
            'code_expires' => null
        ]);

        if (!$user) {
            return false;
        }

        return $user;
    }
    
    /**
     * Denna funktion ställer in smeknamn på en användare efter anrop till /api/set_nickname.
     * 
     * @param string $nickname Nytt smeknamn
     * @return bool True om smeknamnet var giltigt och kunde sättas, annars false
     */
    public static function set_nickname(string $nickname): bool {
        if (!LoginAPI::is_logged_in()) {
            return false;
        }

        if (strlen($nickname) < 3 || strlen($nickname) > 16) {
            return false;
        }

        // Enbart bokstäver, siffror, mellanslag och understreck
        if (!preg_match('/^[a-z0-9_ ]+$/i', $nickname)) {
            return false;
        }

        // Kontrollera att smeknamnet inte redan används
        $user = self::$store->findOneBy(['nickname', '=', $nickname]);
        if ($user) {
            return false;
        }

        $user = self::$store->updateById(LoginAPI::get_user()['_id'], [
            'nickname' => $nickname
        ]);

        if (!$user) {
            return false;
        }

        return true;
    }

    /**
     * Denna funktion hanterar API-anrop till /api/set_nickname.
     */
    public static function api_callback_set_nickname() {
        global $method;

        if ($method !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Metoden är ej tillåten']);
            exit;
        }

        if (!LoginAPI::is_logged_in()) {
            http_response_code(401);
            echo json_encode(['error' => 'Du är inte inloggad']);
            exit;
        }

        if (isset(LoginAPI::get_user()['nickname'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Du har redan valt ett smeknamn']);
            exit;
        }

        $body = json_decode(file_get_contents('php://input'), true);
        if (isset($body) && isset($body['nickname'])) {
            $nickname = $body['nickname'];
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Inget smeknamn skickat']);
        }

        if (!self::set_nickname($nickname)) {
            http_response_code(400);
            echo json_encode(['error' => 'Smeknamnet är ogiltigt eller upptaget']);
            exit;
        }

        echo json_encode(['success' => true]);
        exit;
    }
}

/**
 * Cellsynt hanterar SMS och denna klass används således för att skicka SMS.
 * 
 * @see https://www.cellsynt.com/pdf/Cellsynt_SMS_gateway_HTTP_interface_(Swedish).pdf
 * @package PBCTF
 */
class Cellsynt {
    
    /**
     * Användarnamn för Cellsynt API
     */
    const USERNAME = '';

    /**
     * Lösenord för Cellsynt API
     */
    const PASSWORD = '';

    /**
     * Denna funktion skickar ett SMS till ett telefonnummer.
     * 
     * @param string $phone Telefonnummer i formatet 00467...
     * @param string $message Meddelandet som ska skickas
     * @return bool True om SMS:et skickades, annars false
     */
    public static function send_sms(string $phone, string $message) {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://se-1.cellsynt.net/sms.php?username=" . urlencode(self::USERNAME) . "&password=" . urlencode(self::PASSWORD) . "&destination=" . urlencode($phone) . "&type=text&originatortype=alpha&originator=PBCTF&charset=UTF-8&text=" . urlencode($message) . "&expiry=" . time() + 60 * 5,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET"
        ]);

        curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return false;
        }

        return true;
    }
}

Users::init();
?>
