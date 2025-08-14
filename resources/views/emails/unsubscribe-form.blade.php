<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Darse de baja - {{ $companyName }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 py-12">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-8">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Confirmar baja</h1>
            <p class="text-gray-600">¿Estás seguro que quieres darte de baja de nuestros emails?</p>
        </div>

        <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">Email a dar de baja</h3>
                    <p class="text-sm text-yellow-700 mt-1">{{ $recipientEmail }}</p>
                </div>
            </div>
        </div>

        <div class="mb-6">
            <p class="text-sm text-gray-600 mb-4">
                Si confirmas la baja, ya no recibirás emails promocionales ni informativos de {{ $companyName }}.
                Sin embargo, podrías seguir recibiendo emails transaccionales importantes relacionados con tu cuenta.
            </p>
        </div>

        <form method="POST" action="{{ route('email.unsubscribe.process', $token) }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <button type="submit" name="confirm" value="yes" 
                        class="w-full bg-red-600 text-white py-2 px-4 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition duration-200">
                    Sí, darme de baja
                </button>
                
                <a href="/" 
                   class="w-full bg-gray-300 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition duration-200 text-center inline-block">
                    Cancelar
                </a>
            </div>
        </form>

        <div class="mt-8 text-center">
            <p class="text-xs text-gray-500">
                ¿Tienes preguntas? Contáctanos en 
                <a href="mailto:support@{{ parse_url(config('app.url'), PHP_URL_HOST) }}" 
                   class="text-blue-600 hover:text-blue-800">
                    support@{{ parse_url(config('app.url'), PHP_URL_HOST) }}
                </a>
            </p>
        </div>
    </div>
</body>
</html>