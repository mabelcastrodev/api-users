<?php

class Product {

    public function __construct(private PDO $db) {}

    public function getAll(): array {

        $stmt = $this->db->query(
            "SELECT * FROM productos"
        );

        return $stmt->fetchAll();
    }

    public function create(array $data): int {

        $stmt = $this->db->prepare(
            "INSERT INTO productos (nombre, precio, stock)
             VALUES (?, ?, ?)"
        );

        $stmt->execute([
            $data['nombre'],
            $data['precio'],
            $data['stock']
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function getById(int $id): array|false {

        $stmt = $this->db->prepare(
            "SELECT * FROM productos WHERE id = ?"
        );

        $stmt->execute([$id]);

        return $stmt->fetch();
    }

    public function update(int $id, array $data): int {

        $stmt = $this->db->prepare(
            "UPDATE productos
             SET nombre = ?, precio = ?, stock = ?
             WHERE id = ?"
        );

        $stmt->execute([
            $data['nombre'],
            $data['precio'],
            $data['stock'],
            $id
        ]);

        return $stmt->rowCount();
    }

    public function delete(int $id): int {

        $stmt = $this->db->prepare(
            "DELETE FROM productos WHERE id = ?"
        );

        $stmt->execute([$id]);

        return $stmt->rowCount();
    }
}