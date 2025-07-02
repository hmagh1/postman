<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use App\UserController;  // si ton namespace est différent, ajuste-le ici

class UserControllerTest extends TestCase
{
    private \PDO $pdo;
    private UserController $ctrl;

    protected function setUp(): void
    {
        // 1) Base SQLite en mémoire
        $this->pdo = new \PDO('sqlite::memory:');
        $this->pdo->exec("
            CREATE TABLE users (
              id INTEGER PRIMARY KEY AUTOINCREMENT,
              name TEXT NOT NULL,
              email TEXT NOT NULL
            );
        ");
        $this->pdo->exec("INSERT INTO users (name,email) VALUES ('TestUser','test@example.com');");

        // 2) Instancie le controller et injecte le PDO
        $this->ctrl = new UserController();
        $ref = new \ReflectionClass($this->ctrl);
        $prop = $ref->getProperty('pdo');
        $prop->setAccessible(true);
        $prop->setValue($this->ctrl, $this->pdo);
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

    public function testCreateUser(): void
    {
        $payload = ['name'=>'Alice','email'=>'alice@example.com'];
        $resp = json_decode($this->ctrl->createUser($payload), true);
        $this->assertSame('User created', $resp['message']);
        // Vérifie en base
        $row = $this->pdo->query("SELECT * FROM users WHERE email='alice@example.com'")
                        ->fetch(\PDO::FETCH_ASSOC);
        $this->assertSame('Alice', $row['name']);
    }
}
