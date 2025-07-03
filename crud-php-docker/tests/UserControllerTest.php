<?php

namespace App;

class UserController
{
    protected \PDO $pdo;

    public function getAllUsers(): string
    {
        $stmt = $this->pdo->query("SELECT * FROM users");
        return json_encode($stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    public function getUser(int $id): string
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$user) {
            return json_encode(['error' => 'User not found']);
        }

        return json_encode($user);
    }

    public function createUser(array $data): string
    {
        $stmt = $this->pdo->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
        $stmt->execute([$data['name'], $data['email']]);
        return json_encode(['message' => 'User created']);
    }

    public function updateUser(int $id, array $data): string
    {
        $stmt = $this->pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        $stmt->execute([$data['name'], $data['email'], $id]);
        return json_encode(['message' => 'User updated']);
    }

    public function deleteUser(int $id): string
    {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return json_encode(['message' => 'User deleted']);
    }
}
