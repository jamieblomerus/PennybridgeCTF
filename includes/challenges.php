<?php
namespace PBCTF;

use SleekDB\Store;

/**
 * Klassen Challenges hanterar utmaningar.
 * 
 * @package PBCTF
 */
class Challenges {

    /**
     * Databasen för utmaningar
     */
    private static Store $store;

    /**
     * Initierar SleekDB-databasen för utmaningar.
     * 
     * @return void
     */
    public static function init(): void {
        self::$store = new Store('challenges', __DIR__ . '/../data', ["timeout" => false]);
    }

    /**
     * Hämtar alla utmaningar.
     * 
     * @return array Alla utmaningar som en array
     */
    public static function get_challenges(): array {
        return self::$store->findAll();
    }

    /**
     * Hämtar alla utmaningar i en viss kategori.
     * 
     * @param string $category Kategorin som utmaningarna ska hämtas från
     * @return array Alla utmaningar i kategorin som en array
     */
    public static function get_challenges_by_category(string $category): array {
        return self::$store->findBy(['category', '=', $category], ['points' => 'ASC']);
    }

    /**
     * Hämtar en utmaning baserat på dess ID.
     * 
     * @param int $id ID:t på utmaningen som ska hämtas
     * @return array|null Utmaningen som en array, eller null om den inte finns
     */
    public static function get_challenge(int $id): array|null {
        return self::$store->findById($id);
    }

    /**
     * Hanterar inlämning av flaggor via API-ändpunkten /api/solve_challenge.
     * 
     * @return void
     */
    public static function api_callback_solve_challenge(): void {
        if (!LoginAPI::is_logged_in()) {
            echo json_encode(['error' => 'Du måste vara inloggad för att lösa utmaningar']);
            exit;
        }

        $user = LoginAPI::get_user();

        $body = json_decode(file_get_contents('php://input'), true);

        $flag = $body['flag'] ?? null;
        $challenge_id = $body['challenge_id'] ?? null;

        if (!isset($flag) || !isset($challenge_id)) {
            echo json_encode(['error' => 'Alla fält måste vara ifyllda']);
            exit;
        }

        $challenge = self::$store->findById($challenge_id);

        if ($challenge_id === '0') {
            $challenge = [
                'name' => 'Easter egg',
                'flag' => 'PBCTF{easter_egg}',
                'points' => 50
            ];
        }

        if (!$challenge) {
            echo json_encode(['error' => 'Kunde inte hitta utmaning']);
            exit;
        }

        if ($challenge['flag'] !== $flag) {
            echo json_encode(['error' => 'Fel flagga']);
            exit;
        }

        if (in_array($challenge_id, $user['solved'])) {
            echo json_encode(['error' => 'Du har redan löst denna utmaning']);
            exit;
        }

        $user['solved'][] = $challenge_id;

        $user = Users::$store->updateById($user['_id'], ['solved' => $user['solved'], 'points' => $user['points'] + $challenge['points'] ]);

        if (!$user) {
            echo json_encode(['error' => 'Kunde inte uppdatera användare']);
            exit;
        }

        echo json_encode(['success' => true]);
    }

    /**
     * Hanterar API-anrop till /api/add_challenge.
     * 
     * @return void
     */
    public static function api_callback_add_challenge(): void {
        if (!LoginAPI::is_admin()) {
            echo json_encode(['error' => 'Du är ej behörig att lägga till utmaningar']);
        }

        $body = json_decode(file_get_contents('php://input'), true);
        $name = $body['name'] ?? null;
        $description = $body['description'] ?? null;
        $points = $body['points'] ?? null;
        $category = $body['category'] ?? null;
        $flag = $body['flag'] ?? null;

        if (!$name || !$description || !$points || !$category || !$flag) {
            echo json_encode(['error' => 'Alla fält måste vara ifyllda']);
            exit;
        }

        if (!preg_match('/^[0-9]+$/', $points)) {
            echo json_encode(['error' => 'Poäng måste vara ett heltal']);
            exit;
        }

        if (!in_array($category, ['webb', 'forensik', 'crypto', 'misc'])) {
            echo json_encode(['error' => 'Ogiltig kategori']);
            exit;
        }

        if (strpos($flag, 'PBCTF{') !== 0) {
            echo json_encode(['error' => 'Flaggan måste börja med <code>PBCTF{</code>']);
            exit;
        }

        if (strpos($flag, '}') !== strlen($flag) - 1) {
            echo json_encode(['error' => 'Flaggan måste sluta med <code>}</code>']);
            exit;
        }

        $challenge = self::$store->insert([
            'name' => $name,
            'description' => $description,
            'points' => $points,
            'category' => $category,
            'flag' => $flag
        ]);

        if (!$challenge) {
            echo json_encode(['error' => 'Kunde inte lägga till utmaning']);
            exit;
        }

        echo json_encode(['success' => true]);
        exit;
    }

    /**
     * Hanterar API-anrop till /api/delete_challenge.
     * 
     * @return void
     */
    public static function api_callback_delete_challenge(): void {
        if (!LoginAPI::is_admin()) {
            echo json_encode(['error' => 'Du är ej behörig att ta bort utmaningar']);
        }

        $body = json_decode(file_get_contents('php://input'), true);
        $id = $body['id'] ?? null;

        if (!$id) {
            echo json_encode(['error' => 'Alla fält måste vara ifyllda']);
            exit;
        }

        $challenge = self::$store->findOneBy(['_id', '=', (int)$id]);

        if (!$challenge) {
            echo json_encode(['error' => 'Kunde inte hitta utmaning']);
            exit;
        }

        $challenge = self::$store->deleteById($id);

        if (!$challenge) {
            echo json_encode(['error' => 'Kunde inte ta bort utmaning']);
            exit;
        }

        echo json_encode(['success' => true]);
        exit;
    }
}

Challenges::init();