<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../login.php';

class LoginTest extends TestCase
{
    private $pdo;

    protected function setUp(): void
    {
        // Use in-memory SQLite DB for testing
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Create fake users table
        $this->pdo->exec("CREATE TABLE users (id INTEGER PRIMARY KEY, email TEXT, password_hash TEXT)");

        // Insert one test user
        $password = hash('sha256', 'mypassword');
        $stmt = $this->pdo->prepare("INSERT INTO users (email, password_hash) VALUES (?, ?)");
        $stmt->execute(['test@example.com', $password]);
    }

    public function testValidLogin()
    {
        $this->assertTrue(check_login($this->pdo, 'test@example.com', 'mypassword'));
    }

    public function testInvalidLoginWrongPassword()
    {
        $this->assertFalse(check_login($this->pdo, 'test@example.com', 'wrongpassword'));
    }

    public function testInvalidLoginUnknownUser()
    {
        $this->assertFalse(check_login($this->pdo, 'unknown@example.com', 'mypassword'));
    }
}