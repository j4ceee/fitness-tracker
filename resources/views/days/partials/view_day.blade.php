<div class="day_form light_bg">
    @if (request()->routeIs('days.my'))
        <h2 class="text-lg font-semibold text-gray-300">Heute, <time datetime="@php echo date('Y-m-d'); @endphp" class="text-xl text-white">@php echo date('d.m.Y'); @endphp</time></h2>
    @endif
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
        <div class="day_form_cat day_form_nutrition">
            <p class="day_form_cat_h">{{__('Ernährung')}}</p>
            {{-- Day Calorie Goal --}}
            <div>
                <p>{{ __('Tägl. Kalorienziel') }}</p>
                <div class="w-6/12 flex gap-2 items-center">
                    <p class="day_form_show_input">{{ $day->day_calorie_goal ?? 0 }}</p>
                    <p class="mt-1 w-1/12">kcal</p>
                </div>
            </div>

            {{-- Day Calories --}}
            <div>
                <p>{{ __('Kalorien') }}</p>
                <div class="w-6/12 flex gap-2 items-center">
                    <p class="day_form_show_input">{{ $day->calories ?? 0 }}</p>
                    <p class="mt-1 w-1/12">kcal</p>
                </div>
            </div>
        </div>

        <div class="day_form_cat day_form_meals">
            <p class="day_form_cat_h">{{__('Meal-Tracker')}}</p>
            {{-- Meals Warm --}}
            <div>
                <p>{{ __('Warm') }}</p>
                <div class="w-6/12 flex gap-2 items-center">
                    <p class="day_form_show_input">{{ $day->meals_warm ?? 0 }}</p>
                    <p class="mt-1 w-1/12">{{__('Meals')}}</p>
                </div>
            </div>

            {{-- Meals Cold --}}
            <div>
                <p>{{ __('Kalt') }}</p>
                <div class="w-6/12 flex gap-2 items-center">
                    <p class="day_form_show_input">{{ $day->meals_cold ?? 0 }}</p>
                    <p class="mt-1 w-1/12">{{__('Meals')}}</p>
                </div>
            </div>
        </div>

        <div class="day_form_cat day_form_water">
            <p class="day_form_cat_h">{{__('Wasser-Tracker')}}</p>
            {{-- Water --}}
            <p>{{ __('Wasser') }}</p>

            <div class="w-full flex items-center gap-3 justify-center">
                <p class="text-xl font-bold">
                    <span id="water_count">{{ number_format($day->water ?? '0', 2) }}</span> {{__('L')}}
                </p>
            </div>
        </div>

        <div class="day_form_cat day_form_activ">
            <p class="day_form_cat_h">{{__('Aktivitäten')}}</p>

            {{-- Training Minutes --}}
            <div>
                <p>{{ __('Trainingsdauer') }}</p>
                <div class="w-6/12 flex gap-2 items-center">
                    <p class="day_form_show_input">{{ $day->training_duration ?? 0 }}</p>
                    <p class="mt-1 w-1/12">min</p>
                </div>
            </div>

            {{-- Steps (in km) --}}
            <div>
                <p>{{ __('Kilometer') }}</p>
                <div class="w-6/12 flex gap-2 items-center">
                    <p class="day_form_show_input">{{ $day->steps ?? 0 }}</p>
                    <p class="mt-1 w-1/12">{{__('km')}}</p>
                </div>
            </div>
        </div>

        <div class="day_form_cat day_form_misc">
            <p class="day_form_cat_h">{{__('Weiteres')}}</p>

            {{-- Is this day a cheat day? --}}
            <div>
                <div class="w-6/12 flex gap-2 items-center">
                    <p>{{ __('Cheat Day') }}</p>
                    <input type="checkbox" class="mt-1" {{ ($day->is_cheat_day ?? false) ? 'checked' : '' }} disabled>
                </div>
            </div>

            {{-- Weight --}}
            <div>
                <p>{{ __('Gewicht') }}</p>
                <div class="w-6/12 flex gap-2 items-center">
                    @if ($day->weight ?? null)
                        <p class="day_form_show_input">{{ $day->weight }}</p>
                    @else
                        <p class="day_form_show_input"><span class="text-white select-none" aria-hidden="true">0</span></p>
                    @endif
                    <p class="mt-1 w-1/12">kg</p>
                </div>
            </div>
        </div>

        <div class="day_form_cat day_form_placeholder">
        </div>
    </div>
</div>
