<?php

class Database {
    public static function connect(): PDO {
        // Valores por defecto del proyecto base; pueden cambiarse aquí
        // o mediante variables de entorno (DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS).
        $host     = getenv('DB_HOST') ?: '127.0.0.1';
        $port     = getenv('DB_PORT') ?: 3306;
        $dbname   = getenv('DB_NAME') ?: 'dbisildemo';
        $user     = getenv('DB_USER') ?: 'root';
        $password = getenv('DB_PASS') ?: '';

        $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

        return new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    }
}
