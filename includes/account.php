<?php
namespace PBCTF;

use SleekDB\Store;

class LoginAPI {
    public static function api_callback() {
        global $method;

        if ($method !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Metoden är ej tillåten']);
            exit;
        }

        $body = json_decode(file_get_contents('php://input'), true);
        global $phone;
        if (isset($body)) {
            $phone = $body['phone'];
        } else {
            $phone = $_POST['phone'];
        }

        if (!self::login($phone)) {
            http_response_code(400);
            echo json_encode(['error' => 'Ogiltigt telefonnummer']);
            exit;
        }

        if (isset($body)) {
            echo json_encode(['success' => true]);
        } else {
            return true;
        }
        exit;
    }
    public static function verify_code(string $phone, string $code) {
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
    public static function login(string $phone) {
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

        $code = rand(100000, 999999); // Rand är tyvärr ibland förutsägbar men jag har inte tid för en bättre lösning just nu

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
    public static function is_logged_in() {
        return isset($_SESSION['phone']);
    }
    public static function get_user() {
        if (!self::is_logged_in()) {
            return false;
        }

        $user = Users::$store->findOneBy(['phone', '=', $_SESSION['phone']]);

        if (!$user) {
            return false;
        }

        return $user;
    }
    public static function is_admin() {
        $user = self::get_user();

        if (!$user) {
            return false;
        }

        return $user['admin'] === true;
    }
    public static function get_users() {
        if (!self::is_admin()) {
            return false;
        }

        return Users::$store->findAll();
    }
}

class Users {
    public static Store $store;

    public static function init() {
        self::$store = new Store('users', __DIR__ . '/../data', ["timeout" => false, "auto_cache" => false]);
    }

    public static function get_user_by_phone(string $phone) {
        $user = self::$store->findOneBy(['phone', '=', $phone]);

        if (!$user) {
            return false;
        }

        return $user;
    }

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

    public static function set_nickname(string $nickname) {
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
        global $nickname;
        if (isset($body)) {
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

class Cellsynt {
    const USERNAME = 'webbstartnu';
    const PASSWORD = 'ST7c8fFQ';

    public static function send_sms(string $phone, string $message) {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://se-1.cellsynt.net/sms.php?username=" . urlencode(self::USERNAME) . "&password=" . urlencode(self::PASSWORD) . "&destination=" . urlencode($phone) . "&type=text&originatortype=alpha&originator=PBCTF&charset=UTF-8&text=" . urlencode($message),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET"
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            error_log("cURL Error #:" . $err);
            return false;
        }

        return true;
    }
}

Users::init();
?>