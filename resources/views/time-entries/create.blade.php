@extends('layouts.app')

@section('title', 'Nouveau Pointage')

@section('content')
    <div class="container mx-auto px-4">
        <!-- Page Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Nouveau Pointage</h1>
                <p class="text-gray-600">Enregistrer un nouveau pointage</p>
            </div>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('time-entries.store') }}" method="POST" class="bg-white shadow rounded-lg p-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- User Selection -->
                <div class="col-span-1">
                    <label for="user_id" class="block text-sm font-medium text-gray-700">Utilisateur</label>
                    <select name="user_id[]" multiple id="user_id"
                        class="mt-1 block w-full rounded-md border-gray-300 border py-1.5 px-2 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                        required>
                        <option value="">Sélectionner un utilisateur</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}"
                                {{ is_array(old('user_id')) && in_array($user->id, old('user_id')) ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    <div class="mt-2">
                        <button type="button" id="load_users_btn"
                            class="px-2 py-1 bg-gray-100 text-gray-700 rounded-md text-sm">
                            Charger tous les utilisateurs
                        </button>
                    </div>
                    @error('user_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Project Selection -->
                <div class="col-span-1">
                    <label for="project_id" class="block text-sm font-medium text-gray-700">Projet</label>
                    <select name="project_id" id="project_id"
                        class="mt-1 block w-full rounded-md border-gray-300 border py-1.5 px-2 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                        required>
                        <option value="">Sélectionner un projet</option>
                        @foreach ($projects as $project)
                            <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                {{ $project->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('project_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date -->
                <div class="col-span-1">
                    <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
                    <input type="date" name="date" id="date" value="{{ old('date', date('Y-m-d')) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 border py-1.5 px-2 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                        required>
                    @error('date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Check-in Time -->
                <div class="col-span-1">
                    <label for="check_in" class="block text-sm font-medium text-gray-700">Heure d'entrée</label>
                    <input type="time" name="check_in" id="check_in" value="{{ old('check_in') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 border py-1.5 px-2 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                        required>
                    @error('check_in')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Check-out Time -->
                <div class="col-span-1">
                    <label for="check_out" class="block text-sm font-medium text-gray-700">Heure de sortie</label>
                    <input type="time" name="check_out" id="check_out" value="{{ old('check_out') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 border py-1.5 px-2 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                        required>
                    @error('check_out')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Calculated Hours -->
                <div class="col-span-1">
                    <label class="block text-sm font-medium text-gray-700">Heures travaillées</label>
                    <div class="mt-1 p-2 bg-gray-50 rounded-md">
                        <span id="total_hours">0</span> heures
                    </div>
                </div>

                <!-- Overtime Hours -->
                <div class="col-span-1">
                    <label class="block text-sm font-medium text-gray-700">Heures supplémentaires</label>
                    <div class="mt-1 p-2 bg-yellow-50 rounded-md">
                        <span id="overtime_hours">0</span> heures
                    </div>
                </div>

                <!-- Notes -->
                <div class="col-span-1 md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea name="notes" id="notes" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 border py-1.5 px-2 shadow-sm focus:border-primary-500 focus:ring-primary-500">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div class="col-span-1">
                    <label for="status" class="block text-sm font-medium text-gray-700">Statut</label>
                    <select name="status" id="status"
                        class="mt-1 block w-full rounded-md border-gray-300 border py-1.5 px-2 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                        required>
                        <option value="present" {{ old('status') == 'present' ? 'selected' : '' }}>Présent</option>
                        <option value="absent" {{ old('status') == 'absent' ? 'selected' : '' }}>Absent</option>
                        <option value="late" {{ old('status') == 'late' ? 'selected' : '' }}>En retard</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- jour_type -->
                <div class="col-span-1">
                    <label for="jour_type" class="block text-sm font-medium text-gray-700">Jour Type</label>
                    <select name="jour_type" id="jour_type"
                        class="mt-1 block w-full rounded-md border-gray-300 border py-1.5 px-2 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                        required>
                        <option value="normal" {{ old('jour_type') == 'normal' ? 'selected' : '' }}>normal</option>
                        <option value="férié" {{ old('jour_type') == 'férié' ? 'selected' : '' }}>férié</option>
                    </select>
                    @error('jour_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('time-entries.index') }}"
                    class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Annuler
                </a>
                <button type="submit"
                    class="px-4 py-2 bg-primary-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Enregistrer
                </button>
            </div>
        </form>

        <!-- Debugging Section (Only visible in non-production environments) -->
        @if (app()->environment() !== 'production')
            <div class="mt-6 bg-white shadow rounded-lg p-6 border-l-4 border-yellow-500">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Outils de dépannage</h3>
                <p class="text-sm text-gray-600 mb-4">Cette section est uniquement visible dans les environnements de
                    développement.</p>

                <div class="mb-4">
                    <h4 class="text-md font-medium text-gray-800 mb-2">Informations générales</h4>
                    <ul class="list-disc list-inside text-sm">
                        <li>Utilisateurs disponibles: {{ count($users) }}</li>
                        <li>Projets disponibles: {{ count($projects) }}</li>
                        <li>URL de recherche: <a href="{{ route('users.search') }}?search=test" target="_blank"
                                class="text-blue-600 hover:underline">{{ route('users.search') }}?search=test</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-md font-medium text-gray-800 mb-2">Aide sur la sélection utilisateur</h4>
                    <p class="text-sm text-gray-600 mb-2">Si le sélecteur d'utilisateur ne fonctionne pas correctement,
                        vous pouvez essayer les actions suivantes:</p>
                    <div class="flex space-x-2">
                        <button type="button" id="debug_select_first"
                            class="px-3 py-1 bg-gray-200 text-gray-700 rounded-md text-sm">
                            Sélectionner premier utilisateur
                        </button>
                        <button type="button" id="test_search_url"
                            class="px-3 py-1 bg-gray-200 text-gray-700 rounded-md text-sm">
                            Tester URL recherche
                        </button>
                        <button type="button" id="check_jquery"
                            class="px-3 py-1 bg-gray-200 text-gray-700 rounded-md text-sm">
                            Vérifier jQuery
                        </button>
                    </div>
                    <div id="debug_output" class="mt-2 p-2 bg-gray-100 text-xs font-mono rounded hidden"></div>
                </div>
            </div>
        @endif
    </div>

    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @endpush

    @push('scripts')
        <!-- Only include Select2 JS, jQuery is already in layout -->
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            console.log('Time entries create script loaded');
            $(document).ready(function() {
                console.log('Document ready');

                // Initialize with select2 disabled initially
                let select2Initialized = false;

                // Handle the load users button
                $('#load_users_btn').on('click', function() {
                    console.log('Load users button clicked');

                    // If select2 is initialized, destroy it first
                    if (select2Initialized) {
                        try {
                            $('#user_id').select2('destroy');
                            select2Initialized = false;
                        } catch (error) {
                            console.error('Error destroying select2:', error);
                        }
                    }

                    // Show all the existing options
                    $('#user_id option').show();

                    // Add a message
                    $(this).text('Utilisateurs chargés');
                    setTimeout(() => {
                        $(this).text('Charger tous les utilisateurs');
                    }, 2000);
                });

                // Initialize Select2 for user selection
                try {
                    console.log('Initializing user select2');

                    // Function to init select2
                    function initSelect2() {
                        if (select2Initialized) return;

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
                                url: '{{ route('users.search') }}',
                                dataType: 'json',
                                delay: 250,
                                data: function(params) {
                                    console.log('Search term:', params.term);
                                    return {
                                        search: params.term,
                                        page: params.page || 1
                                    };
                                },
                                processResults: function(data) {
                                    console.log('Received data:', data);
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
                                    console.error('Error searching for users:', textStatus, errorThrown);
                                },
                                cache: true
                            }
                        });

                        select2Initialized = true;
                        console.log('User select2 initialized');
                    }

                    // Initialize select2 on focus
                    $('#user_id').on('focus', function() {
                        if (!select2Initialized) {
                            initSelect2();
                        }
                    });

                    // Only initialize select2 if there are fewer than 10 options
                    if ($('#user_id option').length < 10) {
                        initSelect2();
                    }

                } catch (error) {
                    console.error('Error initializing user select2:', error);
                }

                // Initialize Select2 for project selection
                try {
                    console.log('Initializing project select2');
                    $('#project_id').select2({
                        placeholder: 'Sélectionner un projet...'
                    });
                    console.log('Project select2 initialized');
                } catch (error) {
                    console.error('Error initializing project select2:', error);
                }

                // Function to calculate hours
                function calculateHours() {
                    const checkIn = $('#check_in').val();
                    const checkOut = $('#check_out').val();

                    if (checkIn && checkOut) {
                        const start = new Date('2000-01-01T' + checkIn);
                        const end = new Date('2000-01-01T' + checkOut);

                        // Calculate total hours
                        let totalHours = (end - start) / (1000 * 60 * 60);
                        if (totalHours < 0) {
                            totalHours += 24; // Handle overnight shifts
                        }

                        // Calculate overtime hours
                        const overtimeHours = Math.max(0, totalHours - 8);

                        // Update displays
                        $('#total_hours').text(totalHours.toFixed(2));
                        $('#overtime_hours').text(overtimeHours.toFixed(2));

                        // Add overtime hours to form data
                        $('<input>').attr({
                            type: 'hidden',
                            name: 'overtime_hours',
                            value: overtimeHours.toFixed(2)
                        }).appendTo('form');
                    }
                }

                // Calculate hours when check-in or check-out changes
                $('#check_in, #check_out').on('change', calculateHours);

                // Initial calculation if values exist
                calculateHours();

                // Debug buttons
                $('#debug_select_first').on('click', function() {
                    const firstOption = $('#user_id option:eq(1)');
                    if (firstOption.length) {
                        const value = firstOption.val();
                        $('#user_id').val(value).trigger('change');
                        showDebug(`Utilisateur sélectionné: ${firstOption.text()} (ID: ${value})`);
                    } else {
                        showDebug('Aucun utilisateur disponible');
                    }
                });

                $('#test_search_url').on('click', function() {
                    showDebug('Test de l\'URL de recherche en cours...');

                    fetch('{{ route('users.search') }}?search=test', {
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
                            showDebug(`Statut réponse: ${response.status}`);
                            return response.json();
                        })
                        .then(data => {
                            showDebug(`Données reçues: ${JSON.stringify(data).substring(0, 150)}...`);
                        })
                        .catch(error => {
                            showDebug(`Erreur: ${error.message}`);
                        });
                });

                $('#check_jquery').on('click', function() {
                    showDebug(`Version jQuery: ${$.fn.jquery}`);
                    showDebug(`Select2 défini: ${typeof $.fn.select2 === 'function'}`);
                    showDebug(`Options dans select: ${$('#user_id option').length}`);
                });

                function showDebug(message) {
                    const debugOutput = $('#debug_output');
                    const timestamp = new Date().toISOString().substr(11, 8);
                    debugOutput.removeClass('hidden').prepend(`<div>[${timestamp}] ${message}</div>`);
                }
            });
        </script>
    @endpush
@endsection
