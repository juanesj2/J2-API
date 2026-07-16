<div align="center">
  <img src="public/imagenes/logo_ENFOKA-sin-fondo.png" alt="Logo" width="150"/>
  <h1>J2 API & Hub</h1>
</div>

¡Buenas! Este es el backend principal de mis proyectos. Básicamente, he montado una única API en Laravel 11 para no tener que crear un servidor distinto por cada aplicación que se me ocurra hacer. Desde aquí controlo las bases de datos, los usuarios y todo el ecosistema.

Además, he programado un **Panel de Control (J2 Hub)** dentro de la propia API. Entro ahí desde el móvil o el PC, veo quién se ha registrado, controlo los roles y con darle a un solo botón, el servidor entero se actualiza con los últimos cambios de GitHub.

---

## 📱 Mis Aplicaciones 

Esta API da vida a las siguientes aplicaciones que tengo ahora mismo activas:

### 📸 [Enfoca](#)
Es una red social basada en la fotografía y en capturar el momento. Los usuarios pueden subir sus fotos, interactuar, chatear y conseguir logros (como si fuera un videojuego). Toda la gestión de cuentas, los mensajes del chat y las fotos optimizadas pasan por este servidor.

### ❤️ [Love Widget](#)
Es una aplicación pensada para parejas. Un widget muy chulo para compartir tu estado de ánimo, mandarse mensajes y mantener el contacto de una forma directa y bonita directamente en la pantalla de inicio del móvil. Toda la sincronización y la lógica está conectada a esta misma base de datos.

---

## 🛠 Cómo funciona esto por dentro

- **Laravel 11:** El motor principal para manejar la lógica y la base de datos.
- **Detección Automática:** La API lee sola los archivos de rutas (`api_enfoca.php`, `api_love_widget.php`). Si mañana hago una tercera app, solo añado el archivo y el Hub la reconoce mágicamente.
- **Autenticación mixta:** Uso Tokens (Sanctum) para que las apps de los móviles se conecten, y cookies normales para cuando yo entro al Hub desde mi navegador.

¡Y poco más! Todo centralizado y fácil de escalar.