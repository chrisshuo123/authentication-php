<?php
class UserRepository {

    public function __construct(private PDO $pdo) {}

    public function findByEmail(string $email): array|false {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }

    public function createUser(string $email, string $name, string $password, string $createdAt): void {
        $stmt = $this->pdo->prepare(
            "INSERT INTO users (email, name, password, created_at)
            VALUES (:email, :name, :password, :created_at)"
        );
        $stmt->execute([
            'email' => $email,
            'name' => $name,
            'password' => $password,
            'created_at' => $createdAt,
        ]);
        // return $stmt->fetch();
    }
}