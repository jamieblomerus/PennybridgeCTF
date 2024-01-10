<?php
session_start();
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/includes/challenges.php';
require __DIR__ . '/includes/actions.php';
require __DIR__ . '/includes/account.php';

// Get route
$request = $_SERVER['REQUEST_URI'];
$route = explode('?', $request)[0];
$method = $_SERVER['REQUEST_METHOD'];

// Include page
switch (strToLower($route)) {
    case '/':
    case '/start':
        require_once __DIR__ . '/views/start.php';
        break;
    case '/inloggning':
        require_once __DIR__ . '/views/login.php';
        break;
    case '/inloggning/kod':
        require_once __DIR__ . '/views/login_code.php';
        break;
    case '/utloggning':
        session_unset();
        session_destroy();
        header('Location: /');
        exit;
        break;
    case '/utmaningar':
        require_once __DIR__ . '/views/utmaningar.php';
        break;
    case '/admin':
        if (PBCTF\LoginAPI::is_admin()) {
            require_once __DIR__ . '/views/admin.php';
        } else {
            require_once __DIR__ . '/views/404.php';
        }
        break;
    case '/api/login':
        require_once __DIR__ . '/includes/account.php';
        PBCTF\LoginAPI::api_callback();
        break;
    case '/api/add_challenge':
        PBCTF\Challenges::api_callback_add_challenge();
        break;
    case '/api/delete_challenge':
        PBCTF\Challenges::api_callback_delete_challenge();
        break;
    default:
        if (preg_match('/^\/utmaningar\/[a-z]+\/[0-9]+$/', $route)) {
            require_once __DIR__ . '/views/utmaning.php';
        } else {
            require_once __DIR__ . '/views/404.php';
        }
        break;
}

function get_header() {
    include __DIR__ . '/views/components/header.php';
}

function get_footer() {
    include __DIR__ . '/views/components/footer.php';
}