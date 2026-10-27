# Products API

API REST para administrar productos, su divisa base y precios registrados explícitamente en otras divisas. Fue construida con Laravel 12, PHP 8.2 o superior, Eloquent y autenticación JWT.

## Instalación

```bash
composer install
cp .env.example .env
php artisan key:generate
```

La aplicación usa MySQL. Cree las bases de datos de desarrollo y pruebas, por ejemplo:

```sql
CREATE DATABASE prueba_backend CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE prueba_backend_testing CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Configure en `.env` `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` y `DB_PASSWORD`. La configuración de ejemplo apunta a `prueba_backend` en `127.0.0.1:3306` con el usuario local `root` y contraseña vacía; ajústela según su entorno.

Defina un secreto JWT aleatorio de al menos 32 caracteres:

```env
JWT_SECRET=replace-with-a-long-random-secret
JWT_TTL=3600
```

Si `JWT_SECRET` no está definido, la aplicación utiliza `APP_KEY` como respaldo. En producción se recomienda mantener ambos secretos separados.

Luego prepare los datos y ejecute el servidor:

```bash
php artisan migrate --seed
php artisan serve
```

El seeder crea un usuario local de demostración (`admin@example.com` / `password`) y USD, EUR y VES con tasas únicamente ilustrativas. Cambie o elimine ese usuario fuera de un entorno de prueba.

## Autenticación

Solicite un token:

```http
POST /api/auth/login
Content-Type: application/json

{"email":"admin@example.com","password":"password"}
```

La respuesta incluye `data.access_token`. Envíelo en las demás rutas mediante `Authorization: Bearer <token>`. El token HS256 expira según `JWT_TTL`; no se implementó refresh ni revocación porque están fuera del alcance.

## Endpoints

| Método | Ruta | Descripción |
|---|---|---|
| POST | `/api/auth/login` | Obtener JWT |
| GET | `/api/products` | Listar productos, paginados de 15 en 15 |
| POST | `/api/products` | Crear producto |
| GET | `/api/products/{id}` | Consultar producto |
| PUT | `/api/products/{id}` | Reemplazar los campos editables |
| DELETE | `/api/products/{id}` | Eliminar producto |
| GET | `/api/products/{id}/prices` | Consultar precios registrados |
| POST | `/api/products/{id}/prices` | Registrar precio en una divisa |
| GET | `/api/currency` | Listar divisas |
| POST | `/api/currency` | Crear una divisa |
| GET | `/api/currency/{id}` | Consultar una divisa |

Ejemplo de producto para `POST` o `PUT`:

```json
{
  "name": "Laptop",
  "description": "Equipo de trabajo",
  "price": "1000.00",
  "currency_id": 1,
  "tax_cost": "160.00",
  "manufacturing_cost": "600.00",
  "manufacuring_date_create": "2026-01-15",
  "manufacuring_date_expire": "2028-01-15"
}
```

Ejemplo de precio:

```json
{"currency_id": 2, "price": "920.00"}
```

Al crear una divisa, `exchange_rate_date` acepta una fecha opcional en formato `YYYY-MM-DD`. Las dos fechas de fabricación del producto también son opcionales; si ambas se envían, la fecha de vencimiento no puede ser anterior a la fecha de fabricación.

Las respuestas exitosas usan `data`; las eliminaciones usan `message`. La creación responde `201`, validaciones `422`, recursos inexistentes `404` y errores de autenticación `401`. Los importes se serializan como cadenas decimales para conservar su precisión.

## Pruebas

Las pruebas usan la base MySQL independiente `prueba_backend_testing`, configurada en `phpunit.xml`. `RefreshDatabase` elimina y reconstruye sus tablas, por lo que nunca debe apuntar ese nombre a una base con información real. Ajuste las credenciales de prueba si su MySQL local no usa `root` sin contraseña.

```bash
php artisan test
php artisan route:list --path=api
composer audit
```

La colección importable está en `docs/postman_collection.json`. Su script de login guarda automáticamente el token en la variable de colección.

