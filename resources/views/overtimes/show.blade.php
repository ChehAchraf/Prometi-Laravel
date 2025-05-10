@extends('layouts.app')

@section('title', 'Détails des Heures Supplémentaires')

@section('content')
<div class="container mx-auto px-4">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Détails des Heures Supplémentaires</h1>
            <p class="text-gray-600">Informations détaillées sur la demande d'heures supplémentaires</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('overtimes.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                Retour
            </a>
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

    <!-- Overtime Details -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Informations de la Demande</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Employee Information -->
                <div class="col-span-1">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informations de l'Employé</h3>
                    <div class="flex items-center space-x-4">
                        <img class="h-16 w-16 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($overtime->user->name) }}&background=random" alt="">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $overtime->user->name }}</p>
                            <p class="text-sm text-gray-500">{{ $overtime->user->email }}</p>
                            <p class="text-sm text-gray-500">{{ $overtime->user->phone }}</p>
                        </div>
                    </div>
                </div>

                <!-- Project Information -->
                <div class="col-span-1">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informations du Projet</h3>
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $overtime->project->name }}</p>
                        <p class="text-sm text-gray-500">{{ $overtime->project->location }}</p>
                    </div>
                </div>

                <!-- Overtime Details -->
                <div class="col-span-1">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Détails des Heures Supplémentaires</h3>
                    <div class="space-y-2">
                        <p class="text-sm">
                            <span class="font-medium text-gray-900">Date:</span>
                            <span class="text-gray-500">{{ $overtime->date->format('d/m/Y') }}</span>
                        </p>
                        <p class="text-sm">
                            <span class="font-medium text-gray-900">Heures:</span>
                            <span class="text-gray-500">{{ number_format($overtime->hours, 2) }} heures</span>
                        </p>
                        <p class="text-sm">
                            <span class="font-medium text-gray-900">Statut:</span>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $overtime->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                  ($overtime->status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($overtime->status) }}
                            </span>
                        </p>
                    </div>
                </div>

                <!-- Approval Information -->
                @if($overtime->status !== 'pending')
                <div class="col-span-1">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informations d'Approval</h3>
                    <div class="space-y-2">
                        <p class="text-sm">
                            <span class="font-medium text-gray-900">Approuvé/Rejeté par:</span>
                            <span class="text-gray-500">{{ $overtime->approver->name }}</span>
                        </p>
                        <p class="text-sm">
                            <span class="font-medium text-gray-900">Date:</span>
                            <span class="text-gray-500">{{ $overtime->updated_at->format('d/m/Y H:i') }}</span>
                        </p>
                        @if($overtime->status === 'rejected')
                        <p class="text-sm">
                            <span class="font-medium text-gray-900">Raison du rejet:</span>
                            <span class="text-gray-500">{{ $overtime->rejection_reason }}</span>
                        </p>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <!-- Actions -->
            @if($overtime->isPending() && auth()->user()->can('approve', $overtime))
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" 
                        class="px-4 py-2 bg-green-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                        data-toggle="modal" 
                        data-target="#approveModal">
                    Approuver
                </button>
                <button type="button" 
                        class="px-4 py-2 bg-red-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                        data-toggle="modal" 
                        data-target="#rejectModal">
                    Rejeter
                </button>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content bg-white rounded-lg shadow-xl transform transition-all">
            <form action="{{ route('overtimes.approve', $overtime) }}" method="POST">
                @csrf
                <div class="modal-header flex items-center justify-between p-4 border-b border-gray-200">
                    <h5 class="text-lg font-semibold text-gray-900">Approuver la Demande</h5>
                    <button type="button" class="text-gray-400 hover:text-gray-500" data-dismiss="modal">
                        <span class="sr-only">Fermer</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="modal-body p-6">
                    <div class="space-y-4">
                        <div>
                            <label for="reason" class="block text-sm font-medium text-gray-700">Raison d'approbation (Optionnel)</label>
                            <input type="text" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" 
                                   id="reason" 
                                   name="reason" 
                                   placeholder="Entrez une raison (optionnel)">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse rounded-b-lg">
                    <button type="submit" 
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Approuver
                    </button>
                    <button type="button" 
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" 
                            data-dismiss="modal">
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content bg-white rounded-lg shadow-xl transform transition-all">
            <form action="{{ route('overtimes.reject', $overtime) }}" method="POST">
                @csrf
                <div class="modal-header flex items-center justify-between p-4 border-b border-gray-200">
                    <h5 class="text-lg font-semibold text-gray-900">Rejeter la Demande</h5>
                    <button type="button" class="text-gray-400 hover:text-gray-500" data-dismiss="modal">
                        <span class="sr-only">Fermer</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="modal-body p-6">
                    <div class="space-y-4">
                        <div>
                            <label for="rejection_reason" class="block text-sm font-medium text-gray-700">Raison du rejet <span class="text-red-500">*</span></label>
                            <input type="text" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" 
                                   id="rejection_reason" 
                                   name="rejection_reason" 
                                   required 
                                   placeholder="Entrez la raison du rejet">
                            <p class="mt-1 text-sm text-gray-500">Veuillez fournir une raison pour le rejet de cette demande.</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse rounded-b-lg">
                    <button type="submit" 
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Rejeter
                    </button>
                    <button type="button" 
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" 
                            data-dismiss="modal">
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 