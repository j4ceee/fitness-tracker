@vite(['resources/js/leaderboard.js'])

@section('title')
    {{ __('Leaderboard') }}
@endsection

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">
            {{ __('Leaderboard') }}
        </h2>
    </x-slot>

    @include('components.status-msg')

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 lb_vars">
            <input type="hidden" id="leaderboard_array" value="{{ $lb_array }}">

            <template id="user_temp">
                <div class="team">
                    <p class="rank"></p>
                    <div class="team_info">
                        <p class="name">Team 1</p>
                        <p class="team_info_p team_info_total"><span class="point_label">Gesamt Punkte:</span> <span class="total_score">0</span></p>
                        <p class="team_info_p team_info_month"><span class="point_label">Monatl. Punkte:</span> <span class="month_score">0</span></p>
                        <p class="team_info_p team_info_day"><span class="point_label">Tägl. Punkte:</span> <span class="day_score">0</span></p>
                    </div>
                    <a class="team_calendar_link" href="#">
                        <img class="team_calendar_icon" src="{{ route('image.show', 'noun-calendar-5490924-white.svg') }}" title="" alt="">
                    </a>
                </div>
            </template>

            <div class="lb_info_container">
                <p class="rank">Rang</p>
                <p class="name">Name</p>
                <button id="sort_total" class="total_score sort_btn">Gesamt Punkte</button>
                <button id="sort_month" class="month_score sort_btn">Monatl. Punkte</button>
                <button id="sort_day" class="day_score sort_btn">Tägl. Punkte</button>
            </div>

            <div class="leaderboard_container" data-sort="total">
            </div>

        </div>
    </div>
</x-app-layout>
