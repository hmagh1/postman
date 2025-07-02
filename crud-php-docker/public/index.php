<?php

require_once '/var/www/src/UserController.php';

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];
$path = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

$controller = new UserController();

// Initialiser Memcached
$memcached = new Memcached();
$memcached->addServer('memcached', 11211);

// Point d’entrée de l’API
if ($path[0] === 'users') {
    $id = $path[1] ?? null;

    switch ($method) {
        case 'GET':
            if ($id) {
                echo $controller->getUser($id);
            } else {
                // Vérifier si les utilisateurs sont déjà en cache
                $cached = $memcached->get('all_users');
                if ($cached === false) {
                    $response = $controller->getAllUsers();
                    $memcached->set('all_users', $response, 300); // 5 minutes
                    echo $response;
                } else {
                    echo $cached;
                }
            }
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $memcached->delete('all_users'); // Invalider le cache
            echo $controller->createUser($data);
            break;

        case 'PUT':
            if (!$id) {
                http_response_code(400);
                echo json_encode(["message" => "User ID is required"]);
                break;
            }
            $data = json_decode(file_get_contents('php://input'), true);
            $memcached->delete('all_users'); // Invalider le cache
            echo $controller->updateUser($id, $data);
            break;

        case 'DELETE':
            if (!$id) {
                http_response_code(400);
                echo json_encode(["message" => "User ID is required"]);
                break;
            }
            $memcached->delete('all_users'); // Invalider le cache
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
