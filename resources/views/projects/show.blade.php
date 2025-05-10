@extends('layouts.app')

@section('title', 'Détails du Chantier')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ $project->name }}</h1>
            <p class="text-gray-600">Détails du chantier</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('projects.edit', $project) }}" class="bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                <i class="fas fa-edit mr-2"></i>Modifier
            </a>
            <a href="{{ route('projects.index') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                <i class="fas fa-arrow-left mr-2"></i>Retour
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">{{ $project->name }}</h2>
                    <p class="text-sm text-gray-500">{{ $project->location }}</p>
                </div>
                <span class="px-3 py-1 rounded-full text-sm font-semibold
                    @if($project->status === 'active') bg-green-100 text-green-800
                    @elseif($project->status === 'completed') bg-blue-100 text-blue-800
                    @else bg-yellow-100 text-yellow-800 @endif">
                    {{ ucfirst($project->status) }}
                </span>
            </div>
        </div>

        <div class="px-6 py-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Description</h3>
                    <p class="text-gray-600">{{ $project->description }}</p>
                </div>

                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informations</h3>
                    <dl class="grid grid-cols-1 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Date de début</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $project->start_date->format('d/m/Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Date de fin</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $project->end_date ? $project->end_date->format('d/m/Y') : 'Non définie' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Localisation</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $project->location }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Actions</h3>
            <div class="flex space-x-4">
                <a href="{{ route('time-entries.create', ['project_id' => $project->id]) }}" class="bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    <i class="fas fa-clock mr-2"></i>Nouveau pointage
                </a>
                <a href="{{ route('reports.create', ['project_id' => $project->id]) }}" class="bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    <i class="fas fa-chart-bar mr-2"></i>Nouveau rapport
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 