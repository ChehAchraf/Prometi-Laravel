@extends('layouts.app')

@section('title', 'Heures Supplémentaires')

@section('content')
<div class="container mx-auto px-4">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Heures Supplémentaires</h1>
            <p class="text-gray-600">Gérer les demandes d'heures supplémentaires</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Overtime Requests Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Demandes d'Heures Supplémentaires</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="overtimesTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employé</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Projet</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Heures</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($overtimes as $overtime)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($overtime->user->name) }}&background=random" alt="">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $overtime->user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $overtime->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $overtime->project->name }}</div>
                                <div class="text-sm text-gray-500">{{ $overtime->project->location }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $overtime->date->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ number_format($overtime->hours, 2) }} heures
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $overtime->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                      ($overtime->status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($overtime->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('overtimes.show', $overtime) }}" 
                                       class="text-primary-600 hover:text-primary-900"
                                       title="Voir les détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($overtime->isPending() && auth()->user()->can('approve', $overtime))
                                        <button type="button" 
                                                class="text-green-600 hover:text-green-900"
                                                data-toggle="modal" 
                                                data-target="#approveModal{{ $overtime->id }}"
                                                title="Approuver">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" 
                                                class="text-red-600 hover:text-red-900"
                                                data-toggle="modal" 
                                                data-target="#rejectModal{{ $overtime->id }}"
                                                title="Rejeter">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                Aucune demande d'heures supplémentaires trouvée
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $overtimes->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#overtimesTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json"
            },
            "order": [[2, "desc"]], // Sort by date descending
            "pageLength": 10
        });
    });
</script>
@endpush
@endsection 