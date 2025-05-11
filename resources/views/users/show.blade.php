@extends('layouts.app')

@section('title', 'User Details')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">User Details</h1>
            <p class="text-gray-600">Viewing user information</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('users.edit', $user) }}" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                Edit
            </a>
            <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600">
                    Delete
                </button>
            </form>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Basic Information</h3>
                <div class="space-y-3">
                    <div>
                        <span class="text-sm text-gray-500">Name:</span>
                        <p class="font-medium">{{ $user->name }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Email:</span>
                        <p class="font-medium">{{ $user->email }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Role:</span>
                        <p class="font-medium">
                            @if($user->role)
                                <span class="px-2 py-1 rounded-full text-xs 
                                    @if($user->role->role === 'superadmin')
                                        bg-red-100 text-red-800
                                    @elseif($user->role->role === 'hr_editor')
                                        bg-blue-100 text-blue-800
                                    @elseif($user->role->role === 'pointage_editor')
                                        bg-green-100 text-green-800
                                    @elseif($user->role->role === 'project_viewer')
                                        bg-yellow-100 text-yellow-800
                                    @elseif($user->role->role === 'technical_director')
                                        bg-purple-100 text-purple-800
                                    @else
                                        bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $user->role->getDisplayName() }}
                                </span>
                            @else
                                <span class="text-gray-400">No role assigned</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Status:</span>
                        <p class="font-medium">
                            @if($user->status)
                                <span class="px-2 py-1 rounded-full text-xs inline-flex items-center"
                                      style="background-color: {{ $user->status->color }}20; color: {{ $user->status->color }}">
                                    @if($user->status->icon)
                                        <i class="{{ $user->status->icon }} mr-1"></i>
                                    @endif
                                    {{ $user->status->name }}
                                </span>
                            @else
                                <span class="text-gray-400">No status assigned</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            
            <div>
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Contact Information</h3>
                <div class="space-y-3">
                    <div>
                        <span class="text-sm text-gray-500">Phone:</span>
                        <p class="font-medium">{{ $user->phone ?? 'Not provided' }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Address:</span>
                        <p class="font-medium">{{ $user->address ?? 'Not provided' }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-8 border-t pt-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">System Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <span class="text-sm text-gray-500">Created at:</span>
                    <p class="font-medium">{{ $user->created_at->format('M d, Y H:i') }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Last updated:</span>
                    <p class="font-medium">{{ $user->updated_at->format('M d, Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-6">
    <a href="{{ route('users.index') }}" class="text-primary-600 hover:text-primary-700">
        &larr; Back to Users List
    </a>
</div>
@endsection 