<?php
include __DIR__ . '/includes/actions.php';

// Get route
$request = $_SERVER['REQUEST_URI'];
$route = explode('?', $request)[0];
$method = $_SERVER['REQUEST_METHOD'];

// Include page
switch (strToLower($route)) {
    case '/':
    case '/start':
        require __DIR__ . '/views/start.php';
        break;
    case '/utmaningar':
        require __DIR__ . '/views/utmaningar.php';
        break;
    default:
        require __DIR__ . '/views/404.php';
        break;
}

function get_header() {
    include __DIR__ . '/views/components/header.php';
}

function get_footer() {
    include __DIR__ . '/views/components/footer.php';
}