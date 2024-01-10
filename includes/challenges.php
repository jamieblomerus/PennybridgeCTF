<?php
namespace PBCTF;

use SleekDB\Store;

class Challenges {
    private static Store $store;

    public static function init() {
        self::$store = new Store('challenges', __DIR__ . '/../data', ["timeout" => false]);
    }

    public static function get_challenges() {
        return self::$store->findAll();
    }

    public static function get_challenges_by_category(string $category) {
        return self::$store->findBy(['category', '=', $category], ['points' => 'ASC']);
    }

    public static function get_challenge(int $id) {
        return self::$store->findById($id);
    }

    public static function api_callback_add_challenge() {
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

        if (!in_array($category, ['webb', 'forensik', 'reverse', 'misc'])) {
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

    public static function api_callback_delete_challenge() {
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