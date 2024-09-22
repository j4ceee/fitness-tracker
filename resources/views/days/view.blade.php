@vite(['resources/js/day_stats.js'])

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">
            {{ __('Details vom ') . $day->date->format('d.m.Y') . __(' von ') . $user->name }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('days.partials.view_day', ['day' => $day, 'user' => $user])
        </div>
    </div>
</x-app-layout>
