<x-app-layout>
    <x-slot name="header">
        <h1 class="font-semibold text-xl text-gray-100 leading-tight">
            @if ($user == Auth::user())
                {{ __('Deine getrackten Tage') }}
            @else
                {{ __('Getrackte Tage von ') }} {{ $user->name }}
            @endif
        </h1>
    </x-slot>

    @include('components.status-msg')

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="flex justify-end mb-6">
                <x-blue-button-link @class(["text_icon_button"]) href="{{ route('days.create') }}">
                    <p>{{ __('Tag anlegen') }}</p><img src="{{ route('image.show', 'noun-plus-6413839.svg') }}" alt="">
                </x-blue-button-link>
            </div>

            <div class="mt-12">
                <div class="flex justify-center gap-4 mb-8">
                    <x-blue-button-link :href="route('days.my', ['page' => $page + 1])" title="{{ __('Vorheriger Monat') }}">
                        <span class="month_control_icon" aria-hidden="true"><</span>
                    </x-blue-button-link>
                    <h2 class="text-2xl font-bold">
                        {{ now()->subMonths($page)->translatedFormat('F Y') }}
                    </h2>
                    @if ($page > 0)
                        <x-blue-button-link :href="route('days.my', ['page' => $page - 1])" title="{{ __('Nächster Monat') }}">
                            <span class="month_control_icon" aria-hidden="true">></span>
                        </x-blue-button-link>
                    @endif
                </div>

                <div class="mb-16 month_statistics mx-auto">
                    <div class="flex justify-center gap-4 flex-wrap">
                        <p>{{ __('Gesammelte Punkte: ') }}
                            @if ($userMonthly->points_month ?? null && $userMonthly->points_month >= 1)
                                <strong class="text-green-300 font-bold">+{{ $userMonthly->points_month }}</strong>
                            @else
                                <strong class="text-red-500 font-bold">{{ $userMonthly->points_month ?? 0 }}</strong>
                            @endif
                        </p>
                        <p>{{ __('Verwendete Cheat Days: ') }}
                            <strong class="text-orange-400 font-bold">{{ $userMonthly->cheat_days_used ?? 0 }}</strong>
                            / {{ Config::get('constants.max_cheat_days') }}
                        </p>
                    </div>
                </div>

                <div class="day_list_content">
                    @foreach ($days as $day)
                        <div class="day">
                            @if ($day->is_cheat_day)
                                <abbr class="cheat_day_icon" title="Cheat Day" aria-label="Cheat Day">+</abbr>
                            @endif

                            <h3>{{ $day->date->translatedFormat('l, j. M Y') }}</h3>
                            <details>
                                <summary>
                                    @if ($day->points >= 1)
                                        <span class="day_details leading-8 text-xl font-bold text-green-300">+{{ $day->points }}</span>
                                    @else
                                        <span class="day_details leading-8 text-xl font-bold text-red-500">{{ $day->points }}</span>
                                    @endif
                                </summary>
                                <div class="flex w-full justify-evenly">

                                    <div class="w-2/5 flex justify-center">
                                        <div>
                                            <p class="leading-3">{{ __('Punkte:') }}</p>
                                            @if ($day->points >= 1)
                                                <p class="leading-8 text-xl font-bold text-green-300">+{{ $day->points }}</p>
                                            @else
                                                <p class="leading-8 text-xl font-bold text-red-500">{{ $day->points }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex justify-center flex-grow">
                                        <div class="flex flex-col w-half gap-2 items-center">
                                            <progress class="day_cal_progress" max="1" value="{{ $day->percentage_of_goal }}"></progress>
                                            <p class="inline-block">
                                                <span class="font-bold">{{ $day->calories }}</span> / {{ $day->day_calorie_goal }} kcal
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex w-full justify-evenly">

                                    <div class="w-2/5 flex justify-center">
                                        <p class="text-xl font-bold">
                                            <span id="water_count">{{ number_format($day->water, 2) }}</span>
                                            {{__('L')}}
                                        </p>
                                    </div>

                                    <div class="flex justify-center gap-2 flex-grow">
                                        @if (request()->routeIs('days.my') || Auth::user() == $user)
                                            <x-secondary-button-link @class(["admin-users-action"]) href="{{ route('days.edit', $day->id) }}">
                                                <img class="admin-users-icons" src="{{ route('image.show', 'noun-edit-1047822.svg') }}" title="{{ $day->date->format('d.m.Y') }} bearbeiten" alt="{{ $day->date->format('d.m.Y') }} bearbeiten">
                                            </x-secondary-button-link>

                                            <x-danger-button @class(["admin-users-danger"]) x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion-{{ $day->id }}')">
                                                <img class="admin-users-icons" src="{{ route('image.show', 'noun-trash-2025467.svg') }}" title="{{ $day->date->format('d.m.Y') }} löschen" alt="{{ $day->date->format('d.m.Y') }} löschen">
                                            </x-danger-button>

                                            <x-modal name="confirm-user-deletion-{{ $day->id }}" :show="$errors->userDeletion->isNotEmpty()" focusable>
                                                <form method="post" class="p-6" action="{{ route('days.destroy', $day->id) }}">
                                                    @csrf
                                                    @method('delete')

                                                    <h2 class="text-lg font-medium text-white">
                                                        {{ __('Möchtest du den Tag vom ' . $day->date->format('d.m.Y') . ' wirklich löschen?') }}
                                                    </h2>

                                                    <p class="mt-1 text-sm text-gray-400">
                                                        {{ __('Dies wird den Tag und alle dessen eingetragenen Daten löschen:') }}
                                                    </p>

                                                    <ul class="mt-1 text-sm text-gray-200">
                                                        <li class="mt-1">{{ __('Datum: ') . $day->date->format('d.m.Y') }}</li>
                                                        <li class="mt-1">{{ __('Punkte: ') . $day->points }}</li>
                                                        <li class="mt-1">{{ __('Kalorien: ') . $day->calories }}</li>
                                                        <li class="mt-1">{{ __('Wasser: ') . $day->water . ' L' }}</li>
                                                        <li class="mt-1">{{ __('Aktivitäten: ') . $day->steps . ' km' }}</li>
                                                        <li class="mt-1">{{ __('...') }}</li>
                                                    </ul>

                                                    <div class="mt-6 flex justify-end">
                                                        <x-secondary-button x-on:click="$dispatch('close')">
                                                            {{ __('Abbrechen') }}
                                                        </x-secondary-button>

                                                        <x-danger-button class="ms-3">
                                                            {{ __('Tag löschen') }}
                                                        </x-danger-button>
                                                    </div>
                                                </form>
                                            </x-modal>
                                        @else
                                            <x-secondary-button-link @class(["admin-users-action"]) href="{{ route('days.day', ['userId' => $user->id, 'date' => $day->date->format('Y-m-d')]) }}">
                                                <img class="admin-users-icons" src="{{ route('image.show', 'noun-calendar-5490924.svg') }}" title="{{ $day->date->format('d.m.Y') }} anzeigen" alt="{{ $day->date->format('d.m.Y') }} anzeigen">
                                            </x-secondary-button-link>
                                        @endif
                                    </div>
                                </div>
                            </details>
                        </div>
                    @endforeach

                    <div class="day day_placeholder">
                    </div>
                    <div class="day day_placeholder">
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
