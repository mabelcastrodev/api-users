# API REST de Usuarios (PHP + MySQL)

API REST para gestionar el recurso **Usuarios**. Está construida sobre la arquitectura base del proyecto `api-products`: PHP puro, PDO, patrón Modelo–Controlador y un enrutador simple.

| Recurso   | Ruta base        | Métodos                    |
|-----------|------------------|----------------------------|
| Usuarios  | `/api/users`     | `GET`, `POST`, `PUT`, `DELETE` |
| Productos | `/api/products`  | proyecto base (sin cambios en su lógica) |

---

## 1. Estructura del proyecto

```
api-users/
├── database/
│   └── schema.sql                 # Script de creación de la BD y tablas
├── public/
│   └── index.php                  # Punto de entrada (front controller)
├── src/
│   ├── config/
│   │   └── Database.php           # Conexión PDO a MySQL
│   ├── controllers/
│   │   ├── ProductController.php
│   │   └── UserController.php     # Respuestas JSON, códigos HTTP y validación
│   ├── models/
│   │   ├── Product.php
│   │   └── User.php               # CRUD sobre la tabla users
│   └── routes/
│       └── api.php                # Registro de rutas
├── requests.http                  # Peticiones de ejemplo (REST Client)
└── README.md
```

**Flujo de una petición:** `public/index.php` → crea la conexión (`Database`) → instancia modelos y controladores → `routes/api.php` decide qué método del controlador ejecutar según la URL y el método HTTP → el controlador usa el modelo y responde en JSON.

---

## 2. Requisitos

- PHP **8.0 o superior** con la extensión `pdo_mysql` habilitada
- MySQL 5.7+ o MariaDB 10+ (por ejemplo, el que trae XAMPP o Laragon)
- Postman, Thunder Client, la extensión REST Client de VS Code o `curl` para probar

---

## 3. Instalación

### 3.1 Clonar el repositorio

```bash
git clone https://github.com/mabelcastrodev/api-users.git
cd api-users
```

### 3.2 Crear la base de datos

El script `database/schema.sql` crea la base de datos `dbisildemo`, la tabla `users` (con 2 usuarios de prueba) y la tabla `productos` del proyecto base.

**Opción A: desde la terminal**

```bash
mysql -u root -p -P 3306 < database/schema.sql
```

**Opción B: desde phpMyAdmin**

1. Ingresar a `http://localhost/phpmyadmin`.
2. Ir a la pestaña **Importar**.
3. Seleccionar el archivo `database/schema.sql` y pulsar **Continuar**.

Estructura de la tabla `users`:

| Campo        | Tipo            | Detalle                                   |
|--------------|-----------------|-------------------------------------------|
| `id`         | INT UNSIGNED    | PK, AUTO_INCREMENT                        |
| `name`       | VARCHAR(100)    | Obligatorio                               |
| `email`      | VARCHAR(150)    | Obligatorio, **único**                    |
| `password`   | VARCHAR(255)    | Se guarda encriptada con `password_hash()` |
| `created_at` | TIMESTAMP       | Se llena automáticamente al crear         |

### 3.3 Configurar la conexión

Editar los valores por defecto en `src/config/Database.php`:

```php
$host     = getenv('DB_HOST') ?: '127.0.0.1';
$port     = getenv('DB_PORT') ?: 3306;      // XAMPP suele usar 3306
$dbname   = getenv('DB_NAME') ?: 'dbisildemo';
$user     = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';
```

También se pueden definir como variables de entorno sin modificar el código:

```bash
DB_PORT=3306 DB_PASS=mi_clave php -S localhost:8000 -t public
```

### 3.4 Levantar el servidor

```bash
php -S localhost:8000 -t public
```

La API queda disponible en `http://localhost:8000/api/users`.

---

## 4. Endpoints de Usuarios

| Método   | Endpoint            | Descripción                  | Éxito | Errores posibles |
|----------|---------------------|------------------------------|-------|------------------|
| `GET`    | `/api/users`        | Listar todos los usuarios    | 200   | 500 |
| `GET`    | `/api/users/{id}`   | Obtener un usuario por ID    | 200   | 404, 500 |
| `POST`   | `/api/users`        | Crear un usuario             | 201   | 400, 500 |
| `PUT`    | `/api/users/{id}`   | Actualizar un usuario        | 200   | 400, 404, 500 |
| `DELETE` | `/api/users/{id}`   | Eliminar un usuario          | 200   | 404, 500 |

### Validaciones

| Campo      | POST         | PUT                                             | Regla |
|------------|--------------|-------------------------------------------------|-------|
| `name`     | Obligatorio  | Obligatorio                                     | Texto no vacío, máximo 100 caracteres |
| `email`    | Obligatorio  | Obligatorio                                     | Formato de email válido y que no esté registrado por otro usuario |
| `password` | Obligatorio  | Opcional (si no se envía, se conserva la actual) | Mínimo 6 caracteres |

Otras reglas:

- El body debe ser un **JSON válido**; si no lo es, la API responde `400`.
- La contraseña se guarda con `password_hash()` (bcrypt) y **nunca** se devuelve en las respuestas.
- El email se guarda en minúsculas y sin espacios al inicio o al final.

### Códigos de estado HTTP utilizados

| Código | Cuándo se devuelve |
|--------|--------------------|
| `200 OK` | Consulta, actualización o eliminación exitosa |
| `201 Created` | Usuario creado correctamente |
| `400 Bad Request` | JSON inválido, campos obligatorios faltantes o inválidos, email duplicado |
| `404 Not Found` | El usuario no existe o la ruta no existe |
| `500 Internal Server Error` | Error de base de datos o error inesperado del servidor |

---

## 5. Ejemplos de peticiones HTTP

> El archivo [`requests.http`](requests.http) contiene todas estas peticiones listas para ejecutar con la extensión **REST Client** de VS Code.

### 5.1 Listar usuarios: `GET /api/users`

```bash
curl http://localhost:8000/api/users
```

Respuesta `200 OK`:

```json
[
  { "id": 1, "name": "Ana Torres",   "email": "ana.torres@isil.pe",   "created_at": "2026-11-05 11:20:00" },
  { "id": 2, "name": "Luis Ramírez", "email": "luis.ramirez@isil.pe", "created_at": "2026-11-05 11:20:00" }
]
```

### 5.2 Obtener un usuario: `GET /api/users/{id}`

```bash
curl http://localhost:8000/api/users/1
```

Respuesta `200 OK`:

```json
{ "id": 1, "name": "Ana Torres", "email": "ana.torres@isil.pe", "created_at": "2026-11-05 11:20:00" }
```

Si no existe, `404 Not Found`:

```json
{ "error": "Usuario no encontrado" }
```

### 5.3 Crear un usuario: `POST /api/users`

```bash
curl -X POST http://localhost:8000/api/users \
  -H "Content-Type: application/json" \
  -d '{"name": "María López", "email": "maria.lopez@isil.pe", "password": "maria2026"}'
```

Respuesta `201 Created`:

```json
{
  "mensaje": "Usuario creado",
  "id": 3,
  "data": { "id": 3, "name": "María López", "email": "maria.lopez@isil.pe", "created_at": "2026-11-05 11:25:10" }
}
```

Con datos faltantes o inválidos, `400 Bad Request`:

```bash
curl -X POST http://localhost:8000/api/users \
  -H "Content-Type: application/json" \
  -d '{"name": "", "email": "correo-invalido", "password": "123"}'
```

```json
{
  "error": "Datos inválidos",
  "detalles": {
    "name": "El campo name es obligatorio",
    "email": "El campo email no tiene un formato válido",
    "password": "El campo password debe tener al menos 6 caracteres"
  }
}
```

Con un email ya registrado, `400 Bad Request`:

```json
{ "error": "El email ya está registrado" }
```

### 5.4 Actualizar un usuario: `PUT /api/users/{id}`

Sin cambiar la contraseña:

```bash
curl -X PUT http://localhost:8000/api/users/3 \
  -H "Content-Type: application/json" \
  -d '{"name": "María López Díaz", "email": "maria.lopez@isil.pe"}'
```

Cambiando también la contraseña:

```bash
curl -X PUT http://localhost:8000/api/users/3 \
  -H "Content-Type: application/json" \
  -d '{"name": "María López Díaz", "email": "maria.lopez@isil.pe", "password": "nuevaClave99"}'
```

Respuesta `200 OK`:

```json
{
  "mensaje": "Usuario actualizado",
  "id": 3,
  "data": { "id": 3, "name": "María López Díaz", "email": "maria.lopez@isil.pe", "created_at": "2026-11-05 11:25:10" }
}
```

### 5.5 Eliminar un usuario: `DELETE /api/users/{id}`

```bash
curl -X DELETE http://localhost:8000/api/users/3
```

Respuesta `200 OK`:

```json
{ "mensaje": "Usuario eliminado", "id": 3 }
```

### 5.6 Error del servidor (500)

Si la base de datos no está disponible o hay un error inesperado:

```json
{ "error": "Error interno del servidor", "detalle": "SQLSTATE[HY000] [2002] Connection refused" }
```

> **Windows (CMD/PowerShell):** en lugar de comillas simples usa comillas dobles y escapa las internas, por ejemplo:
> `curl -X POST http://localhost:8000/api/users -H "Content-Type: application/json" -d "{\"name\":\"María\",\"email\":\"maria@isil.pe\",\"password\":\"maria2026\"}"`
> O usa Postman / Thunder Client con el body en formato **raw → JSON**.

---

## 6. Usuarios de prueba

| Email                  | Contraseña  |
|------------------------|-------------|
| ana.torres@isil.pe     | secret123   |
| luis.ramirez@isil.pe   | clave456    |
