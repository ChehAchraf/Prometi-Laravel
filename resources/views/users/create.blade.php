@extends('layouts.app')

@section('title', 'Nouvel Utilisateur')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Nouvel Utilisateur</h1>
        <p class="text-gray-600">Créer un nouvel utilisateur dans le système</p>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Nom complet</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
                <input type="password" name="password" id="password" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmer le mot de passe</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
            </div>

            <div class="mb-4">
                <label for="role" class="block text-sm font-medium text-gray-700">Rôle</label>
                <select name="role" id="role" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    <option value="">Sélectionner un rôle</option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrateur</option>
                    <option value="manager" {{ old('role') == 'manager' ? 'selected' : '' }}>Gestionnaire</option>
                    <option value="worker" {{ old('role') == 'worker' ? 'selected' : '' }}>Ouvrier</option>
                </select>
                @error('role')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="phone" class="block text-sm font-medium text-gray-700">Téléphone</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                @error('phone')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="address" class="block text-sm font-medium text-gray-700">Adresse</label>
                <textarea name="address" id="address" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">{{ old('address') }}</textarea>
                @error('address')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('users.index') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    Annuler
                </a>
                <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    Créer l'utilisateur
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 