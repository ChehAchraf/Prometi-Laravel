    <!-- Users -->
    @if(auth()->user()->isAdmin())
    <x-nav-link href="{{ route('users.index') }}" :active="request()->routeIs('users.*')">
        <i class="fas fa-users mr-2"></i> {{ __('Users') }}
    </x-nav-link>

    <!-- User Statuses -->
    <x-nav-link href="{{ route('user-statuses.index') }}" :active="request()->routeIs('user-statuses.*')">
        <i class="fas fa-user-clock mr-2"></i> {{ __('User Statuses') }}
    </x-nav-link>
    @endif 