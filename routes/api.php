<?php

// Aquí centralizamos todas las sub-rutas de la API para mantener el orden.
// Laravel cargará este archivo desde bootstrap/app.php, y este a su vez cargará el resto.

require __DIR__.'/api_common.php';
require __DIR__.'/api_enfoca.php';
require __DIR__.'/api_love_widget.php';
