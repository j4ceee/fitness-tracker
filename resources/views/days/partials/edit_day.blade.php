@if ($day->id ?? null)
    <form method="post" action="{{ route('days.update', $day->id) }}" class="day_form light_bg">
        @method('PATCH')
@elseif (request()->routeIs('days.my') || request()->routeIs('dashboard') || request()->routeIs('days.create'))
    <form method="post" action="{{ route('days.store') }}" class="day_form light_bg">
        @method('PUT')
@endif
    @csrf
    @if (request()->routeIs('days.my') || request()->routeIs('dashboard'))
        <h2 class="text-lg font-semibold text-gray-300">Heute, <time datetime="@php echo date('Y-m-d'); @endphp" class="text-xl text-white">@php echo date('d.m.Y'); @endphp</time></h2>
    @endif

    @unless($day->id ?? null || request()->routeIs('days.my') || request()->routeIs('dashboard'))
        {{-- when creating a new day & not on the dashboard or my days page --}}
        <fieldset class="day_form_date">
            <x-input-label for="date" :value="__('Datum')" :required="true"/>
            <x-date-input id="date" name="date" class="flex-grow mt-1"
                          :value="old('date', date('Y-m-d'))"
                          required/>
        </fieldset>
    @elseif (request()->routeIs('days.my') || request()->routeIs('dashboard'))
        <input type="hidden" id="date" name="date" value="{{ date('Y-m-d') }}">
    @endunless

    <div class="day_form_header">
        {{-- Progress Circle, https://codepen.io/yichinweng/pen/WNvXevO --}}
        <div class="progress_bar_cont" id="progress_bar_cont" data-progress="{{ $day->percentage_of_goal ?? 0 }}">
            <div class="cal_stats">
                <p id="cal_progress_text" class="text-lg font-bold m-0 p-0 leading-5">{{ $day->calories ?? 0 }}</p>
                <p class="text-gray-400 font-bold m-0 p-0 leading-5">
                    / <span id="cal_goal_text">{{ $day->day_calorie_goal ?? $user->user_stats->global_calorie_goal ?? 0 }}</span></p>
                <p class="text-sm text-gray-500 font-bold m-0 p-0 leading-none absolute bottom-11">kcal</p>
            </div>

            <svg viewbox="0 0 110 110" class="progress_circle">
                <linearGradient id="gradient" x1="0" y1="0" x2="0" y2="100%">
                    <stop offset="0%" stop-color="#259FE2"/>
                    <stop offset="100%" stop-color="#0baeff"/>
                </linearGradient>
                <path class="grey" d="M30,90 A40,40 0 1,1 80,90" fill='none'/>
                <path id="progress_bar_blue" fill='none' class="blue" d="M30,90 A40,40 0 1,1 80,90"/>
            </svg>
        </div>

        <div class="ml-4">
            <p class="text-sm text-gray-400">{{__('Punkte:')}}</p>
            @if ($day->id ?? null)
                @if ($day->points >= 1)
                    <p class="text-2xl font-bold text-green-300">{{ $day->points }}</p>
                @else
                    <p class="text-2xl font-bold text-red-500">{{ $day->points }}</p>
                @endif
            @else
                <p class="text-2xl font-bold text-red-500">0 <span class="text-gray-500 text-sm">{{__('(Bitte speichern)')}}</span></p>
            @endif
        </div>
    </div>

    <div class="day_form_content">
        <fieldset class="day_form_cat day_form_nutrition">
            <legend class="day_form_cat_h">{{__('Ernährung')}}</legend>
            {{-- Day Calorie Goal --}}
            <div>
                @if ($user->user_stats->global_calorie_goal ?? null)
                    <x-input-label for="day_calorie_goal" :value="__('Tägl. Kalorienziel')"/>
                @else
                    <x-input-label for="day_calorie_goal" :value="__('Tägl. Kalorienziel')" :required="true"/>
                @endif
                {{-- <p class="my-1 text-gray-400 text-sm">{{ __('Verwendet das im Profil festgelegte globale Kalorienziel, falls keines gesetzt wird.') }}</p> --}}
                <div class="w-6/12 flex gap-2 items-center">
                    @if ($user->user_stats->global_calorie_goal ?? null)
                        <x-number-input id="day_calorie_goal" name="day_calorie_goal" min="0" max="10000" step="1"
                                        class="flex-grow mt-1"
                                        :value="old('day_calorie_goal', $day->day_calorie_goal ?? '')"
                                        placeholder="{{ $user->user_stats->global_calorie_goal }}"/>
                    @else
                        <x-number-input id="day_calorie_goal" name="day_calorie_goal" min="0" max="10000" step="1"
                                        class="flex-grow mt-1"
                                        :value="old('day_calorie_goal', $day->day_calorie_goal ?? 0)" required/>
                    @endif
                    <p class="mt-1 w-1/12">kcal</p>
                </div>
                <x-input-error class="mt-2" :messages="$errors->get('day_calorie_goal')"/>
            </div>

            {{-- Day Calories --}}
            <div>
                <x-input-label for="calories" :value="__('Kalorien')" :required="true"/>
                <div class="w-6/12 flex gap-2 items-center">
                    <x-number-input id="calories" name="calories" min="0" max="10000" step="1" class="flex-grow mt-1"
                                    :value="old('calories', $day->calories ?? 0)"
                                    required/>
                    <p class="mt-1 w-1/12">kcal</p>
                </div>
                <x-input-error class="mt-2" :messages="$errors->get('calories')"/>
            </div>
        </fieldset>

        <fieldset class="day_form_cat day_form_meals">
            <legend class="day_form_cat_h">{{__('Meal-Tracker')}}</legend>
            {{-- Meals Warm --}}
            <div>
                <x-input-label for="meals_warm" :value="__('Warm')" :required="true"/>
                <div class="w-6/12 flex gap-2 items-center">
                    <x-number-input id="meals_warm" name="meals_warm" min="0" max="5" step="1" class="flex-grow mt-1"
                                    :value="old('meals_warm', $day->meals_warm ?? 0)"
                                    required/>
                    <p class="mt-1 w-1/12">{{__('Meals')}}</p>
                </div>
                <x-input-error class="mt-2" :messages="$errors->get('meals_warm')"/>
            </div>

            {{-- Meals Cold --}}
            <div>
                <x-input-label for="meals_cold" :value="__('Kalt')" :required="true"/>
                <div class="w-6/12 flex gap-2 items-center">
                    <x-number-input id="meals_cold" name="meals_cold" min="0" max="5" step="1" class="flex-grow mt-1"
                                    :value="old('meals_cold', $day->meals_cold ?? 0)"
                                    required/>
                    <p class="mt-1 w-1/12">{{__('Meals')}}</p>
                </div>
                <x-input-error class="mt-2" :messages="$errors->get('meals_cold')"/>
            </div>
        </fieldset>

        <fieldset class="day_form_cat day_form_water">
            <legend class="day_form_cat_h">{{__('Wasser-Tracker')}}</legend>
            {{-- Water --}}
            <x-input-label for="water" :value="__('Wasser')" :required="true"/>

            <input type="hidden" id="water" name="water" min="0" max="3" step=".5" class="flex-grow mt-1"
                   value="{{ old('water', $day->water ?? '0') }}"
                   required/>

            <div class="w-full flex items-center gap-3 justify-center">
                <button class="water_btn" id="water_btn_minus" type="button"><span>-</span></button>
                <p class="text-xl font-bold"><span
                        id="water_count">{{ number_format(old('water', $day->water ?? '0'), 2) }}</span> {{__('L')}}</p>
                <button class="water_btn" id="water_btn_plus" type="button"><span>+</span></button>
            </div>

            <x-input-error class="mt-2" :messages="$errors->get('water')"/>
        </fieldset>

        <fieldset class="day_form_cat day_form_activ">
            <legend class="day_form_cat_h">{{__('Aktivitäten')}}</legend>

            {{-- Training Minutes --}}
            <div>
                <x-input-label for="training_duration" :value="__('Trainingsdauer')" :required="true"/>
                <div class="w-6/12 flex gap-2 items-center">
                    <x-number-input id="training_duration" name="training_duration" min="0" max="210" step="15"
                                    class="flex-grow mt-1"
                                    :value="old('training_duration', $day->training_duration ?? 0)"
                                    required/>
                    <p class="mt-1 w-1/12">min</p>
                </div>
                <x-input-error class="mt-2" :messages="$errors->get('training_duration')"/>
            </div>

            {{-- Steps (in km) --}}
            <div>
                <x-input-label for="steps" :value="__('Kilometer')" :required="true"/>
                <div class="w-6/12 flex gap-2 items-center">
                    <x-number-input id="steps" name="steps" min="0" max="40" step=".5" class="flex-grow mt-1"
                                    :value="old('steps', $day->steps ?? 0)"
                                    required/>
                    <p class="mt-1 w-1/12">{{__('km')}}</p>
                </div>
                <x-input-error class="mt-2" :messages="$errors->get('steps')"/>
            </div>
        </fieldset>

        <fieldset class="day_form_cat day_form_misc">
            <legend class="day_form_cat_h">{{__('Weiteres')}}</legend>

            {{-- Is this day a cheat day? --}}
            <div>
                <div class="w-6/12 flex gap-2 items-center">
                    <x-input-label for="is_cheat_day" :value="__('Cheat Day')"/>
                    <input type="hidden" name="is_cheat_day" value="0">
                    <input type="checkbox" id="is_cheat_day" name="is_cheat_day" class="mt-1" value="1" {{ old('is_cheat_day', $day->is_cheat_day ?? false) ? 'checked' : '' }}>
                </div>
                <x-input-error class="mt-2" :messages="$errors->get('is_cheat_day')"/>
            </div>

            {{-- Weight --}}
            <div>
                <x-input-label for="weight" :value="__('Gewicht')"/>
                <div class="w-6/12 flex gap-2 items-center">
                    <x-number-input id="weight" name="weight" min="0" max="200" step=".1" class="flex-grow mt-1"
                                    :value="old('weight', $day->weight ?? '')"
                    />
                    <p class="mt-1 w-1/12">kg</p>
                </div>
                <x-input-error class="mt-2" :messages="$errors->get('weight')"/>
            </div>
        </fieldset>

        <div class="day_form_cat day_form_placeholder">
        </div>
    </div>
    <div class="flex items-center justify-end gap-4">
        @if (session('status'))
            <p x-data="{ show: true }"
               x-show="show"
               x-transition
               x-init="setTimeout(() => show = false, 4000)"
               class="text-sm text-gray-400">

                @if (session('status') === 'day-updated')
                    {{ __('Gespeichert.') }}
                @elseif (session('status') === 'day-no-changes')
                    {{ __('Keine Veränderungen.') }}
                @endif
            </p>
        @endif

        <x-primary-button type="submit">{{ __('Speichern') }}</x-primary-button>
    </div>
</form>
