<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accès Refusé - Prometi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-primary-800">Prometi</h1>
            <p class="text-gray-600 mt-2">Suivi du Pointage des Collaborateurs sur Chantier</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-8 text-center">
            <div class="mb-6 flex justify-center">
                <div class="bg-red-100 p-4 rounded-full">
                    <i class="fas fa-lock text-red-500 text-5xl"></i>
                </div>
            </div>
            
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Accès Refusé</h2>
            <p class="text-gray-600 mb-6">Vous n'avez pas les autorisations nécessaires pour accéder à cette page.</p>
            
            <div class="flex justify-center">
                <a href="{{ url('/') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <i class="fas fa-home mr-2"></i> Retour à l'accueil
                </a>
            </div>
            
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-500">
                    Si vous pensez qu'il s'agit d'une erreur, veuillez contacter votre administrateur.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
