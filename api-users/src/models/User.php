<?php

class User {

    // Columnas públicas: el campo password NUNCA se devuelve en las respuestas
    private const PUBLIC_COLUMNS = 'id, name, email, created_at';

    public function __construct(private PDO $db) {}

    public function getAll(): array {

        $stmt = $this->db->query(
            "SELECT " . self::PUBLIC_COLUMNS . " FROM users ORDER BY id"
        );

        return $stmt->fetchAll();
    }

    public function getById(int $id): array|false {

        $stmt = $this->db->prepare(
            "SELECT " . self::PUBLIC_COLUMNS . " FROM users WHERE id = ?"
        );

        $stmt->execute([$id]);

        return $stmt->fetch();
    }

    /**
     * Verifica si un email ya está registrado.
     * $exceptId permite ignorar al propio usuario al actualizar.
     */
    public function emailExists(string $email, ?int $exceptId = null): bool {

        $sql = "SELECT COUNT(*) FROM users WHERE email = ?";
        $params = [$email];

        if ($exceptId !== null) {
            $sql .= " AND id <> ?";
            $params[] = $exceptId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn() > 0;
    }

    public function create(array $data): int {

        $stmt = $this->db->prepare(
            "INSERT INTO users (name, email, password)
             VALUES (?, ?, ?)"
        );

        $stmt->execute([
            $data['name'],
            $data['email'],
            password_hash($data['password'], PASSWORD_BCRYPT)
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): int {

        // Si se envía password, se actualiza (encriptada); si no, se conserva la actual
        if (!empty($data['password'])) {

            $stmt = $this->db->prepare(
                "UPDATE users
                 SET name = ?, email = ?, password = ?
                 WHERE id = ?"
            );

            $stmt->execute([
                $data['name'],
                $data['email'],
                password_hash($data['password'], PASSWORD_BCRYPT),
                $id
            ]);

        } else {

            $stmt = $this->db->prepare(
                "UPDATE users
                 SET name = ?, email = ?
                 WHERE id = ?"
            );

            $stmt->execute([
                $data['name'],
                $data['email'],
                $id
            ]);
        }

        return $stmt->rowCount();
    }

    public function delete(int $id): int {

        $stmt = $this->db->prepare(
            "DELETE FROM users WHERE id = ?"
        );

        $stmt->execute([$id]);

        return $stmt->rowCount();
    }
}
