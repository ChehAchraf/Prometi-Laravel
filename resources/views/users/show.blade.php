@extends('layouts.app')

@section('title', 'Détails de l\'Utilisateur')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Détails de l'Utilisateur</h1>
            <p class="text-gray-600">Informations détaillées de l'utilisateur</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('users.edit', $user) }}" class="bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                <i class="fas fa-edit mr-2"></i>Modifier
            </a>
            <a href="{{ route('users.index') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                <i class="fas fa-arrow-left mr-2"></i>Retour
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-16 w-16">
                    <img class="h-16 w-16 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}" alt="">
                </div>
                <div class="ml-4">
                    <h2 class="text-xl font-semibold text-gray-900">{{ $user->name }}</h2>
                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                </div>
            </div>
        </div>

        <div class="px-6 py-4">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Rôle</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            @if($user->role === 'admin') bg-red-100 text-red-800
                            @elseif($user->role === 'manager') bg-yellow-100 text-yellow-800
                            @else bg-green-100 text-green-800 @endif">
                            {{ ucfirst($user->role) }}
                        </span>
                    </dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Téléphone</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $user->phone ?? 'Non spécifié' }}</dd>
                </div>

                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Adresse</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $user->address ?? 'Non spécifiée' }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Date de création</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('d/m/Y H:i') }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Dernière mise à jour</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $user->updated_at->format('d/m/Y H:i') }}</dd>
                </div>
            </dl>
        </div>
    </div>
</div>
@endsection 