@extends('layouts.app')

@section('title', 'Create New User')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Create New User</h1>
    <p class="text-gray-600">Add a new user to the system</p>
</div>

@if ($errors->any())
<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
    <strong class="font-bold">There were some errors with your submission:</strong>
    <ul class="mt-2 list-disc list-inside">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="bg-white rounded-lg shadow-md p-6">
    <form action="{{ route('users.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="col-span-2 md:col-span-1">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-input w-full rounded-md shadow-sm" required>
            </div>
            
            <div class="col-span-2 md:col-span-1">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-input w-full rounded-md shadow-sm" required>
            </div>
            
            <div class="col-span-2 md:col-span-1">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" id="password" class="form-input w-full rounded-md shadow-sm" required>
            </div>
            
            <div class="col-span-2 md:col-span-1">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-input w-full rounded-md shadow-sm" required>
            </div>
            
            <div class="col-span-2 md:col-span-1">
                <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select name="role" id="role" class="form-select w-full rounded-md shadow-sm" required>
                    <option value="">Select a role</option>
                    @foreach($roles as $value => $label)
                        <option value="{{ $value }}" {{ old('role') == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-span-2 md:col-span-1">
                <label for="user_status_id" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="user_status_id" id="user_status_id" class="form-select w-full rounded-md shadow-sm" required>
                    <option value="">Select a status</option>
                    @foreach($statuses as $id => $name)
                        <option value="{{ $id }}" {{ old('user_status_id', 1) == $id ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-span-2 md:col-span-1">
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="form-input w-full rounded-md shadow-sm">
            </div>
            
            <div class="col-span-2">
                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                <textarea name="address" id="address" rows="3" class="form-textarea w-full rounded-md shadow-sm">{{ old('address') }}</textarea>
            </div>
        </div>
        
        <div class="mt-6 flex items-center justify-end">
            <a href="{{ route('users.index') }}" class="text-gray-600 hover:text-gray-800 mr-4">Cancel</a>
            <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                Create User
            </button>
        </div>
    </form>
</div>
@endsection 