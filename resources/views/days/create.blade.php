@vite(['resources/js/day_stats.js'])

@section('title')
    {{ __('Neuen Tag erstellen') }}
@endsection

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">
            {{ __('Neuen Tag erstellen') }}
        </h2>
    </x-slot>

    @include('components.status-msg')

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('days.partials.edit_day', ['user' => $user])
        </div>
    </div>
</x-app-layout>
