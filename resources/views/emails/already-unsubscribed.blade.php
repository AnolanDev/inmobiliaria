<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ya dado de baja - {{ $companyName }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 py-12">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-8">
        <div class="text-center mb-8">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 mb-4">
                <svg class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Ya estás dado de baja</h1>
            <p class="text-gray-600">Este email ya se encuentra dado de baja de nuestros envíos.</p>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Email ya dado de baja</h3>
                    <p class="text-sm text-blue-700 mt-1">{{ $recipientEmail }}</p>
                </div>
            </div>
        </div>

        <div class="text-center text-sm text-gray-600 space-y-2 mb-6">
            <p>Este email address ya había sido dado de baja anteriormente de nuestros envíos promocionales e informativos.</p>
            <p>No necesitas hacer nada más.</p>
        </div>

        <div class="text-center">
            <a href="/" 
               class="inline-block bg-blue-600 text-white py-2 px-6 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200">
                Volver al sitio web
            </a>
        </div>

        <div class="mt-8 text-center">
            <p class="text-xs text-gray-500 mb-2">
                ¿Quieres volver a recibir nuestros emails? Puedes suscribirte nuevamente en nuestro sitio web.
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