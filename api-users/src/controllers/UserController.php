<?php

class UserController {

    public function __construct(
        private User $user
    ) {}

    // GET /api/users
    public function index(): void {

        $this->json(200, $this->user->getAll());
    }

    // GET /api/users/{id}
    public function show(int $id): void {

        $user = $this->user->getById($id);

        if (!$user) {
            $this->json(404, ['error' => 'Usuario no encontrado']);
            return;
        }

        $this->json(200, $user);
    }

    // POST /api/users
    public function store(): void {

        $data = $this->getJsonBody();

        if ($data === null) {
            $this->json(400, ['error' => 'El cuerpo de la petición debe ser un JSON válido']);
            return;
        }

        $errors = $this->validate($data, true);

        if (!empty($errors)) {
            $this->json(400, ['error' => 'Datos inválidos', 'detalles' => $errors]);
            return;
        }

        $data = $this->clean($data);

        if ($this->user->emailExists($data['email'])) {
            $this->json(400, ['error' => 'El email ya está registrado']);
            return;
        }

        $id = $this->user->create($data);

        $this->json(201, [
            'mensaje' => 'Usuario creado',
            'id' => $id,
            'data' => $this->user->getById($id)
        ]);
    }

    // PUT /api/users/{id}
    public function update(int $id): void {

        if (!$this->user->getById($id)) {
            $this->json(404, ['error' => 'Usuario no encontrado']);
            return;
        }

        $data = $this->getJsonBody();

        if ($data === null) {
            $this->json(400, ['error' => 'El cuerpo de la petición debe ser un JSON válido']);
            return;
        }

        $errors = $this->validate($data, false);

        if (!empty($errors)) {
            $this->json(400, ['error' => 'Datos inválidos', 'detalles' => $errors]);
            return;
        }

        $data = $this->clean($data);

        if ($this->user->emailExists($data['email'], $id)) {
            $this->json(400, ['error' => 'El email ya está registrado por otro usuario']);
            return;
        }

        $this->user->update($id, $data);

        $this->json(200, [
            'mensaje' => 'Usuario actualizado',
            'id' => $id,
            'data' => $this->user->getById($id)
        ]);
    }

    // DELETE /api/users/{id}
    public function destroy(int $id): void {

        $result = $this->user->delete($id);

        if ($result === 0) {
            $this->json(404, ['error' => 'Usuario no encontrado']);
            return;
        }

        $this->json(200, [
            'mensaje' => 'Usuario eliminado',
            'id' => $id
        ]);
    }

    /* ---------------------------------------------------------------
     * Métodos auxiliares
     * ------------------------------------------------------------- */

    /**
     * Lee el body como JSON. Devuelve null si está vacío o no es un objeto JSON válido.
     */
    private function getJsonBody(): ?array {

        $data = json_decode(file_get_contents('php://input'), true);

        return is_array($data) ? $data : null;
    }

    /**
     * Validación básica de campos obligatorios.
     * - POST: name, email y password son obligatorios.
     * - PUT : name y email son obligatorios; password es opcional
     *         (si se envía, se valida y se reemplaza).
     */
    private function validate(array $data, bool $isCreate): array {

        $errors = [];

        $name = $data['name'] ?? null;
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!is_string($name) || trim($name) === '') {
            $errors['name'] = 'El campo name es obligatorio';
        } elseif (mb_strlen(trim($name)) > 100) {
            $errors['name'] = 'El campo name no debe superar los 100 caracteres';
        }

        if (!is_string($email) || trim($email) === '') {
            $errors['email'] = 'El campo email es obligatorio';
        } elseif (!filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'El campo email no tiene un formato válido';
        }

        if ($isCreate || $password !== null) {
            if (!is_string($password) || $password === '') {
                $errors['password'] = 'El campo password es obligatorio';
            } elseif (strlen($password) < 6) {
                $errors['password'] = 'El campo password debe tener al menos 6 caracteres';
            }
        }

        return $errors;
    }

    /**
     * Normaliza los datos antes de guardarlos.
     */
    private function clean(array $data): array {

        return [
            'name' => trim($data['name']),
            'email' => strtolower(trim($data['email'])),
            'password' => $data['password'] ?? null
        ];
    }

    private function json(int $status, array $payload): void {

        http_response_code($status);

        echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    }
}
