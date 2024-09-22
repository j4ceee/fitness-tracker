@vite(['resources/js/day_stats.js'])

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">
            {{ $day->date->format('d.m.Y') . __(' bearbeiten') }}
        </h2>
    </x-slot>

    @include('components.status-msg')

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('days.partials.edit_day', ['day' => $day, 'user' => $user])
        </div>
    </div>
</x-app-layout>
