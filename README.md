# API-COLEGIO

Proyecto Laravel para gestión de instituciones/colegios con autenticación JWT y documentación OpenAPI/Swagger.

---

## Índice

- [Requisitos](#requisitos)
- [Instalación](#instalación)
- [Variables de entorno](#variables-de-entorno)
- [Base de datos](#base-de-datos)
- [Migraciones y seeders](#migraciones-y-seeders)
- [Ejecución](#ejecución)
- [Documentación Swagger](#documentación-swagger)
- [Autenticación JWT](#autenticación-jwt)
- [Endpoints principales](#endpoints-principales)
- [Estructura relevante](#estructura-relevante)
- [Comandos útiles](#comandos-útiles)
- [Solución de problemas](#solución-de-problemas)
- [Despliegue](#despliegue)
- [Licencia](#licencia)

---

## Requisitos

- PHP 8.1+Extensiones comunes de Laravel: `bcmath`, `ctype`, `fileinfo`, `json`, `mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`.
- Composer 2.x
- MySQL 5.7+/8.x (o MariaDB compatible)
- Node.js 16+ (solo si vas a compilar front/Vite; para la API no es obligatorio)
- Redis opcional (el proyecto funciona sin Redis)

Sugerencia Windows (Laragon/XAMPP/WAMP): MySQL suele estar en `localhost:3306` con usuario `root` y contraseña vacía.

---

## Instalación

```bash
git clone <URL_DE_TU_REPO> API-COLEGIO
cd API-COLEGIO

# Dependencias PHP
composer install

# Copiar variables de entorno
cp .env.example .env
```

---

## Variables de entorno

Sustituye el contenido de tu `.env` con lo siguiente y ajusta según tu entorno:

```env
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:F3M6lLIiPRLy7VrswiNKc4Fq+uFw3RIFU6bA2cOsub8=
APP_DEBUG=true
APP_URL=http://localhost

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file
# APP_MAINTENANCE_STORE=database

PHP_CLI_SERVER_WORKERS=4

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=apicolegio
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync

CACHE_STORE=database
# CACHE_PREFIX=
CACHE_DRIVER=file
MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="${APP_NAME}"

JWT_SECRET=w9lVR2iASAwpxh7KImlTvXdoFjGNqBMzj5KPT0n9h2zIAa0P7bsZiMbDt1ntP5ox

JWT_ALGO=HS256
JWT_TTL=60
JWT_REFRESH_TTL=20160
JWT_BLACKLIST_ENABLED=true
L5_SWAGGER_GENERATE_ALWAYS=true
L5_SWAGGER_CONST_HOST="${APP_URL}/api"
```

Claves útiles:

```bash
# Generar APP_KEY si fuese necesario
php artisan key:generate

# Regenerar JWT_SECRET si fuese necesario
php artisan jwt:secret
```

No subas `.env` al repositorio.

---

## Base de datos

1. Crea la base de datos:

   ```sql
   CREATE DATABASE apicolegio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
2. Verifica credenciales en `.env` (`DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).

---

## Migraciones y seeders

Ejecuta migraciones:

```bash
php artisan migrate
```

Semillas de datos:

```bash
# Si DatabaseSeeder ya llama a todo:
php artisan db:seed

# O individualmente:
php artisan db:seed --class=RegionComunaSeeder
php artisan db:seed --class=DemoInstitucionesSeeder
php artisan db:seed --class=UserRoleSeeder
```

Notas:

- `RegionComunaSeeder` carga regiones y comunas.
- `DemoInstitucionesSeeder` crea instituciones, colegios, personas y asigna por colegio un usuario con rol Encargado.
- `UserRoleSeeder` crea el rol Encargado y usuarios de ejemplo si corresponde.

---

## Ejecución

Servidor embebido de Laravel:

```bash
php artisan serve
```

Por defecto: `http://127.0.0.1:8000`.
Ajusta `APP_URL` en `.env` si usas host/puerto distinto (por ejemplo `http://127.0.0.1:8000`).

---

## Documentación Swagger

Generar documentación:

```bash
php artisan l5-swagger:generate
```

Abrir Swagger UI:

```
http://localhost/api/documentation
```

El JSON/YAML suele guardarse en `storage/api-docs/openapi.json` o `.yaml`.
En producción, considera proteger o deshabilitar la UI.

**Configuración de escaneo L5-Swagger** (en `config/l5-swagger.php`):

```php
'paths' => [
  'annotations' => [
    base_path('app/Http/Controllers'),
    base_path('app/Swagger'),
  ],
],
```

---

## Autenticación JWT

La API usa autenticación Bearer JWT.

Flujo básico:

1. Registro — `POST /api/auth/register`

   ```json
   {
     "nombre_usuario": "javier01",
     "password": "123456"
   }
   ```
2. Login — `POST /api/auth/login`Respuesta: `access_token`, `token_type`, `expires_in`.
3. Endpoint protegido — `GET /api/auth/me`Enviar `Authorization: Bearer <access_token>`.
4. Logout — `POST /api/auth/logout`.

Ejemplo rápido con cURL:

```bash
# Login
curl -X POST http://127.0.0.1:8000/api/auth/login   -H "Content-Type: application/json"   -d '{"nombre_usuario":"javier01","password":"123456"}'

# Llamada autenticada
curl http://127.0.0.1:8000/api/auth/me   -H "Authorization: Bearer <ACCESS_TOKEN>"
```

---

## Endpoints principales

Autenticación:

- `POST /api/auth/register`
- `POST /api/auth/login`
- `GET /api/auth/me`
- `POST /api/auth/logout`

Regiones:

- `GET /api/regiones?include=comunas`
- `GET /api/regiones/{regionId}/comunas`

Instituciones (wizard):

- `GET /api/instituciones` (paginado; `page`, `per_page`, `search`, `estado`)
- `GET /api/instituciones/{id}`
- `POST /api/instituciones`
- `PUT /api/instituciones/{id}`

Para estructuras exactas, ver Swagger UI.

---

## Estructura relevante

```
app/
  Http/
    Controllers/
      Controller.php                # @OA\Info, @OA\Server, @OA\SecurityScheme
      UsuarioController.php         # Auth
      RegionController.php          # Regiones y comunas
      InstitucionController.php     # Instituciones (wizard)
  Models/
  Swagger/
      SchemasCommon.php             # ErrorMessage, ErrorValidation
      SchemasRegion.php             # Region, Comuna, Responses
      SchemasInstituciones.php      # InstitucionWizard, paginación, requests
database/
  migrations/
  seeders/
      RegionComunaSeeder.php
      DemoInstitucionesSeeder.php
      UserRoleSeeder.php
```

---

## Comandos útiles

```bash
# Limpiar cachés
php artisan optimize:clear
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Migrar desde cero y resembrar (destruye datos)
php artisan migrate:fresh --seed

# Ejecutar pruebas (si existen)
php artisan test
```

---

## Solución de problemas

- MySQL no conectaRevisa credenciales/host/puerto en `.env`. En Laragon: `root` sin contraseña.
- Errores en migracionesEjecuta `php artisan migrate:fresh --seed` en desarrollo.
- Swagger: `$ref not found`, “Unable to merge”, duplicados

  - Define los schemas referenciados en `app/Swagger/*.php`.
  - En `config/l5-swagger.php` usa:
    ```php
    'paths' => [
      'annotations' => [
        base_path('app/Http/Controllers'),
        base_path('app/Swagger'),
      ],
    ],
    ```
  - Evita duplicar `@OA\Schema(schema="...")` con el mismo nombre.
  - En swagger-php v4, coloca anotaciones sobre clases/métodos (no docblocks sueltos).
- JWT inválido o expirado
  Repite login. Ajusta `JWT_TTL` y `JWT_REFRESH_TTL` según lo requerido.

---

## Despliegue

```bash
# Producción
composer install --no-dev --optimize-autoloader
php artisan key:generate        # si falta APP_KEY
php artisan migrate --force
php artisan l5-swagger:generate # opcional, si vas a servir la UI
```

Configura el servidor web para apuntar al directorio `public/` y define las variables de entorno del servidor (no uses el `.env` de local).

---

## Usuarios por Defecto para el front 

usuario1:

    usuario: Javier
    contrasena:12345678

usaurio2:
	usaurio:Maria
	contrasena:12345678
