# J2-API: Servicio Central de API

J2-API es un backend centralizado construido con **Laravel 11** diseñado para proveer servicios RESTful a múltiples aplicaciones y plataformas (Frontend Web, Móvil, etc.).

## 🚀 Arquitectura

Este repositorio está desacoplado de los frontends cliente. Utiliza **Laravel Sanctum** para ofrecer:
- Autenticación segura para SPAs (Single Page Applications) mediante cookies de sesión (CSRF / stateful authentication).
- Autenticación por Tokens (API Tokens) para aplicaciones móviles y clientes de terceros.

## 📦 Requisitos del Sistema

- PHP >= 8.2
- Composer
- Base de datos MySQL / MariaDB / PostgreSQL

## ⚙️ Configuración y Despliegue

1. **Clonar el repositorio y entrar en la carpeta:**
   ```bash
   git clone <repo-url> J2-API
   cd J2-API
   ```

2. **Instalar dependencias de PHP:**
   ```bash
   composer install
   ```

3. **Configuración de Variables de Entorno:**
   - Copia `.env.example` a `.env`.
   - Genera la llave de la aplicación: `php artisan key:generate`
   - Configura tus accesos a Base de Datos en el `.env` (ej. `DB_DATABASE=j2_db`).
   - Define el `FRONTEND_URL` o dominios permitidos en `SANCTUM_STATEFUL_DOMAINS` para el manejo de sesiones SPA (por ejemplo, `localhost:5173,enfoca.example.com`).

4. **Migraciones:**
   ```bash
   php artisan migrate
   ```

5. **Iniciar el Servidor Local:**
   ```bash
   php artisan serve
   ```
   Por defecto, la API estará disponible en `http://localhost:8000/api`.

## 🔒 Autenticación y CORS

Para conectar tu SPA (ej. React o Vue):
- Asegúrate de que el frontend configura `axios` (o tu cliente HTTP) con `withCredentials: true`.
- Antes de iniciar sesión, el cliente debe realizar una petición `GET` a `/sanctum/csrf-cookie` para inicializar la protección CSRF.
- Las variables `CORS_ALLOWED_ORIGINS` o la configuración en `config/cors.php` deben coincidir exactamente con el origen de tus aplicaciones cliente.

## 📚 Endpoints Principales (Ejemplos)
- `POST /api/login`: Autenticación de usuarios.
- `POST /api/register`: Registro de nuevos usuarios.
- `GET /api/usuario`: Retorna la información del usuario en sesión.
*(Ver las rutas completas en `routes/api.php`)*
