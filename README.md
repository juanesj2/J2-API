<div align="center">
  <img src="public/imagenes/logo_ENFOKA-sin-fondo.png" alt="J2 Hub Logo" width="200"/>

  <h1>🚀 J2 API & Hub Central</h1>
  <p>El centro neurálgico para el ecosistema de aplicaciones de JuanStiven.</p>
  
  ![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
  ![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
  ![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
  ![Alpine.js](https://img.shields.io/badge/Alpine.js-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white)
</div>

---

## 🌟 ¿Qué es J2 API & Hub?

J2 API es un servidor **RESTful de alto rendimiento** construido con Laravel 11. No solo sirve los datos de las aplicaciones móviles y web (como *Enfoca* y *Love Widget*), sino que incorpora un **Panel de Control Premium (J2 Hub)** desde el cual se puede gestionar toda la infraestructura.

### ✨ Características del Hub
- **Detección Dinámica de Apps:** Lee automáticamente las rutas y descubre qué aplicaciones están activas en el servidor.
- **Gestión de Usuarios:** Lista completa de todos los usuarios del ecosistema, con buscadores en tiempo real y filtros dinámicos por App.
- **Despliegue con un Clic:** Botón mágico para hacer un `git pull`, migrar bases de datos y limpiar la caché, ¡todo en 3 segundos sin tocar la terminal!
- **Control de Roles:** Asignación rápida de permisos de administrador (Admin/SuperAdmin).

---

## ⚙️ Arquitectura

El ecosistema utiliza **Laravel Sanctum** para manejar dos tipos de autenticación:
1. **API Tokens:** Para clientes móviles y dispositivos IoT.
2. **Sesiones Stateful:** Para la navegación web dentro del J2 Hub.

---

## 🚀 Despliegue Automatizado (Live)

¡Este servidor está alojado en Alwaysdata! 
Para actualizar la versión de producción:
1. Entra a **[https://j2api.alwaysdata.net/hub](https://j2api.alwaysdata.net/hub)**.
2. Inicia sesión con tus credenciales de administrador.
3. Haz clic en el botón de **"Pull & Actualizar API"**.
4. ¡El sistema descargará los cambios de GitHub al instante!

---

<div align="center">
  <p><i>Desarrollado con ❤️ para dominar el mundo del software.</i></p>
</div>
