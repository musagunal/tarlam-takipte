<?php

class User
{
    public static function findById(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    public static function findByEmail(string $email): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    public static function create(string $username, string $email, string $password): int
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO users (username, email, password) VALUES (:username, :email, :password)'
        );

        $stmt->execute([
            'username' => $username,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        return (int) Database::connection()->lastInsertId();
    }

    public static function updatePassword(int $id, string $password): void
    {
        $stmt = Database::connection()->prepare('UPDATE users SET password = :password WHERE id = :id');
        $stmt->execute([
            'id' => $id,
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ]);
    }
}
