@extends('layouts.app')

@section('title', 'Edit User Status')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Edit Status</h1>
        <p class="text-gray-600">Update user status information</p>
    </div>

    @if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <strong class="font-bold">There were errors with your submission!</strong>
        <ul class="mt-2 list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('user-statuses.update', $userStatus) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Status Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $userStatus->name) }}" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" required>
                </div>

                <!-- Color -->
                <div>
                    <label for="color" class="block text-sm font-medium text-gray-700">Color</label>
                    <div class="mt-1 flex items-center space-x-3">
                        <input type="color" name="color" id="color" value="{{ old('color', $userStatus->color) }}" 
                            class="h-10 w-10 rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        <input type="text" id="colorHex" value="{{ old('color', $userStatus->color) }}" 
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            readonly>
                    </div>
                </div>

                <!-- Icon -->
                <div>
                    <label for="icon" class="block text-sm font-medium text-gray-700">Icon (Font Awesome class)</label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500">
                            <i class="fas fa-icons"></i>
                        </span>
                        <input type="text" name="icon" id="icon" value="{{ old('icon', $userStatus->icon) }}" 
                            class="block w-full rounded-none rounded-r-md border-gray-300 focus:border-primary-500 focus:ring-primary-500" 
                            placeholder="fas fa-check-circle">
                    </div>
                    <p class="mt-1 text-sm text-gray-500">
                        Example: fas fa-check-circle, fas fa-user, etc. 
                        <a href="https://fontawesome.com/icons" target="_blank" class="text-primary-600 hover:text-primary-500">
                            Browse icons
                        </a>
                    </p>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="3" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">{{ old('description', $userStatus->description) }}</textarea>
                </div>

                <!-- Is Active -->
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $userStatus->is_active) ? 'checked' : '' }}
                            class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-gray-300 rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="is_active" class="font-medium text-gray-700">Active</label>
                        <p class="text-gray-500">Only active statuses will be available for selection when creating or editing users.</p>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('user-statuses.index') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    Cancel
                </a>
                <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    Update Status
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const colorInput = document.getElementById('color');
        const colorHexInput = document.getElementById('colorHex');
        
        // Update the text field when the color picker changes
        colorInput.addEventListener('input', function() {
            colorHexInput.value = this.value;
        });
        
        // Ensure the color picker's value is set to the text field on load
        colorHexInput.value = colorInput.value;
    });
</script>
@endpush
@endsection 