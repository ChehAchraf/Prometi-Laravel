@extends('layouts.app')

@section('title', 'Tableau de Bord')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Tableau de Bord</h1>
    <p class="text-gray-600">Vue d'ensemble du suivi de pointage</p>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Total Collaborateurs</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $totalUsers ?? 0 }}</h3>
            </div>
            <div class="bg-primary-100 p-3 rounded-full">
                <i class="fas fa-users text-primary-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Chantiers Actifs</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $activeProjects ?? 0 }}</h3>
            </div>
            <div class="bg-primary-100 p-3 rounded-full">
                <i class="fas fa-building text-primary-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Présents Aujourd'hui</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $presentToday ?? 0 }}</h3>
            </div>
            <div class="bg-primary-100 p-3 rounded-full">
                <i class="fas fa-user-check text-primary-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Absents Aujourd'hui</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $absentToday ?? 0 }}</h3>
            </div>
            <div class="bg-primary-100 p-3 rounded-full">
                <i class="fas fa-user-times text-primary-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Activité Récente</h2>
    <div class="space-y-4">
        @forelse($recentActivities ?? [] as $activity)
        <div class="flex items-center space-x-4">
            <div class="flex-shrink-0">
                <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center">
                    <i class="fas {{ $activity->icon }} text-primary-600"></i>
                </div>
            </div>
            <div class="flex-1">
                <p class="text-sm text-gray-800">{{ $activity->description }}</p>
                <p class="text-xs text-gray-500">{{ $activity->time }}</p>
            </div>
        </div>
        @empty
        <p class="text-gray-500 text-center">Aucune activité récente</p>
        @endforelse
    </div>
</div>

<!-- Today's Attendance -->
<div class="bg-white rounded-lg shadow-md p-6 mt-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Pointages du Jour</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Collaborateur</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chantier</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Heure d'arrivée</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($todayAttendance ?? [] as $attendance)
                <tr class="@if($attendance->status !== 'present') bg-red-100 border-l-4 border-red-400 @endif">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <img class="h-10 w-10 rounded-full" src="{{ $attendance->user->avatar }}" alt="">
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $attendance->user->name }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $attendance->project->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $attendance->check_in }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $attendance->status === 'present' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $attendance->status }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">Aucun pointage enregistré aujourd'hui</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection 