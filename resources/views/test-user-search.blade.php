@extends('layouts.app')

@section('title', 'Test User Search')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-4">Test User Search</h1>
    
    <div class="mb-6 bg-white p-6 rounded-lg shadow">
        <h2 class="text-lg font-semibold mb-4">Select2 User Search</h2>
        <div class="mb-4">
            <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">Utilisateur</label>
            <select id="user_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                <option value="">Sélectionner un utilisateur</option>
            </select>
        </div>
        
        <div class="mt-4">
            <p>Selected user: <span id="selected-user-info">None</span></p>
        </div>
    </div>
    
    <div class="mb-6 bg-white p-6 rounded-lg shadow">
        <h2 class="text-lg font-semibold mb-4">Fetch API User Search</h2>
        <div class="mb-4">
            <label for="search_input" class="block text-sm font-medium text-gray-700 mb-2">Rechercher un utilisateur</label>
            <input type="text" id="search_input" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" placeholder="Entrez au moins 2 caractères...">
        </div>
        
        <div id="search_results" class="mt-4 hidden">
            <h3 class="text-md font-medium mb-2">Résultats:</h3>
            <div id="results_container" class="border rounded-md p-4 bg-gray-50">
                <!-- Results will be displayed here -->
            </div>
        </div>
    </div>
    
    <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
        <h3 class="text-lg font-medium mb-2">Debug Information</h3>
        <pre id="debug_info" class="text-sm bg-white p-3 rounded border overflow-auto max-h-60"></pre>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #4f46e5;
    }
</style>
@endpush

@push('scripts')
<!-- Only include Select2 JS, jQuery is already in layout -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    function appendDebug(message) {
        const debugEl = document.getElementById('debug_info');
        const timestamp = new Date().toISOString().substr(11, 8);
        debugEl.textContent += `[${timestamp}] ${message}\n`;
        console.log(message);
    }
    
    $(document).ready(function() {
        appendDebug("Document ready");
        
        // Test Select2
        try {
            appendDebug("Initializing Select2");
            
            $('#user_id').select2({
                placeholder: 'Rechercher un utilisateur...',
                minimumInputLength: 2,
                language: {
                    errorLoading: function() {
                        return 'Erreur de chargement des résultats';
                    },
                    searching: function() {
                        return 'Recherche en cours...';
                    },
                    noResults: function() {
                        return 'Aucun résultat trouvé';
                    },
                    inputTooShort: function(args) {
                        return `Entrez au moins ${args.minimum} caractères`;
                    }
                },
                ajax: {
                    url: '{{ route("users.search") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        appendDebug(`Select2 search term: ${params.term}`);
                        return {
                            search: params.term,
                            page: params.page || 1
                        };
                    },
                    processResults: function(data) {
                        appendDebug(`Select2 received data: ${JSON.stringify(data).substring(0, 100)}...`);
                        return {
                            results: data.data.map(function(user) {
                                return {
                                    id: user.id,
                                    text: user.name + ' (' + user.email + ')'
                                };
                            }),
                            pagination: {
                                more: data.next_page_url ? true : false
                            }
                        };
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        appendDebug(`Select2 error: ${textStatus}, ${errorThrown}`);
                        console.error('Error searching for users:', textStatus, errorThrown);
                    },
                    cache: true
                }
            }).on('select2:select', function(e) {
                const data = e.params.data;
                appendDebug(`Selected user: ID=${data.id}, Text=${data.text}`);
                $('#selected-user-info').text(`ID: ${data.id}, Name: ${data.text}`);
            });
            
            appendDebug("Select2 initialized successfully");
        } catch (error) {
            appendDebug(`Select2 initialization error: ${error.message}`);
        }
        
        // Test Fetch API
        const searchInput = document.getElementById('search_input');
        const searchResults = document.getElementById('search_results');
        const resultsContainer = document.getElementById('results_container');
        
        searchInput.addEventListener('input', debounce(function() {
            const searchTerm = searchInput.value.trim();
            appendDebug(`Fetch API search term: ${searchTerm}`);
            
            if (searchTerm.length < 2) {
                searchResults.classList.add('hidden');
                return;
            }
            
            resultsContainer.innerHTML = '<div class="p-2 text-gray-500">Recherche en cours...</div>';
            searchResults.classList.remove('hidden');
            
            const url = `{{ route('users.search') }}?search=${encodeURIComponent(searchTerm)}`;
            appendDebug(`Fetching from URL: ${url}`);
            
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
                appendDebug(`Response status: ${response.status}`);
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                appendDebug(`Received data: ${JSON.stringify(data).substring(0, 100)}...`);
                
                if (data.data && data.data.length > 0) {
                    resultsContainer.innerHTML = '';
                    
                    data.data.forEach(user => {
                        const resultItem = document.createElement('div');
                        resultItem.className = 'p-2 hover:bg-gray-100 cursor-pointer mb-2 border rounded';
                        resultItem.innerHTML = `
                            <div class="font-medium">${user.name}</div>
                            <div class="text-sm text-gray-600">${user.email}</div>
                        `;
                        
                        resultItem.addEventListener('click', function() {
                            appendDebug(`Clicked on user: ID=${user.id}, Name=${user.name}`);
                            $('#selected-user-info').text(`ID: ${user.id}, Name: ${user.name}`);
                        });
                        
                        resultsContainer.appendChild(resultItem);
                    });
                } else {
                    resultsContainer.innerHTML = '<div class="p-2 text-gray-500">Aucun utilisateur trouvé</div>';
                }
            })
            .catch(error => {
                appendDebug(`Fetch error: ${error.message}`);
                resultsContainer.innerHTML = `<div class="p-2 text-red-500">Erreur: ${error.message}</div>`;
            });
        }, 300));
        
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
@endpush 