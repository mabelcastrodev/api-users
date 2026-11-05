<?php

class ProductController {

    public function __construct(
        private Product $product
    ) {}

    public function index(): void {

        echo json_encode(
            $this->product->getAll()
        );
    }

    public function store(): void {

        $data = json_decode(
            file_get_contents('php://input'),
            true
        );

        $id = $this->product->create($data);

        http_response_code(201);

        echo json_encode([
            'mensaje' => 'Producto creado',
            'id' => $id
        ]);
    }

    public function show(int $id): void {

        $product = $this->product->getById($id);

        if (!$product) {

            http_response_code(404);

            echo json_encode([
                'error' => 'Producto no encontrado'
            ]);

            return;
        }

        echo json_encode($product);
    }

    public function update(int $id): void {

        $data = json_decode(
            file_get_contents('php://input'),
            true
        );

        $result = $this->product->update($id, $data);

        if ($result === 0) {

            http_response_code(404);

            echo json_encode([
                'error' => 'Producto no encontrado o sin cambios'
            ]);

            return;
        }

        echo json_encode([
            'mensaje' => 'Producto actualizado',
            'id' => $id
        ]);
    }

    public function destroy(int $id): void {

        $result = $this->product->delete($id);

        if ($result === 0) {

            http_response_code(404);

            echo json_encode([
                'error' => 'Producto no encontrado'
            ]);

            return;
        }

        http_response_code(204);
    }
}