<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Baja confirmada - {{ $companyName }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 py-12">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-8">
        <div class="text-center mb-8">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">¡Baja confirmada!</h1>
            <p class="text-gray-600">Te has dado de baja exitosamente de nuestros emails.</p>
        </div>

        <div class="bg-green-50 border border-green-200 rounded-md p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-green-800">Email dado de baja</h3>
                    <p class="text-sm text-green-700 mt-1">{{ $recipientEmail }}</p>
                </div>
            </div>
        </div>

        <div class="text-center text-sm text-gray-600 space-y-2 mb-6">
            <p>Ya no recibirás emails promocionales ni informativos de {{ $companyName }}.</p>
            <p>Es posible que aún recibas emails transaccionales importantes relacionados con tu cuenta o servicios activos.</p>
        </div>

        <div class="text-center">
            <a href="/" 
               class="inline-block bg-blue-600 text-white py-2 px-6 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200">
                Volver al sitio web
            </a>
        </div>

        <div class="mt-8 text-center">
            <p class="text-xs text-gray-500 mb-2">
                ¿Cambiaste de opinión? Puedes suscribirte nuevamente en cualquier momento.
            </p>
            <p class="text-xs text-gray-500">
                ¿Preguntas? Contáctanos en 
                <a href="mailto:support@{{ parse_url(config('app.url'), PHP_URL_HOST) }}" 
                   class="text-blue-600 hover:text-blue-800">
                    support@{{ parse_url(config('app.url'), PHP_URL_HOST) }}
                </a>
            </p>
        </div>
    </div>
</body>
</html>