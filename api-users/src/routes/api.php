<?php

$method = $_SERVER['REQUEST_METHOD'];

$path = rtrim(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH),
    '/'
);

/* =================================================================
 * Rutas: Usuarios  (/api/users)
 * =============================================================== */

if ($path === '/api/users') {

    if ($method === 'GET') {
        $userController->index();
        exit;
    }

    if ($method === 'POST') {
        $userController->store();
        exit;
    }
}

if (preg_match('#^/api/users/([1-9][0-9]*)$#', $path, $matches)) {

    $id = (int) $matches[1];

    if ($method === 'GET') {
        $userController->show($id);
        exit;
    }

    if ($method === 'PUT') {
        $userController->update($id);
        exit;
    }

    if ($method === 'DELETE') {
        $userController->destroy($id);
        exit;
    }
}

/* =================================================================
 * Rutas: Productos  (/api/products)  — proyecto base
 * =============================================================== */

if ($path === '/api/products' && $method === 'GET') {
    $productController->index();
    exit;
}

if ($path === '/api/products/register' && $method === 'POST') {
    $productController->store();
    exit;
}

if (preg_match('#^/api/products/([1-9][0-9]*)$#', $path, $matches)) {

    $id = (int) $matches[1];

    if ($method === 'GET') {
        $productController->show($id);
        exit;
    }

    if ($method === 'PUT') {
        $productController->update($id);
        exit;
    }

    if ($method === 'DELETE') {
        $productController->destroy($id);
        exit;
    }
}

http_response_code(404);

echo json_encode([
    'error' => 'Endpoint no encontrado'
]);
