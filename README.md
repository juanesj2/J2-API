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



TODO

COMPROBAR Arreglar el descargar y guardar foto
COMPROBAR Arreglar micrófono 
COMPROBAR Arreglar mapa 
COMPROBAR Añadir juego de huella 🫆 al mandar emoji
COMPROBAR Dar una vuelta a los dibujos de pareja, arreglar selector de colores, ver los dibujos ya echos de ella 
COMPROBAR Tinder de pareja, llegan muchas notificaciones 
COMPROBAR En la parte de más poner como notificación para los juegos 
COMPROBAR Al darle a la flecha para atrás que salga de donde estás 
COMPROBAR En cine y comida bajar el menú y revisar rendimiento 
COMPROBAR En la parte de planificación de viaje dentro de equipaje al darle intro que cree otro 
COMPROBAR Arreglar el enviar notificación personalizada 
COMPROBAR Error al eliminar mensajes revisar
COMPROBAR Revisar colección del widget en más, al cerrar la app se deja de ver el álbum seleccionado 
Revisar notificaciones con la app cerrada 
La notificación de la racha siempre recuerda aunque se haya subido foto
Por alguna razón cuando mi novia hace fotos la calidad de la foto se ve degradada incluso antes de subirla, con mi móvil no, porque?
Cuando estoy en el chat los mensajes no se actualizan automaticamente, tengo que salir y entrar en el chat para poder ver los nuevos mensajes, eso no esta bien