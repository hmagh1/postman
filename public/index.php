<?php

require_once __DIR__ . '/../src/UserController.php';

use App\UserController;

// Réponse JSON systématique
header("Content-Type: application/json");

// Récupère la méthode et le chemin
$method = $_SERVER['REQUEST_METHOD'];
$path   = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

$controller = new UserController();

// Initialise Memcached
$memcached = new Memcached();
$memcached->addServer('memcached', 11211);

// Routage basique
if ($path[0] === 'users') {
    $id = $path[1] ?? null;

    switch ($method) {
        case 'GET':
            if ($id) {
                echo $controller->getUser($id);
            } else {
                // Cache GET /users
                $cacheKey = 'all_users';
                $cached   = $memcached->get($cacheKey);
                if ($cached === false) {
                    $response = $controller->getAllUsers();
                    $memcached->set($cacheKey, $response, 300);
                    header('X-Cache: MISS');
                    echo $response;
                } else {
                    header('X-Cache: HIT');
                    echo $cached;
                }
            }
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $memcached->delete('all_users');
            echo $controller->createUser($data);
            break;

        case 'PUT':
            if (!$id) {
                http_response_code(400);
                echo json_encode(["message" => "User ID is required"]);
                break;
            }
            $data = json_decode(file_get_contents('php://input'), true);
            $memcached->delete('all_users');
            echo $controller->updateUser($id, $data);
            break;

        case 'DELETE':
            if (!$id) {
                http_response_code(400);
                echo json_encode(["message" => "User ID is required"]);
                break;
            }
            $memcached->delete('all_users');
            echo $controller->deleteUser($id);
            break;

        default:
            http_response_code(405);
            echo json_encode(["message" => "Method not allowed"]);
            break;
    }
} else {
    http_response_code(404);
    echo json_encode(["message" => "Endpoint not found"]);
}
