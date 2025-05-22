<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña - Traceability</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }
        .bg-primary {
            background-color: #91AF2B;
        }
        .bg-primary-dark {
            background-color: #647a18;
        }
        .text-primary {
            color: #91AF2B;
        }
        .border-primary {
            border-color: #91AF2B;
        }
        .focus\:ring-primary:focus {
            --tw-ring-color: #91AF2B;
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
    <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-lg shadow-lg">
        <div class="text-center">
			<img src="{{ asset('icon.png') }}" class="w-32 h-32 mb-4 mx-auto" alt="">
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                Restablecer Contraseña
            </h2>
			@if(isset($isValid) && $isValid)
            <p class="mt-2 text-sm text-gray-600">
                Ingresa tu nueva contraseña para continuar
            </p>
			@endif
        </div>

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

        <form class="mt-8 space-y-6" method="POST" action="{{ route('api.reset-password') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

			@if(isset($isValid) && $isValid)
            <div class="rounded-md shadow-sm space-y-4">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Nueva Contraseña</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <input id="password" name="password" type="password" autocomplete="new-password" required 
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm"
                               placeholder="••••••••" minlength="8">
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar Contraseña</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required 
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm"
                               placeholder="••••••••" minlength="8">
                    </div>
                </div>
            </div>
			@else
				<div>
					<p class="mt-2 text-lg text-red-600">
						{{ $error }}
					</p>
				</div>
			@endif

            <div>
                <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white {{ (isset($isValid) && !$isValid) ? 'bg-gray-400 cursor-not-allowed' : 'btn-primary' }} focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary" {{ (isset($isValid) && !$isValid) ? 'disabled' : '' }}>
                    {{ (isset($isValid) && !$isValid) ? 'Enlace Inválido' : 'Restablecer Contraseña' }}
                </button>
            </div>
        </form>
    </div>
</body>
</html>
