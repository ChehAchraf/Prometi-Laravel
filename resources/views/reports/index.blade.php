@extends('layouts.app')

@section('title', 'Rapports')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Tableau de Bord</h1>
        
        <div class="flex items-center space-x-4">
            <!-- Export Form -->
            <form action="{{ route('reports.export') }}" method="GET" class="flex items-center space-x-4">
                <div class="flex items-center space-x-2">
                    <input type="date" name="start_date" class="rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    <input type="date" name="end_date" class="rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    <select name="project_id" class="rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        <option value="">Tous les projets</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-800 focus:outline-none focus:border-green-800 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-file-excel mr-2"></i>
                    Exporter Excel
                </button>
            </form>
        </div>
    </div>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Rapports</h1>
        <p class="text-gray-600">Analyses et statistiques des pointages et chantiers</p>
    </div>

    <!-- Report Filters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <label for="report_type" class="block text-sm font-medium text-gray-700 mb-1">Type de Rapport</label>
                <select id="report_type" name="report_type" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 py-2 px-3 border">
                    <option value="heures_travaillees">Heures Travaillées</option>
                    <option value="heures_supplementaires">Heures Supplémentaires</option>
                    <option value="absences">Absences</option>
                    <option value="performance_chantier">Performance Chantier</option>
                    <option value="cout_main_oeuvre">Coût Main d'Œuvre</option>
                </select>
            </div>
            <div>
                <label for="date_debut" class="block text-sm font-medium text-gray-700 mb-1">Période du</label>
                <div class="relative">
                    <input type="text" id="date_debut" name="date_debut" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 py-2 px-3 border" placeholder="JJ/MM/AAAA">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <i class="fas fa-calendar-alt text-gray-400"></i>
                    </div>
                </div>
            </div>
            <div>
                <label for="date_fin" class="block text-sm font-medium text-gray-700 mb-1">au</label>
                <div class="relative">
                    <input type="text" id="date_fin" name="date_fin" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 py-2 px-3 border" placeholder="JJ/MM/AAAA">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <i class="fas fa-calendar-alt text-gray-400"></i>
                    </div>
                </div>
            </div>
            <div>
                <label for="project_id" class="block text-sm font-medium text-gray-700 mb-1">Chantier</label>
                <select id="project_id" name="project_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 py-2 px-3 border">
                    <option value="">Tous les chantiers</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="mt-4 flex justify-end">
            <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-md flex items-center">
                <i class="fas fa-search mr-2"></i> Générer Rapport
            </button>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="flex flex-wrap gap-4 mb-6">
        <button class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md flex items-center">
            <i class="fas fa-file-excel mr-2"></i> Exporter Excel
        </button>
        <button class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-md flex items-center">
            <i class="fas fa-file-pdf mr-2"></i> Exporter PDF
        </button>
        <button class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-md flex items-center">
            <i class="fas fa-envelope mr-2"></i> Envoyer par Email
        </button>
        <button class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-md flex items-center">
            <i class="fas fa-print mr-2"></i> Imprimer
        </button>
    </div>

    <!-- Report Tabs -->
    <div class="mb-6">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8">
                <button class="border-primary-500 text-primary-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" id="tab-summary">
                    Résumé
                </button>
                <button class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" id="tab-charts">
                    Graphiques
                </button>
                <button class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" id="tab-details">
                    Détails
                </button>
                <button class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" id="tab-saved">
                    Rapports Sauvegardés
                </button>
            </nav>
        </div>
    </div>

    <!-- Summary Tab Content -->
    <div id="content-summary" class="mb-6">
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <!-- Total Hours Card -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center mb-4">
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <i class="fas fa-clock text-blue-500"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-gray-500 text-sm">Total Heures Travaillées</h3>
                        <p class="text-2xl font-bold">{{ $stats['totalHours']['current'] }}</p>
                    </div>
                </div>
                <div class="text-sm text-gray-500">
                    Mois précédent: {{ $stats['totalHours']['previous'] }}
                    <span class="ml-2 {{ $stats['totalHours']['change'] >= 0 ? 'text-green-500' : 'text-red-500' }}">
                        {{ $stats['totalHours']['change'] >= 0 ? '+' : '' }}{{ $stats['totalHours']['change'] }}%
                    </span>
                </div>
            </div>

            <!-- Overtime Hours Card -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center mb-4">
                    <div class="bg-yellow-100 p-3 rounded-lg">
                        <i class="fas fa-user-clock text-yellow-500"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-gray-500 text-sm">Heures Supplémentaires</h3>
                        <p class="text-2xl font-bold">{{ $stats['overtimeHours']['current'] }}</p>
                    </div>
                </div>
                <div class="text-sm text-gray-500">
                    Mois précédent: {{ $stats['overtimeHours']['previous'] }}
                    <span class="ml-2 {{ $stats['overtimeHours']['change'] >= 0 ? 'text-green-500' : 'text-red-500' }}">
                        {{ $stats['overtimeHours']['change'] >= 0 ? '+' : '' }}{{ $stats['overtimeHours']['change'] }}%
                    </span>
                </div>
            </div>

            <!-- Active Employees Card -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center mb-4">
                    <div class="bg-green-100 p-3 rounded-lg">
                        <i class="fas fa-users text-green-500"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-gray-500 text-sm">Collaborateurs Actifs</h3>
                        <p class="text-2xl font-bold">{{ $stats['activeEmployees']['current'] }}</p>
                    </div>
                </div>
                <div class="text-sm text-gray-500">
                    Mois précédent: {{ $stats['activeEmployees']['previous'] }}
                    <span class="ml-2 {{ $stats['activeEmployees']['change'] >= 0 ? 'text-green-500' : 'text-red-500' }}">
                        {{ $stats['activeEmployees']['change'] >= 0 ? '+' : '' }}{{ $stats['activeEmployees']['change'] }}%
                    </span>
                </div>
            </div>

            <!-- Absence Rate Card -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center mb-4">
                    <div class="bg-red-100 p-3 rounded-lg">
                        <i class="fas fa-user-times text-red-500"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-gray-500 text-sm">Taux d'Absence</h3>
                        <p class="text-2xl font-bold">{{ $stats['absenceRate']['current'] }}%</p>
                    </div>
                </div>
                <div class="text-sm text-gray-500">
                    Mois précédent: {{ $stats['absenceRate']['previous'] }}%
                    <span class="ml-2 {{ $stats['absenceRate']['change'] <= 0 ? 'text-green-500' : 'text-red-500' }}">
                        {{ $stats['absenceRate']['change'] <= 0 ? '+' : '' }}{{ abs($stats['absenceRate']['change']) }}%
                    </span>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Heures Travaillées par Chantier</h3>
                <div class="h-80">
                    <canvas id="projectHoursChart"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Évolution des Heures Travaillées</h3>
                <div class="h-80">
                    <canvas id="hoursEvolutionChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Top Performers Table -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Top Collaborateurs</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Collaborateur</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fonction</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chantier</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Heures Travaillées</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Heures Supp.</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Performance</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($topPerformers as $employee)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full" src="{{ $employee->avatar_url }}" alt="">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $employee->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $employee->role }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $employee->project_name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ number_format($employee->total_hours, 0) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ number_format($employee->overtime_hours, 0) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-full bg-gray-200 rounded-full h-2.5 mr-2 w-24">
                                        <div class="bg-green-600 h-2.5 rounded-full" style="width: {{ $employee->performance }}%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ $employee->performance }}%</span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Project Performance Table -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Performance des Chantiers</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chantier</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chef de Projet</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Collaborateurs</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Heures Totales</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progression</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($projects as $project)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $project->name }}</div>
                                <div class="text-xs text-gray-500">{{ $project->location }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <img class="h-8 w-8 rounded-full" src="{{ $project->manager_avatar_url }}" alt="">
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $project->manager_name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $project->employees_count }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ number_format($project->total_hours, 0) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-full bg-gray-200 rounded-full h-2.5 mr-2 w-24">
                                        <div class="bg-primary-600 h-2.5 rounded-full" style="width: {{ $project->progress }}%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ $project->progress }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $project->status === 'completed' ? 'bg-gray-100 text-gray-800' : 'bg-green-100 text-green-800' }}">
                                    {{ $project->status === 'completed' ? 'Terminé' : 'En cours' }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Charts Tab Content -->
    <div id="content-charts" class="hidden mb-6">
        <!-- Additional charts will be added here -->
    </div>

    <!-- Details Tab Content -->
    <div id="content-details" class="hidden mb-6">
        <!-- Detailed reports will be added here -->
    </div>

    <!-- Saved Reports Tab Content -->
    <div id="content-saved" class="hidden mb-6">
        <!-- Saved reports list will be added here -->
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize date pickers
    flatpickr("#date_debut", {
        dateFormat: "d/m/Y",
        defaultDate: "01/03/2025"
    });

    flatpickr("#date_fin", {
        dateFormat: "d/m/Y",
        defaultDate: "31/03/2025"
    });

    // Tab switching
    const tabs = ['summary', 'charts', 'details', 'saved'];
    
    tabs.forEach(tab => {
        document.getElementById(`tab-${tab}`).addEventListener('click', function() {
            // Hide all content
            tabs.forEach(t => {
                document.getElementById(`content-${t}`).classList.add('hidden');
                document.getElementById(`tab-${t}`).classList.remove('border-primary-500', 'text-primary-600');
                document.getElementById(`tab-${t}`).classList.add('border-transparent', 'text-gray-500');
            });
            
            // Show selected content
            document.getElementById(`content-${tab}`).classList.remove('hidden');
            document.getElementById(`tab-${tab}`).classList.remove('border-transparent', 'text-gray-500');
            document.getElementById(`tab-${tab}`).classList.add('border-primary-500', 'text-primary-600');
        });
    });

    // Initialize Charts
    const projectHoursChart = new Chart(document.getElementById('projectHoursChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($projectNames) !!},
            datasets: [{
                label: 'Heures Travaillées',
                data: {!! json_encode($projectHours) !!},
                backgroundColor: 'rgba(14, 165, 233, 0.7)',
                borderColor: 'rgba(14, 165, 233, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    const hoursEvolutionChart = new Chart(document.getElementById('hoursEvolutionChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: {!! json_encode($monthLabels) !!},
            datasets: [{
                label: 'Heures Normales',
                data: {!! json_encode($regularHours) !!},
                backgroundColor: 'rgba(14, 165, 233, 0.2)',
                borderColor: 'rgba(14, 165, 233, 1)',
                borderWidth: 2,
                tension: 0.3
            },
            {
                label: 'Heures Supplémentaires',
                data: {!! json_encode($overtimeHoursData) !!},
                backgroundColor: 'rgba(234, 179, 8, 0.2)',
                borderColor: 'rgba(234, 179, 8, 1)',
                borderWidth: 2,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
});
</script>
@endpush 