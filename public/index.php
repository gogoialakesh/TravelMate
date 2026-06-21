<?php
/**
 * TravelMate - Application Entry Point
 *
 * All requests are routed through this file via .htaccess.
 * This file:
 *   1. Loads configuration and helpers
 *   2. Starts a secure session
 *   3. Resolves the URL to a controller/method
 *   4. Dispatches the request
 */

// ============================================================
// 1. Bootstrapping
// ============================================================

// Load application configuration
require_once dirname(__DIR__) . '/config/app.php';
require_once CONFIG_PATH . '/database.php';

// Load helpers
require_once APP_PATH . '/helpers/Security.php';
require_once APP_PATH . '/helpers/Logger.php';
require_once APP_PATH . '/helpers/Validator.php';
require_once APP_PATH . '/helpers/FileUpload.php';

// Load models
require_once APP_PATH . '/models/User.php';
require_once APP_PATH . '/models/Trip.php';
require_once APP_PATH . '/models/TripMember.php';
require_once APP_PATH . '/models/Responsibility.php';
require_once APP_PATH . '/models/Resource.php';
require_once APP_PATH . '/models/ResourceAssignment.php';
require_once APP_PATH . '/models/Expense.php';
require_once APP_PATH . '/models/Message.php';
require_once APP_PATH . '/models/Album.php';
require_once APP_PATH . '/models/Media.php';
require_once APP_PATH . '/models/Review.php';
require_once APP_PATH . '/models/Notification.php';

// Load services (NotificationService first — depended on by others)
require_once APP_PATH . '/services/NotificationService.php';
require_once APP_PATH . '/services/AuthService.php';
require_once APP_PATH . '/services/UserService.php';
require_once APP_PATH . '/services/TripService.php';
require_once APP_PATH . '/services/ResponsibilityService.php';
require_once APP_PATH . '/services/ResourceService.php';
require_once APP_PATH . '/services/ExpenseService.php';
require_once APP_PATH . '/services/AlbumService.php';
require_once APP_PATH . '/services/ReviewService.php';

// Load controllers
require_once APP_PATH . '/controllers/HomeController.php';
require_once APP_PATH . '/controllers/AuthController.php';
require_once APP_PATH . '/controllers/DashboardController.php';
require_once APP_PATH . '/controllers/UserController.php';
require_once APP_PATH . '/controllers/TripController.php';
require_once APP_PATH . '/controllers/ResponsibilityController.php';
require_once APP_PATH . '/controllers/ResourceController.php';
require_once APP_PATH . '/controllers/ChatController.php';
require_once APP_PATH . '/controllers/ExpenseController.php';
require_once APP_PATH . '/controllers/AlbumController.php';
require_once APP_PATH . '/controllers/ReviewController.php';
require_once APP_PATH . '/controllers/NotificationController.php';

// Initialize logger
Logger::init();

// Start secure session
Security::startSession();

// ============================================================
// 2. Route Resolution
// ============================================================

// Get the request URI (strip query string)
$requestUri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = strtoupper($_SERVER['REQUEST_METHOD']);

// Normalize URI — strip base path prefix
// When accessed via root .htaccess redirect, URI starts with /travelmate (not /travelmate/public)
$basePaths = ['/travelmate/public', '/travelmate'];
foreach ($basePaths as $basePath) {
    if (strpos($requestUri, $basePath) === 0) {
        $requestUri = substr($requestUri, strlen($basePath));
        break;
    }
}

// Ensure leading slash, handle empty
if (empty($requestUri)) {
    $requestUri = '/';
}

// Load route definitions
$routes = require ROUTES_PATH . '/web.php';

// ============================================================
// 3. Route Matching with Dynamic Parameter Support
// ============================================================

$matched    = false;
$params     = [];

foreach ($routes as $routeKey => $handler) {
    // Split route key into method and path (e.g., "GET /trips/{id}")
    [$routeMethod, $routePath] = explode(' ', $routeKey, 2);

    if ($routeMethod !== $requestMethod) {
        continue;
    }

    // Convert {param} placeholders to named regex groups
    $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $routePath);
    $pattern = '#^' . $pattern . '$#';

    if (preg_match($pattern, $requestUri, $matches)) {
        // Extract named parameters
        foreach ($matches as $key => $value) {
            if (is_string($key)) {
                $params[$key] = $value;
            }
        }

        [$controllerClass, $method] = $handler;

        // Dispatch
        if (class_exists($controllerClass)) {
            $controller = new $controllerClass();
            if (method_exists($controller, $method)) {
                $controller->$method($params);
            } else {
                http_response_code(500);
                echo "Controller method not found: {$controllerClass}::{$method}";
            }
        } else {
            http_response_code(500);
            echo "Controller not found: {$controllerClass}";
        }

        $matched = true;
        break;
    }
}

// ============================================================
// 4. 404 Handler
// ============================================================

if (!$matched) {
    http_response_code(404);
    $pageTitle = '404 Not Found';
    require_once VIEWS_PATH . '/layouts/header.php';
    ?>
    <div class="container mt-5 text-center">
        <div class="py-5">
            <div class="display-1 fw-bold text-primary">404</div>
            <h2 class="mb-3">Page Not Found</h2>
            <p class="text-muted mb-4">The page you're looking for doesn't exist or has been moved.</p>
            <a href="<?= BASE_URL ?>/" class="btn btn-primary btn-lg">
                <i class="bi bi-house me-2"></i>Go Home
            </a>
        </div>
    </div>
    <?php
    require_once VIEWS_PATH . '/layouts/footer.php';
}
