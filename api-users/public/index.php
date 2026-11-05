<?php

require_once __DIR__ . '/../src/config/Database.php';
require_once __DIR__ . '/../src/models/Product.php';
require_once __DIR__ . '/../src/models/User.php';
require_once __DIR__ . '/../src/controllers/ProductController.php';
require_once __DIR__ . '/../src/controllers/UserController.php';

header('Content-Type: application/json; charset=utf-8');

try {

    $db = Database::connect();

    $productController = new ProductController(new Product($db));

    $userController = new UserController(new User($db));

    require_once __DIR__ . '/../src/routes/api.php';

} catch (Throwable $e) {

    error_log($e->getMessage());

    http_response_code(500);

    echo json_encode([
        'error' => 'Error interno del servidor',
        'detalle' => $e->getMessage()
    ]);
}
