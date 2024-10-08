@php use Carbon\Carbon; @endphp
@vite(['resources/js/day_stats.js'])

@section('title')
    {{ __('Dashboard') }}
@endsection

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="light_bg overflow-hidden shadow-sm rounded-lg p-4 mb-6">
                <p class="text-lg">{{ __("Hallo,") }}</p>
                <p class="ml-6 font-bold text-xl inline-block">{{ Auth::user()->name }}</p>
                <p class="inline-block text-xl">!</p>
            </div>

            <div class="light_bg overflow-hidden shadow-sm rounded-lg p-4 mb-6">
                <div class="flex justify-evenly gap-4 flex-wrap">
                    <p>{{ __('Gesamt Punkte: ') }}
                        @if (Auth::user()->user_stats->points_total >= 1)
                            <strong class="text-green-300 font-bold">+{{ Auth::user()->user_stats->points_total }}</strong>
                        @else
                            <strong class="text-red-500 font-bold">{{ Auth::user()->user_stats->points_total }}</strong>
                        @endif
                    </p>

                    <p>{{ __('Monatl. Punkte: ') }}
                        @if (($userMonthly->points_month ?? null) && $userMonthly->points_month >= 1)
                            <strong class="text-green-300 font-bold">+{{ $userMonthly->points_month }}</strong>
                        @else
                            <strong class="text-red-500 font-bold">{{ $userMonthly->points_month ?? 0 }}</strong>
                        @endif
                    </p>

                    <p>{{ __('Monatl. Cheat Days: ') }}
                        <strong class="text-blue-300 font-bold">{{ $userMonthly->cheat_days_used ?? 0 }}</strong>
                        / {{ Config::get('constants.max_cheat_days') }}
                    </p>
                </div>
            </div>

            <div class="flex justify-center gap-4 mb-8 flex-wrap">
                <x-blue-button-link :href="route('dashboard', ['day' => $dayInt + 1])"
                                    title="{{ __('Vorheriger Tag') }}">
                    <span class="month_control_icon order-first" aria-hidden="true"><</span>
                </x-blue-button-link>
                <h2 class="text-2xl font-bold order-3 sm:order-2">
                    <span class="hidden sm:inline">{{ Carbon::parse($date)->translatedFormat('l, d.m.Y') }}</span>
                    <span class="inline sm:hidden">{{ Carbon::parse($date)->translatedFormat('d.m.Y') }}</span>
                </h2>
                @if ($dayInt == 0)
                    <x-blue-button-link :href="route('dashboard', ['day' => $dayInt - 1])"
                                        title="{{ __('Nächster Tag') }}" class="invisible order-3" aria-hidden="true">
                        <span class="month_control_icon" aria-hidden="true">></span>
                    </x-blue-button-link>
                @else
                    <x-blue-button-link :href="route('dashboard', ['day' => $dayInt - 1])"
                                        title="{{ __('Nächster Tag') }}" class="order-2 sm:order-3">
                        <span class="month_control_icon" aria-hidden="true">></span>
                    </x-blue-button-link>
                @endif
            </div>

            @include('days.partials.edit_day', ['day' => $day, 'user' => Auth::user()])
        </div>
    </div>
</x-app-layout>
