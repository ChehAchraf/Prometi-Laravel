@extends('layouts.app')

@section('title', 'Détails du Pointage')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Détails du Pointage</h1>
            <p class="text-gray-600">Informations détaillées du pointage</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('time-entries.edit', $timeEntry) }}" class="bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                <i class="fas fa-edit mr-2"></i>Modifier
            </a>
            <a href="{{ route('time-entries.index') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                <i class="fas fa-arrow-left mr-2"></i>Retour
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">{{ $timeEntry->user->name }}</h2>
                    <p class="text-sm text-gray-500">{{ $timeEntry->project->name }}</p>
                </div>
                <span class="px-3 py-1 rounded-full text-sm font-semibold
                    @if($timeEntry->status === 'present') bg-green-100 text-green-800
                    @elseif($timeEntry->status === 'absent') bg-red-100 text-red-800
                    @else bg-yellow-100 text-yellow-800 @endif">
                    {{ ucfirst($timeEntry->status) }}
                </span>
            </div>
        </div>

        <div class="px-6 py-4">
            <dl class="grid grid-cols-1 gap-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Date</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $timeEntry->date->format('d/m/Y') }}</dd>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Heure d'entrée</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $timeEntry->check_in ? $timeEntry->check_in->format('H:i') : 'Non spécifiée' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Heure de sortie</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $timeEntry->check_out ? $timeEntry->check_out->format('H:i') : 'Non spécifiée' }}
                        </dd>
                    </div>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Chantier</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $timeEntry->project->name }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Localisation</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $timeEntry->project->location }}</dd>
                </div>

                @if($timeEntry->notes)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Notes</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $timeEntry->notes }}</dd>
                    </div>
                @endif
            </dl>
        </div>
    </div>
</div>
@endsection 