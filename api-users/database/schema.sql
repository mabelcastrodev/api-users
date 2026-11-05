-- =============================================================
-- Base de datos para api-users (MySQL / MariaDB)
-- =============================================================

CREATE DATABASE IF NOT EXISTS dbisildemo
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE dbisildemo;

-- -------------------------------------------------------------
-- Tabla: users
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    name        VARCHAR(100)  NOT NULL,
    email       VARCHAR(150)  NOT NULL,
    password    VARCHAR(255)  NOT NULL,          -- hash bcrypt (password_hash)
    created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Datos de prueba
-- Contraseñas en texto plano: secret123 y clave456 (guardadas como hash bcrypt)
INSERT INTO users (name, email, password) VALUES
    ('Ana Torres',   'ana.torres@isil.pe',   '$2y$10$YdCkWqn14xGwZoIsLUdLCe9lgDpThvHt4nLarBxo35GaQWY7XRaPu'),
    ('Luis Ramírez', 'luis.ramirez@isil.pe', '$2y$10$ruv9QRvek490ygiFJKUa6OWD5Y5jSQjq.P8BZuQilLeTp86dxbCzG');

-- -------------------------------------------------------------
-- Tabla: productos (proyecto base api-products)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS productos (
    id      INT UNSIGNED   NOT NULL AUTO_INCREMENT,
    nombre  VARCHAR(100)   NOT NULL,
    precio  DECIMAL(10,2)  NOT NULL,
    stock   INT            NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
