@extends('layouts.app')

@section('title', 'Modifier le Pointage')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Modifier le Pointage</h1>
        <p class="text-gray-600">Modifier les informations du pointage</p>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('time-entries.update', $timeEntry) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label for="project_id" class="block text-sm font-medium text-gray-700">Chantier</label>
                <select name="project_id" id="project_id" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    <option value="">Sélectionner un chantier</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ old('project_id', $timeEntry->project_id) == $project->id ? 'selected' : '' }}>
                            {{ $project->name }}
                        </option>
                    @endforeach
                </select>
                @error('project_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
                <input type="date" name="date" id="date" value="{{ old('date', $timeEntry->date->format('Y-m-d')) }}" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                @error('date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="check_in" class="block text-sm font-medium text-gray-700">Heure d'entrée</label>
                    <input type="time" name="check_in" id="check_in" value="{{ old('check_in', $timeEntry->check_in ? $timeEntry->check_in->format('H:i') : '') }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    @error('check_in')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="check_out" class="block text-sm font-medium text-gray-700">Heure de sortie</label>
                    <input type="time" name="check_out" id="check_out" value="{{ old('check_out', $timeEntry->check_out ? $timeEntry->check_out->format('H:i') : '') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    @error('check_out')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                <textarea name="notes" id="notes" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">{{ old('notes', $timeEntry->notes) }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="status" class="block text-sm font-medium text-gray-700">Statut</label>
                <select name="status" id="status" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    <option value="">Sélectionner un statut</option>
                    <option value="present" {{ old('status', $timeEntry->status) == 'present' ? 'selected' : '' }}>Présent</option>
                    <option value="absent" {{ old('status', $timeEntry->status) == 'absent' ? 'selected' : '' }}>Absent</option>
                    <option value="late" {{ old('status', $timeEntry->status) == 'late' ? 'selected' : '' }}>En retard</option>
                </select>
                @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('time-entries.index') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    Annuler
                </a>
                <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 