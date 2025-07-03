<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\UserController; // ajuste si ton namespace est différent

class UserControllerTest extends TestCase
{
    private \PDO $pdo;
    private UserController $ctrl;

    protected function setUp(): void
    {
        // Base SQLite en mémoire
        $this->pdo = new \PDO('sqlite::memory:');
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        // Création de la table users
        $this->pdo->exec("
            CREATE TABLE users (
              id INTEGER PRIMARY KEY AUTOINCREMENT,
              name TEXT NOT NULL,
              email TEXT NOT NULL
            );
        ");
        $this->pdo->exec("INSERT INTO users (name,email) VALUES ('TestUser','test@example.com');");

        // Instanciation du contrôleur + injection du PDO
        $this->ctrl = new UserController();
        $ref = new \ReflectionClass($this->ctrl);
        $prop = $ref->getProperty('pdo');
        $prop->setAccessible(true);
        $prop->setValue($this->ctrl, $this->pdo);
    }

    protected function tearDown(): void
    {
        $this->pdo = null;
    }

    public function testGetAllUsers(): void
    {
        $json = $this->ctrl->getAllUsers();
        $data = json_decode($json, true);
        $this->assertCount(1, $data);
        $this->assertSame('TestUser', $data[0]['name']);
    }

    public function testGetUserById(): void
    {
        $json = $this->ctrl->getUser(1);
        $user = json_decode($json, true);
        $this->assertSame('TestUser', $user['name']);
    }

    public function testGetUserNotFound(): void
    {
        $json = $this->ctrl->getUser(999);
        $result = json_decode($json, true);
        $this->assertArrayHasKey('error', $result); // dépend de ton implémentation
    }

    public function testCreateUser(): void
    {
        $payload = ['name' => 'Alice', 'email' => 'alice@example.com'];
        $resp = json_decode($this->ctrl->createUser($payload), true);

        $this->assertSame('User created', $resp['message']);

        $row = $this->pdo->query("SELECT * FROM users WHERE email='alice@example.com'")
                         ->fetch(\PDO::FETCH_ASSOC);
        $this->assertSame('Alice', $row['name']);
    }

    public function testUpdateUser(): void
    {
        $payload = ['name' => 'UpdatedUser', 'email' => 'test@example.com'];
        $resp = json_decode($this->ctrl->updateUser(1, $payload), true);

        $this->assertSame('User updated', $resp['message']);

        $name = $this->pdo->query("SELECT name FROM users WHERE id = 1")->fetchColumn();
        $this->assertSame('UpdatedUser', $name);
    }

    public function testDeleteUser(): void
    {
        $resp = json_decode($this->ctrl->deleteUser(1), true);
        $this->assertSame('User deleted', $resp['message']);

        $count = $this->pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        $this->assertSame(0, (int)$count);
    }
}
