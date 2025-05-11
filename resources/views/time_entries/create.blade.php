@extends('layouts.app')

@section('title', 'New Time Entry')

@section('content')
<div class="container mx-auto px-4">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">New Time Entry</h1>
            <p class="text-gray-600">Record a new time entry</p>
        </div>
    </div>

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <ul>
                @foreach($errors->all() as $error)
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
                <label for="user_id" class="block text-sm font-medium text-gray-700">User</label>
                <select name="user_id" id="user_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" required>
                    <option value="">Select a user</option>
                </select>
                @error('user_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Project Selection -->
            <div class="col-span-1">
                <label for="project_id" class="block text-sm font-medium text-gray-700">Project</label>
                <select name="project_id" id="project_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" required>
                    <option value="">Select a project</option>
                    @foreach($projects as $project)
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
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" required>
                @error('date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Check-in Time -->
            <div class="col-span-1">
                <label for="check_in" class="block text-sm font-medium text-gray-700">Check-in Time</label>
                <input type="time" name="check_in" id="check_in" value="{{ old('check_in') }}" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" required>
                @error('check_in')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Check-out Time -->
            <div class="col-span-1">
                <label for="check_out" class="block text-sm font-medium text-gray-700">Check-out Time</label>
                <input type="time" name="check_out" id="check_out" value="{{ old('check_out') }}" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" required>
                @error('check_out')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Calculated Hours -->
            <div class="col-span-1">
                <label class="block text-sm font-medium text-gray-700">Hours Worked</label>
                <div class="mt-1 p-2 bg-gray-50 rounded-md">
                    <span id="total_hours">0</span> hours
                </div>
            </div>

            <!-- Overtime Hours -->
            <div class="col-span-1">
                <label class="block text-sm font-medium text-gray-700">Overtime Hours</label>
                <div class="mt-1 p-2 bg-yellow-50 rounded-md">
                    <span id="overtime_hours">0</span> hours
                </div>
            </div>

            <!-- Notes -->
            <div class="col-span-1 md:col-span-2">
                <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                <textarea name="notes" id="notes" rows="3" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div class="col-span-1">
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" required>
                    <option value="present" {{ old('status') == 'present' ? 'selected' : '' }}>Present</option>
                    <option value="absent" {{ old('status') == 'absent' ? 'selected' : '' }}>Absent</option>
                    <option value="late" {{ old('status') == 'late' ? 'selected' : '' }}>Late</option>
                    <option value="early_leave" {{ old('status') == 'early_leave' ? 'selected' : '' }}>Early Leave</option>
                </select>
                @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('time-entries.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-primary-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                Save
            </button>
        </div>
    </form>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2 for user selection
        $('#user_id').select2({
            placeholder: 'Search for a user...',
            minimumInputLength: 2,
            language: {
                errorLoading: function() {
                    return 'Error loading results';
                },
                searching: function() {
                    return 'Searching...';
                },
                noResults: function() {
                    return 'No results found';
                }
            },
            ajax: {
                url: '{{ route("users.search") }}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function(data) {
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
                error: function() {
                    console.error('Error searching for users');
                },
                cache: true
            }
        });

        // Initialize Select2 for project selection
        $('#project_id').select2({
            placeholder: 'Select a project...'
        });

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
    });
</script>
@endsection
@endsection 