<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contraseña Restablecida - Traceability</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }
        .text-primary {
            color: #91AF2B;
        }
        .btn-primary {
            background-color: #3CAADC;
            transition: background-color 0.3s;
        }
        .btn-primary:hover {
            background-color: #2d8eb8;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-lg shadow-lg text-center">
        <div class="flex justify-center">
            <div class="bg-green-100 p-3 rounded-full">
                <svg class="h-12 w-12 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
        </div>
        
        <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
            ¡Contraseña Actualizada!
        </h2>
        
        <p class="mt-2 text-gray-600">
            Tu contraseña ha sido restablecida exitosamente. Ahora puedes iniciar sesión con tu nueva contraseña.
        </p>

        <div class="mt-8">
            <a href="#" class="btn-primary text-white font-medium rounded-md px-4 py-2 inline-block">
                Volver al Inicio de Sesión
            </a>
        </div>
    </div>
</body>
</html>
