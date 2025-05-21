<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }} | @yield('title', 'Dashboard')</title>
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
    @stack('styles')

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Custom Styles -->
    <style>
        /* Chef search specific styles */
        .chef-search-results {
            max-height: 250px;
            overflow-y: auto;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 100;
            width: 100%;
        }
        
        .chef-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.35rem 0.75rem;
            margin: 0.25rem;
            background-color: #f3f4f6;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            line-height: 1.25rem;
        }
        
        .remove-chef, .remove-additional-chef {
            background: none;
            border: none;
            color: #6b7280;
            cursor: pointer;
            margin-left: 0.5rem;
            padding: 0.125rem 0.25rem;
        }
        
        .remove-chef:hover, .remove-additional-chef:hover {
            color: #ef4444;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 bg-primary-800 text-white w-64 transform transition-transform duration-150 ease-in lg:translate-x-0 -translate-x-full" id="sidebar">
        <div class="flex items-center justify-between p-4 border-b border-primary-700">
            <div class="flex items-center space-x-2">
                <i class="fas fa-clock text-2xl"></i>
                <span class="text-xl font-semibold">Suivi Pointage</span>
            </div>
            <button class="lg:hidden text-white focus:outline-none" id="closeSidebar">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <nav class="mt-5">
            <a href="{{ route('dashboard') }}" class="flex items-center py-3 px-4 text-white {{ request()->routeIs('dashboard') ? 'bg-primary-700' : 'hover:bg-primary-700' }}">
                <i class="fas fa-tachometer-alt w-6"></i>
                <span>Tableau de bord</span>
            </a>
            <a href="{{ route('projects.index') }}" class="flex items-center py-3 px-4 text-white {{ request()->routeIs('projects.*') ? 'bg-primary-700' : 'hover:bg-primary-700' }}">
                <i class="fas fa-building w-6"></i>
                <span>Chantiers</span>
            </a>
            <a href="{{ route('time-entries.index') }}" class="flex items-center py-3 px-4 text-white {{ request()->routeIs('time-entries.*') ? 'bg-primary-700' : 'hover:bg-primary-700' }}">
                <i class="fas fa-clipboard-check w-6"></i>
                <span>Pointages</span>
            </a>
            <a href="{{ route('reports.index') }}" class="flex items-center py-3 px-4 text-white {{ request()->routeIs('reports.*') ? 'bg-primary-700' : 'hover:bg-primary-700' }}">
                <i class="fas fa-chart-bar w-6"></i>
                <span>Rapports</span>
            </a>
            
            @if(Auth::user()->role && Auth::user()->role->role === 'superadmin' || 
                Auth::user()->role && Auth::user()->role->role === 'hr_editor')
            <a href="{{ route('users.index') }}" class="flex items-center py-3 px-4 text-white {{ request()->routeIs('users.*') ? 'bg-primary-700' : 'hover:bg-primary-700' }}">
                <i class="fas fa-users w-6"></i>
                <span>Utilisateurs</span>
            </a>
            <a href="{{ route('user-statuses.index') }}" class="flex items-center py-3 px-4 text-white {{ request()->routeIs('user-statuses.*') ? 'bg-primary-700' : 'hover:bg-primary-700' }}">
                <i class="fas fa-user-tag w-6"></i>
                <span>Statuts Utilisateurs</span>
            </a>
            @endif
            
            <a href="{{ route('overtimes.index') }}" class="flex items-center py-3 px-4 text-white {{ request()->routeIs('overtimes.*') ? 'bg-primary-700' : 'hover:bg-primary-700' }}">
                <i class="fas fa-clock w-6"></i>
                <span>Heures Supplémentaires</span>
            </a>
            
            <form method="POST" action="{{ route('logout') }}" class="mt-auto">
                @csrf
                <button type="submit" class="flex items-center w-full py-3 px-4 text-white hover:bg-primary-700">
                    <i class="fas fa-sign-out-alt w-6"></i>
                    <span>Déconnexion</span>
                </button>
            </form>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="lg:ml-64 transition-all duration-150 ease-in" id="main">
        <!-- Top Navigation -->
        <header class="bg-white shadow-md">
            <div class="flex items-center justify-between p-4">
                <button class="lg:hidden focus:outline-none" id="openSidebar">
                    <i class="fas fa-bars text-primary-800"></i>
                </button>
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <button class="focus:outline-none">
                            <i class="fas fa-bell text-gray-500"></i>
                            <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>
                    </div>
                    <div class="flex items-center space-x-2">
                        <img src="https://randomuser.me/api/portraits/men/1.jpg" alt="Profile" class="w-8 h-8 rounded-full">
                        <span class="hidden md:inline-block">{{ Auth::user()->name }}</span>
                        <i class="fas fa-chevron-down text-xs text-gray-500"></i>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="p-6">
            @yield('content')
        </main>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Base Scripts -->
    <script>
        // Sidebar toggle functionality
        const sidebar = document.getElementById('sidebar');
        const main = document.getElementById('main');
        const openSidebar = document.getElementById('openSidebar');
        const closeSidebar = document.getElementById('closeSidebar');

        openSidebar.addEventListener('click', () => {
            sidebar.classList.remove('-translate-x-full');
        });

        closeSidebar.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
        });
    </script>
    
    @stack('scripts')
    @yield('scripts')
</body>
</html> 