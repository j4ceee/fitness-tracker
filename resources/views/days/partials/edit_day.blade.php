@if ($day->id ?? null)
    <form method="post" action="{{ route('days.update', $day->id) }}" class="day_form light_bg">
        @method('PATCH')
@elseif (request()->routeIs('days.my') || request()->routeIs('dashboard') || request()->routeIs('days.create'))
    <form method="post" action="{{ route('days.store') }}" class="day_form light_bg">
        @method('PUT')
@endif
    @csrf
    @unless($day->id ?? null || request()->routeIs('dashboard'))
        {{-- when creating a new day & not on the dashboard page --}}
        <fieldset class="day_form_date">
            <x-input-label for="date" :value="__('Datum')" :required="true"/>
            <x-date-input id="date" name="date" class="flex-grow mt-1"
                          :value="old('date', $date)"
                          required/>
        </fieldset>
    @elseif (request()->routeIs('dashboard'))
        <input type="hidden" id="date" name="date" value="{{ $date }}">
    @endunless

    <div class="day_form_header">
        {{-- Progress Circle, https://codepen.io/yichinweng/pen/WNvXevO --}}
        <div class="progress_bar_cont" id="progress_bar_cont" data-progress="{{ $day->percentage_of_goal ?? 0 }}" data-mode="edit">
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
        <fieldset class="day_form_cat day_form_activ">
            <legend class="day_form_cat_h">{{__('Training')}}</legend>

            <x-day-form-details :cat_class="'day_form_activ'" :cat_name="'Training'">
                <p>{{__('Hier kannst du deine sportlichen Aktivitäten eintragen.')}}</p>
                <p>{{__('Die Trainingsdauer wird in Minuten angegeben, die Schritte in Kilometern.')}}</p>
            </x-day-form-details>

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
                    <x-number-input id="steps" name="steps" min="0" max="40" step="1" class="flex-grow mt-1"
                                    :value="old('steps', $day->steps ?? 0)"
                                    required/>
                    <p class="mt-1 w-1/12">{{__('km')}}</p>
                </div>
                <x-input-error class="mt-2" :messages="$errors->get('steps')"/>
            </div>
        </fieldset>

        <fieldset class="day_form_cat day_form_nutrition">
            <legend class="day_form_cat_h">{{__('Ernährung')}}</legend>

            <x-day-form-details :cat_class="'day_form_nutrition'" :cat_name="'Ernährung'">
                <p>{{__('Hier kannst du deine Kalorien eintragen.')}}</p>
                <p>{{__('Falls du auf deinem Profil ein globales Kalorienziel festgelegt hast, wird dieses hier automatisch eingetragen.')}}</p>
            </x-day-form-details>

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

        <fieldset class="day_form_cat day_form_meals">
            <legend class="day_form_cat_h">{{__('Meal-Tracker')}}</legend>

            <x-day-form-details :cat_class="'day_form_meals'" :cat_name="'Meal-Tracker'">
                <p>{{__('Hier kannst du deine')}} <strong class="underline">{{__('selbstgemachten')}}</strong> {{__('Mahlzeiten eintragen.')}}</p>
                <p>{{__('Fertiggerichte (z.B. Tiefkühlpizza) zählen nicht dazu.')}}</p>
            </x-day-form-details>

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

        <fieldset class="day_form_cat day_form_neg">
            <legend class="day_form_cat_h">{{__('Diät')}}</legend>

            <x-day-form-details :cat_class="'day_form_neg'" :cat_name="'Diät'">
                <p>{{__('Hier kannst du eintragen, ob du an diesem Tag gegen Diätvorsätze verstoßen hast.')}}</p>
                <p>{{__('Falls du ein Nahrungsmittel aus den folgenden Kategorien zu dir genommen hast, setze den Haken.')}}</p>
            </x-day-form-details>

            {{-- Alcohol --}}
            <div>
                <x-image-toggle name="took_alcohol" svgName="noun-alcohol-6779240" isChecked="{{ (bool)old('took_alcohol', $day->took_alcohol ?? false) }}">
                    {{__('Alkohol')}}
                </x-image-toggle>
                <x-input-error class="mt-2" :messages="$errors->get('took_alcohol')"/>
            </div>

            {{-- Fast food --}}
            <div>
                <x-image-toggle name="took_fast_food" svgName="noun-burger-6779289" isChecked="{{ (bool)old('took_fast_food', $day->took_fast_food ?? false) }}">
                    {{__('Fast Food')}}
                </x-image-toggle>
                <x-input-error class="mt-2" :messages="$errors->get('took_fast_food')"/>
            </div>

            {{-- Sweets --}}
            <div>
                <x-image-toggle name="took_sweets" svgName="noun-lollipop-6779258" isChecked="{{ (bool)old('took_sweets', $day->took_sweets ?? false) }}">
                    {{__('Süßigkeiten')}}
                </x-image-toggle>
                <x-input-error class="mt-2" :messages="$errors->get('took_sweets')"/>
            </div>
        </fieldset>

        <fieldset class="day_form_cat day_form_misc">
            <legend class="day_form_cat_h">{{__('Weiteres')}}</legend>

            <x-day-form-details :cat_class="'day_form_misc'" :cat_name="'Weiteres'">
                <p>{{__('Hier kannst du den Tag als Cheat-Day markieren und dein Gewicht eintragen.')}}</p>
                <p>{{__('Wenn du den Tag als Cheat-Day markierst, werden dir keine Punkte für das Verfehlen von Zielen abgezogen.')}}
            </x-day-form-details>

            {{-- Is this day a cheat day? --}}
            <div>
                <x-image-toggle class="mt-0.5" name="is_cheat_day" svgName="cheat-day-white" isChecked="{{ (bool)old('is_cheat_day', $day->is_cheat_day ?? false) }}">
                    {{__('Cheat Day')}}
                </x-image-toggle>
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
