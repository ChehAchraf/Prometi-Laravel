@extends('layouts.app')

@section('title', 'Modifier le Chantier')

@section('styles')
<style>
    .chef-search-results {
        max-height: 200px;
        overflow-y: auto;
    }
    .selected-chefs {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 8px;
    }
    .chef-badge {
        display: flex;
        align-items: center;
        background-color: #EBF5FF;
        color: #1E40AF;
        padding: 4px 8px;
        border-radius: 4px;
    }
    .chef-badge button {
        margin-left: 8px;
        color: #6B7280;
    }
</style>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Modifier le Chantier</h1>
        <p class="text-gray-600">Modifier les informations du chantier</p>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('projects.update', $project) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Nom du chantier</label>
                <input type="text" name="name" id="name" value="{{ old('name', $project->name) }}" required
                    class="mt-1 block w-full rounded-md border-gray-300 border py-1.5 px-2 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="description" rows="3" required
                    class="mt-1 block w-full rounded-md border-gray-300 border py-1.5 px-2 shadow-sm focus:border-primary-500 focus:ring-primary-500">{{ old('description', $project->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="location" class="block text-sm font-medium text-gray-700">Localisation</label>
                <input type="text" name="location" id="location" value="{{ old('location', $project->location) }}" required
                    class="mt-1 block w-full rounded-md border-gray-300 border py-1.5 px-2 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                @error('location')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="start_date" class="block text-sm font-medium text-gray-700">Date de début</label>
                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $project->start_date->format('Y-m-d')) }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 border py-1.5 px-2 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    @error('start_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="end_date" class="block text-sm font-medium text-gray-700">Date de fin</label>
                    <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $project->end_date ? $project->end_date->format('Y-m-d') : '') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 border py-1.5 px-2 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    @error('end_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <label for="status" class="block text-sm font-medium text-gray-700">Statut</label>
                <select name="status" id="status" required
                    class="mt-1 block w-full rounded-md border-gray-300 border py-1.5 px-2 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    <option value="">Sélectionner un statut</option>
                    <option value="active" {{ old('status', $project->status) == 'active' ? 'selected' : '' }}>En cours</option>
                    <option value="completed" {{ old('status', $project->status) == 'completed' ? 'selected' : '' }}>Terminé</option>
                    <option value="on_hold" {{ old('status', $project->status) == 'on_hold' ? 'selected' : '' }}>En pause</option>
                </select>
                @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Chef de Chantier Search Section -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Chef de Chantier Principal</label>
                <div class="relative">
                    <input type="text" id="chef_search" 
                        placeholder="Rechercher un chef de chantier principal..."
                        class="block w-full rounded-md border-gray-300 border py-1.5 px-2 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    
                    <!-- Search Results -->
                    <div id="chef_search_results" class="absolute left-0 z-50 w-full bg-white mt-1 rounded-md shadow-lg border overflow-y-auto max-h-60 hidden">
                        <!-- Search results will be added here dynamically -->
                    </div>
                </div>
                
                <!-- Selected Chef Container -->
                <div id="selected_chef" class="mt-2">
                    <!-- Selected chef will be added here via JavaScript -->
                </div>
                <input type="hidden" id="chef_id" name="chef_id" value="{{ old('chef_id', $project->chef_id) }}">
                
                @error('chef_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Additional Chefs de Chantier Search Section -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Chefs de Chantier Supplémentaires</label>
                <div class="relative">
                    <input type="text" id="additional_chef_search" 
                        placeholder="Rechercher des chefs de chantier supplémentaires..."
                        class="block w-full rounded-md border-gray-300 border py-1.5 px-2 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    
                    <!-- Search Results -->
                    <div id="additional_chef_search_results" class="absolute left-0 z-50 w-full bg-white mt-1 rounded-md shadow-lg border overflow-y-auto max-h-60 hidden">
                        <!-- Search results will be added here dynamically -->
                    </div>
                </div>
                
                <!-- Selected Chefs Container -->
                <div id="selected_chefs" class="mt-2 flex flex-wrap">
                    <!-- Selected chefs will be added here via JavaScript -->
                </div>
                
                @error('chef_ids')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('projects.index') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
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

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Main Chef de Chantier
        const chefSearch = document.getElementById('chef_search');
        const searchResults = document.getElementById('chef_search_results');
        const selectedChef = document.getElementById('selected_chef');
        const chefIdInput = document.getElementById('chef_id');
        let selectedChefId = parseInt(chefIdInput.value) || null;
        
        // Additional Chefs de Chantier
        const additionalChefSearch = document.getElementById('additional_chef_search');
        const additionalSearchResults = document.getElementById('additional_chef_search_results');
        const selectedChefs = document.getElementById('selected_chefs');
        const selectedChefIds = new Set();
        
        // Initialize with current chef if exists
        @if($project->chef)
            selectedChefId = {{ $project->chef->id }};
            localStorage.setItem(`chef_{{ $project->chef->id }}`, JSON.stringify({
                id: {{ $project->chef->id }},
                name: "{{ $project->chef->name }}",
                email: "{{ $project->chef->email }}"
            }));
            renderSelectedChef();
        @endif
        
        // Initialize with current assigned chefs
        @foreach($project->users as $chef)
            @if(!$project->chef_id || $chef->id != $project->chef_id)
                selectedChefIds.add({{ $chef->id }});
                localStorage.setItem(`chef_{{ $chef->id }}`, JSON.stringify({
                    id: {{ $chef->id }},
                    name: "{{ $chef->name }}",
                    email: "{{ $chef->email }}"
                }));
            @endif
        @endforeach
        
        // Render initially selected additional chefs
        renderSelectedChefs();
        
        // Function to render selected main chef
        function renderSelectedChef() {
            // Clear the container
            selectedChef.innerHTML = '';
            
            if (selectedChefId) {
                const chefData = JSON.parse(localStorage.getItem(`chef_${selectedChefId}`));
                if (chefData) {
                    const badge = document.createElement('div');
                    badge.className = 'chef-badge';
                    badge.innerHTML = `
                        ${chefData.name}
                        <button type="button" id="remove-chef" class="remove-chef">
                            <i class="fas fa-times"></i>
                        </button>
                    `;
                    selectedChef.appendChild(badge);
                    
                    // Update hidden input
                    chefIdInput.value = chefData.id;
                    
                    // Add event listener to remove button
                    document.getElementById('remove-chef').addEventListener('click', function() {
                        selectedChefId = null;
                        chefIdInput.value = '';
                        renderSelectedChef();
                    });
                }
            }
        }
        
        // Function to render selected additional chefs
        function renderSelectedChefs() {
            // Clear the container
            selectedChefs.innerHTML = '';
            
            // Add each selected chef as a badge
            selectedChefIds.forEach(chefId => {
                // Skip if this chef is already the main chef
                if (chefId === selectedChefId) return;
                
                const chefData = JSON.parse(localStorage.getItem(`chef_${chefId}`));
                if (chefData) {
                    const badge = document.createElement('div');
                    badge.className = 'chef-badge';
                    badge.innerHTML = `
                        ${chefData.name}
                        <input type="hidden" name="chef_ids[]" value="${chefData.id}">
                        <button type="button" data-id="${chefData.id}" class="remove-additional-chef">
                            <i class="fas fa-times"></i>
                        </button>
                    `;
                    selectedChefs.appendChild(badge);
                }
            });
            
            // Add event listeners to remove buttons
            document.querySelectorAll('.remove-additional-chef').forEach(btn => {
                btn.addEventListener('click', function() {
                    const chefId = parseInt(this.getAttribute('data-id'));
                    selectedChefIds.delete(chefId);
                    renderSelectedChefs();
                });
            });
        }
        
        // Search for main chef
        chefSearch.addEventListener('input', debounce(function() {
            const searchTerm = chefSearch.value.trim();
            
            console.log('Main chef search term:', searchTerm);
            
            if (searchTerm.length < 2) {
                searchResults.classList.add('hidden');
                return;
            }
            
            // Display loading indicator
            searchResults.innerHTML = '<div class="p-2 text-gray-500">Recherche en cours...</div>';
            searchResults.classList.remove('hidden');
            
            // Fetch chef de chantier results
            const url = `{{ route('search.chef-chantier') }}?search=${encodeURIComponent(searchTerm)}`;
            console.log('Fetching from URL:', url);
            
            fetch(url, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            })
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Search results:', data);
                    if (data.success && data.data && data.data.length > 0) {
                        // Display search results
                        searchResults.innerHTML = '';
                        searchResults.classList.remove('hidden');
                        
                        data.data.forEach(chef => {
                            // Store chef data in localStorage for later use
                            localStorage.setItem(`chef_${chef.id}`, JSON.stringify(chef));
                            
                            const resultItem = document.createElement('div');
                            resultItem.className = 'p-2 hover:bg-gray-100 cursor-pointer';
                            resultItem.innerHTML = `
                                <div class="font-medium">${chef.name}</div>
                                <div class="text-sm text-gray-600">${chef.email}</div>
                            `;
                            
                            // Add click handler to select a chef
                            resultItem.addEventListener('click', function() {
                                selectedChefId = chef.id;
                                renderSelectedChef();
                                searchResults.classList.add('hidden');
                                chefSearch.value = '';
                            });
                            
                            searchResults.appendChild(resultItem);
                        });
                    } else {
                        // No results
                        searchResults.innerHTML = '<div class="p-2 text-gray-500">Aucun chef de chantier trouvé</div>';
                        searchResults.classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error searching for chefs:', error);
                    searchResults.innerHTML = '<div class="p-2 text-red-500">Erreur lors de la recherche: ' + error.message + '</div>';
                    searchResults.classList.remove('hidden');
                });
        }, 300));
        
        // Search for additional chefs
        additionalChefSearch.addEventListener('input', debounce(function() {
            const searchTerm = additionalChefSearch.value.trim();
            
            console.log('Additional chef search term:', searchTerm);
            
            if (searchTerm.length < 2) {
                additionalSearchResults.classList.add('hidden');
                return;
            }
            
            // Display loading indicator
            additionalSearchResults.innerHTML = '<div class="p-2 text-gray-500">Recherche en cours...</div>';
            additionalSearchResults.classList.remove('hidden');
            
            // Fetch chef de chantier results
            const url = `{{ route('search.chef-chantier') }}?search=${encodeURIComponent(searchTerm)}`;
            console.log('Form fetching from URL:', url);
            
            fetch(url, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            })
                .then(response => {
                    console.log('Form response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Form search results:', data);
                    if (data.success && data.data && data.data.length > 0) {
                        // Display search results
                        additionalSearchResults.innerHTML = '';
                        additionalSearchResults.classList.remove('hidden');
                        
                        data.data.forEach(chef => {
                            // Store chef data in localStorage for later use
                            localStorage.setItem(`chef_${chef.id}`, JSON.stringify(chef));
                            
                            const resultItem = document.createElement('div');
                            resultItem.className = 'p-2 hover:bg-gray-100 cursor-pointer';
                            resultItem.innerHTML = `
                                <div class="font-medium">${chef.name}</div>
                                <div class="text-sm text-gray-600">${chef.email}</div>
                            `;
                            
                            // Add click handler to select a chef
                            resultItem.addEventListener('click', function() {
                                selectedChefIds.add(chef.id);
                                renderSelectedChefs();
                                additionalSearchResults.classList.add('hidden');
                                additionalChefSearch.value = '';
                            });
                            
                            additionalSearchResults.appendChild(resultItem);
                        });
                    } else {
                        // No results
                        additionalSearchResults.innerHTML = '<div class="p-2 text-gray-500">Aucun chef de chantier trouvé</div>';
                        additionalSearchResults.classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Form search error:', error);
                    additionalSearchResults.innerHTML = `<div class="p-2 text-red-500">Erreur: ${error.message}</div>`;
                    additionalSearchResults.classList.remove('hidden');
                });
        }, 300));
        
        // Hide search results when clicking outside
        document.addEventListener('click', function(event) {
            if (!chefSearch.contains(event.target) && !searchResults.contains(event.target)) {
                searchResults.classList.add('hidden');
            }
            
            if (!additionalChefSearch.contains(event.target) && !additionalSearchResults.contains(event.target)) {
                additionalSearchResults.classList.add('hidden');
            }
        });
        
        // Simple debounce function to prevent too many API calls
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
    });
</script>
@endsection 