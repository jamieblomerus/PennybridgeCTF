<?php
session_start();
require __DIR__ . '/vendor/autoload.php';
include __DIR__ . '/includes/actions.php';
include __DIR__ . '/includes/account.php';

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
    case '/api/login':
        require_once __DIR__ . '/includes/account.php';
        PBCTF\LoginAPI::api_callback();
        break;
    default:
        require_once __DIR__ . '/views/404.php';
        break;
}

function get_header() {
    include __DIR__ . '/views/components/header.php';
}

function get_footer() {
    include __DIR__ . '/views/components/footer.php';
}