<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Chef Search</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .search-results {
            max-height: 300px;
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
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-md mb-6">
        <h1 class="text-2xl font-bold mb-4">Test Chef de Chantier Search</h1>
        
        <div class="mb-4">
            <input type="text" id="search-input" 
                placeholder="Type to search for chef de chantier..." 
                class="w-full p-2 border rounded">
        </div>
        
        <div id="results-container" class="search-results mt-4 border rounded p-4 hidden"></div>
    </div>
    
    <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-bold mb-4">Project Form Test</h2>
        
        <form id="test-form" action="javascript:void(0);" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Project Name</label>
                <input type="text" class="w-full p-2 border rounded" value="Test Project">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Chef de Chantier</label>
                <div class="relative">
                    <input type="text" id="chef_search" 
                        placeholder="Search for chef de chantier..." 
                        class="w-full p-2 border rounded">
                    
                    <div id="chef_search_results" class="search-results absolute z-10 w-full bg-white mt-1 rounded-md shadow-lg hidden border"></div>
                </div>
                
                <div id="selected_chefs" class="selected-chefs mt-2"></div>
            </div>
            
            <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600">
                Submit Test
            </button>
        </form>
        
        <div id="form-result" class="mt-4 p-3 bg-gray-50 rounded hidden"></div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Top search functionality
            const searchInput = document.getElementById('search-input');
            const resultsContainer = document.getElementById('results-container');
            
            searchInput.addEventListener('input', debounce(function() {
                const searchTerm = searchInput.value.trim();
                
                console.log('Search term:', searchTerm);
                
                if (searchTerm.length < 2) {
                    resultsContainer.classList.add('hidden');
                    return;
                }
                
                // Show loading
                resultsContainer.innerHTML = '<div class="text-gray-500">Searching...</div>';
                resultsContainer.classList.remove('hidden');
                
                // Perform search
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
                    return response.json();
                })
                .then(data => {
                    console.log('Search results:', data);
                    
                    if (data.success && data.data && data.data.length > 0) {
                        // Display results
                        resultsContainer.innerHTML = `
                            <div class="mb-2 text-gray-500">Total chefs in system: ${data.total_chefs}</div>
                            <div class="mb-2 text-gray-500">Found ${data.data.length} results:</div>
                        `;
                        
                        data.data.forEach(chef => {
                            const resultItem = document.createElement('div');
                            resultItem.className = 'p-2 mb-2 bg-gray-50 rounded';
                            resultItem.innerHTML = `
                                <div class="font-semibold">${chef.name}</div>
                                <div class="text-sm text-gray-600">${chef.email}</div>
                                <div class="text-xs text-gray-500">ID: ${chef.id}</div>
                            `;
                            resultsContainer.appendChild(resultItem);
                        });
                    } else {
                        resultsContainer.innerHTML = '<div class="text-gray-500">No results found</div>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    resultsContainer.innerHTML = `<div class="text-red-500">Error: ${error.message}</div>`;
                });
            }, 300));
            
            // Project form functionality
            const chefSearch = document.getElementById('chef_search');
            const searchResults = document.getElementById('chef_search_results');
            const selectedChefs = document.getElementById('selected_chefs');
            const selectedChefIds = new Set();
            const testForm = document.getElementById('test-form');
            const formResult = document.getElementById('form-result');
            
            // Function to render selected chefs
            function renderSelectedChefs() {
                // Clear the container
                selectedChefs.innerHTML = '';
                
                // Add each selected chef as a badge
                selectedChefIds.forEach(chefId => {
                    const chefData = JSON.parse(localStorage.getItem(`chef_${chefId}`));
                    if (chefData) {
                        const badge = document.createElement('div');
                        badge.className = 'chef-badge';
                        badge.innerHTML = `
                            ${chefData.name}
                            <input type="hidden" name="chef_ids[]" value="${chefData.id}">
                            <button type="button" data-id="${chefData.id}" class="remove-chef">
                                <span>&times;</span>
                            </button>
                        `;
                        selectedChefs.appendChild(badge);
                    }
                });
                
                // Add event listeners to remove buttons
                document.querySelectorAll('.remove-chef').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const chefId = this.getAttribute('data-id');
                        selectedChefIds.delete(parseInt(chefId));
                        renderSelectedChefs();
                    });
                });
            }
            
            // Search for chefs as the user types
            chefSearch.addEventListener('input', debounce(function() {
                const searchTerm = chefSearch.value.trim();
                
                console.log('Form search term:', searchTerm);
                
                if (searchTerm.length < 2) {
                    searchResults.classList.add('hidden');
                    return;
                }
                
                // Display loading indicator
                searchResults.innerHTML = '<div class="p-2 text-gray-500">Searching...</div>';
                searchResults.classList.remove('hidden');
                
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
                                selectedChefIds.add(chef.id);
                                renderSelectedChefs();
                                searchResults.classList.add('hidden');
                                chefSearch.value = '';
                            });
                            
                            searchResults.appendChild(resultItem);
                        });
                    } else {
                        // No results
                        searchResults.innerHTML = '<div class="p-2 text-gray-500">No chef de chantier found</div>';
                        searchResults.classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Form search error:', error);
                    searchResults.innerHTML = `<div class="p-2 text-red-500">Error: ${error.message}</div>`;
                    searchResults.classList.remove('hidden');
                });
            }, 300));
            
            // Hide search results when clicking outside
            document.addEventListener('click', function(event) {
                if (!chefSearch.contains(event.target) && !searchResults.contains(event.target)) {
                    searchResults.classList.add('hidden');
                }
            });
            
            // Form submission
            testForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const chefIds = Array.from(selectedChefIds);
                
                formResult.innerHTML = `
                    <div class="font-medium mb-2">Form Data:</div>
                    <div class="text-sm">Selected Chef IDs: ${chefIds.join(', ') || 'None'}</div>
                `;
                formResult.classList.remove('hidden');
            });
            
            // Simple debounce function
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
</body>
</html> 