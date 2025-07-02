<?php

require_once __DIR__ . '/../src/Database/Database.php';
require_once __DIR__ . '/../src/Models/Contact.php';
require_once __DIR__ . '/../src/Controllers/ContactController.php';

use App\Controllers\ContactController;

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle CORS preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    http_response_code(200);
    exit;
}

// Parse the URL path
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = str_replace('/api', '', $path);
$path = trim($path, '/');

// Route the request
try {
    $controller = new ContactController();
    
    if ($path === 'contacts' || $path === 'contacts/') {
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                $controller->index();
                break;
            case 'POST':
                $controller->store();
                break;
            default:
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
                break;
        }
    } elseif (preg_match('/^contacts\/(\d+)$/', $path, $matches)) {
        $id = (int) $matches[1];
        
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'DELETE':
                $controller->destroy($id);
                break;
            default:
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
                break;
        }
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint not found']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error: ' . $e->getMessage()]);
} 