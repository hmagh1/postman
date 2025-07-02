<?php

class UserController {
    private $pdo;

    public function __construct() {
        $this->pdo = new PDO("mysql:host=db;dbname=crud", "user", "userpass");
    }

    public function getAllUsers() {
        $stmt = $this->pdo->query("SELECT * FROM users");
        return json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function getUser($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return json_encode($stmt->fetch(PDO::FETCH_ASSOC));
    }

    public function createUser($data) {
        $stmt = $this->pdo->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
        $stmt->execute([$data['name'], $data['email']]);
        return json_encode(["message" => "User created"]);
    }

    public function updateUser($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        $stmt->execute([$data['name'], $data['email'], $id]);
        return json_encode(["message" => "User updated"]);
    }

    public function deleteUser($id) {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return json_encode(["message" => "User deleted"]);
    }
}
