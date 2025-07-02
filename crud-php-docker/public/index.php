<?php
require_once '../src/UserController.php';

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];
$path = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

$controller = new UserController();

if ($path[0] === 'users') {
    $id = $path[1] ?? null;

    switch ($method) {
        case 'GET':
            echo $id ? $controller->getUser($id) : $controller->getAllUsers();
            break;
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            echo $controller->createUser($data);
            break;
        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            echo $controller->updateUser($id, $data);
            break;
        case 'DELETE':
            echo $controller->deleteUser($id);
            break;
        default:
            http_response_code(405);
            echo json_encode(["message" => "Method not allowed"]);
    }
}
