<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #ff4d6d;
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
            text-align: center;
        }
        .content p {
            font-size: 16px;
            line-height: 1.5;
            color: #555;
            margin-bottom: 20px;
        }
        .button {
            display: inline-block;
            padding: 12px 25px;
            background-color: #ff4d6d;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: bold;
            margin: 20px 0;
        }
        .footer {
            background-color: #f4f4f4;
            color: #888;
            padding: 20px;
            text-align: center;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>{{ $app == 'love_widget' ? 'Love Widget' : 'Enfoca' }}</h1>
        </div>
        <div class="content">
            <h2>Recuperación de contraseña</h2>
            <p>Has recibido este correo porque hemos recibido una solicitud para restablecer la contraseña de tu cuenta.</p>
            <a href="{{ $url }}" class="button">Restablecer Contraseña</a>
            <p>Si no has solicitado restablecer la contraseña, no es necesario que realices ninguna otra acción.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ $app == 'love_widget' ? 'Love Widget' : 'Enfoca' }}. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
