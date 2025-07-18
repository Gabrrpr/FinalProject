<?php
// Simple router for the voting system

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Normalize base path for subdirectory deployments
$base = '/FinalProject/public';
if (strpos($uri, $base) === 0) {
    $uri = substr($uri, strlen($base));
    if ($uri === '' || $uri === false) $uri = '/';
}

switch ($uri) {
    case '/':
    case '/index.php':
        require_once __DIR__ . '/../src/pages/home.php';
        break;
    case '/login':
        require_once __DIR__ . '/../src/pages/login.php';
        break;
    case '/logout':
        require_once __DIR__ . '/../src/pages/logout.php';
        break;
    case '/register':
        require_once __DIR__ . '/../src/pages/register.php';
        break;
    case '/dashboard':
        require_once __DIR__ . '/../src/pages/dashboard.php';
        break;
    case '/admin':
        require_once __DIR__ . '/../src/pages/admin.php';
        break;
    case '/vote':
        require_once __DIR__ . '/../src/pages/vote.php';
        break;
    case '/results':
        require_once __DIR__ . '/../src/pages/results.php';
        break;
    default:
        http_response_code(404);
        echo '404 Not Found';
        break;
}
