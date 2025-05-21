@extends('layouts.app')

@section('title', 'Gestion des Pointages')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Gestion des Pointages</h1>
        <p class="text-gray-600">Suivi des pointages des collaborateurs</p>
    </div>
    <a href="{{ route('time-entries.create') }}" class="bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
        <i class="fas fa-plus mr-2"></i>Nouveau Pointage
    </a>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
@endif

<!-- Filter Section -->
<div class="bg-white rounded-lg shadow-md p-4 mb-6">
    <form action="{{ route('time-entries.index') }}" method="GET" class="flex flex-wrap items-end gap-4">
        <div class="w-full md:w-auto flex-1">
            <label for="project_filter" class="block text-sm font-medium text-gray-700 mb-1">Filtrer par Chantier</label>
            <select name="project_id" id="project_filter" class="mt-1 block w-full rounded-md border-gray-300 border py-1.5 px-2 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                <option value="">Tous les chantiers</option>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                        {{ $project->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="w-full md:w-auto flex items-end">
            <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                <i class="fas fa-filter mr-2"></i>Filtrer
            </button>
            @if(request()->has('project_id') && request('project_id') != '')
                <a href="{{ route('time-entries.index') }}" class="ml-2 bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    <i class="fas fa-times mr-2"></i>Réinitialiser
                </a>
            @endif
        </div>
    </form>
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Collaborateur</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chantier</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Heure d'arrivée</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">jour type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($timeEntries as $entry)
                <tr class="@if($entry->status === 'absent') bg-red-100 border-l-4 border-red-400 @endif">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <img class="h-10 w-10 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($entry->user->name) }}" alt="">
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $entry->user->name }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $entry->project->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $entry->date->format('d/m/Y') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $entry->check_in ? $entry->check_in->format('H:i') : '-' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            @if($entry->status === 'present') bg-green-100 text-green-800
                            @elseif($entry->status === 'absent') bg-red-100 text-red-800
                            @elseif($entry->status === 'late') bg-yellow-100 text-yellow-800
                            @else bg-orange-100 text-orange-800 @endif">
                            {{ ucfirst($entry->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $entry->jour_type }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('time-entries.show', $entry) }}" class="text-primary-600 hover:text-primary-900 mr-3">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('time-entries.edit', $entry) }}" class="text-primary-600 hover:text-primary-900 mr-3">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('time-entries.destroy', $entry) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce pointage ?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                        Aucun pointage trouvé
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $timeEntries->links() }}
</div>
@endsection 