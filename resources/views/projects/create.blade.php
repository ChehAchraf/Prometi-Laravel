@extends('layouts.app')

@section('title', 'Nouveau Chantier')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Nouveau Chantier</h1>
        <p class="text-gray-600">Créer un nouveau chantier</p>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('projects.store') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Nom du chantier</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="description" rows="3" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="location" class="block text-sm font-medium text-gray-700">Localisation</label>
                <input type="text" name="location" id="location" value="{{ old('location') }}" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                @error('location')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="start_date" class="block text-sm font-medium text-gray-700">Date de début</label>
                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    @error('start_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="end_date" class="block text-sm font-medium text-gray-700">Date de fin</label>
                    <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    @error('end_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <label for="status" class="block text-sm font-medium text-gray-700">Statut</label>
                <select name="status" id="status" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    <option value="">Sélectionner un statut</option>
                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>En cours</option>
                    <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Terminé</option>
                    <option value="on_hold" {{ old('status') == 'on_hold' ? 'selected' : '' }}>En pause</option>
                </select>
                @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('projects.index') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    Annuler
                </a>
                <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    Créer le chantier
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 