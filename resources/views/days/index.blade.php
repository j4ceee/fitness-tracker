@vite(['resources/js/day_stats.js'])

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">
            @if ($user == Auth::user())
                {{ __('Deine getrackten Tage') }}
            @else
                {{ __('Getrackte Tage von ') }} {{ $user->name }}
            @endif
        </h2>
    </x-slot>

    @include('components.status-msg')

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($page === 0)
                @include('days.partials.track_today')
            @endif
        </div>
    </div>
</x-app-layout>
