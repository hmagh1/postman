<?php
namespace App;

use PDO;
use PDOException;

class UserController {
    private PDO $pdo;

    public function __construct() {
        $retries = 5;
        while ($retries--) {
            try {
                $this->pdo = new PDO(
                    "mysql:host=db;dbname=crud;charset=utf8",
                    getenv('MYSQL_USER') ?: 'user',
                    getenv('MYSQL_PASSWORD') ?: 'userpass',
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );
                return;
            } catch (PDOException $e) {
                sleep(3);
            }
        }
        http_response_code(500);
        echo json_encode(["error" => "Database connection failed after multiple attempts"]);
        exit;
    }

    public function getAllUsers() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM users");
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return json_encode($users);
        } catch (PDOException $e) {
            return $this->errorResponse("Failed to fetch users", $e);
        }
    }

    public function getUser($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$user) {
                http_response_code(404);
                return json_encode(["message" => "User not found"]);
            }
            return json_encode($user);
        } catch (PDOException $e) {
            return $this->errorResponse("Failed to fetch user", $e);
        }
    }

    public function createUser($data) {
        if (empty($data['name']) || empty($data['email'])) {
            http_response_code(400);
            return json_encode(["error" => "Name and email are required"]);
        }

        try {
            $stmt = $this->pdo->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
            $stmt->execute([$data['name'], $data['email']]);
            http_response_code(201);
            return json_encode([
                "message" => "User created",
                "id"      => $this->pdo->lastInsertId()
            ]);
        } catch (PDOException $e) {
            return $this->errorResponse("Failed to create user", $e);
        }
    }

    public function updateUser($id, $data) {
        if (empty($data['name']) || empty($data['email'])) {
            http_response_code(400);
            return json_encode(["error" => "Name and email are required"]);
        }

        try {
            $stmt = $this->pdo->prepare(
                "UPDATE users SET name = ?, email = ? WHERE id = ?"
            );
            $stmt->execute([$data['name'], $data['email'], $id]);
            return json_encode(["message" => "User updated"]);
        } catch (PDOException $e) {
            return $this->errorResponse("Failed to update user", $e);
        }
    }

    public function deleteUser($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);
            return json_encode(["message" => "User deleted"]);
        } catch (PDOException $e) {
            return $this->errorResponse("Failed to delete user", $e);
        }
    }

    private function errorResponse(string $message, \Throwable $e) {
        http_response_code(500);
        return json_encode([
            "error"   => $message,
            "details" => $e->getMessage()
        ]);
    }
}
