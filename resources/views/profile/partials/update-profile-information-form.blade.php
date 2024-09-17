@php use Illuminate\Contracts\Auth\MustVerifyEmail; @endphp
<section>
    <header>
        <h2 class="text-lg font-medium text-white">
            {{ __('Profil Informationen') }}
        </h2>

        <p class="mt-1 text-sm text-gray-300">
            {{ __("Aktualisiere deine Profil- & Kontoinformationen.") }}
        </p>

        <p class="mt-1 text-sm text-gray-300" aria-hidden="true">
            {{ __("Pflichtfelder sind mit einem Stern markiert") }}
            (<abbr class="req" title="{{ __("Pflichtfeld") }}">*</abbr>).</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        {{-- Name --}}
        <div>
            <x-input-label for="name" :value="__('Name')" :required="true"/>
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)"
                          required autofocus autocomplete="name"/>
            <x-input-error class="mt-2" :messages="$errors->get('name')"/>
        </div>

        {{-- Email Address --}}
        <div>
            <x-input-label for="email" :value="__('E-Mail')" :required="true"/>
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                          :value="old('email', $user->email)" required autocomplete="email"/>
            <x-input-error class="mt-2" :messages="$errors->get('email')"/>

            {{-- Email Verification --}}
            @if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Deine E-Mail-Adresse wurde noch nicht bestätigt.') }}

                        <button form="send-verification"
                                class="underline text-sm text-gray-500 hover:text-gray-400 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Klicke hier, um eine neue Bestätigungs-E-Mail zu senden.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('Eine neue Bestätigungs-E-Mail wurde an die angegebene Adresse gesendet.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        {{-- Gender --}}
        <div>
            <x-input-label for="gender" :value="__('Geschlecht')" :required="true"/>
            <select id="gender" name="gender" class="light_bg transition focus:transition border-2 border-gray-500 blue_border_focus rounded-md shadow-sm w-full" required>
                <option value="invalid" {{ old('gender') == 'invalid' ? 'selected' : '' }}>Bitte auswählen</option>
                <option value="m" {{ old('gender', $user->user_stats->gender ?? '') == 'm' ? 'selected' : '' }}>♂️ Männlich</option>
                <option value="f" {{ old('gender', $user->user_stats->gender ?? '') == 'f' ? 'selected' : '' }}>♀️ Weiblich</option>
                <option value="o" {{ old('gender', $user->user_stats->gender ?? '') == 'o' ? 'selected' : '' }}>⚧️ Divers</option>
            </select>
            <x-input-error :messages="$errors->get('gender')" class="mt-2" />
        </div>

        {{-- calorie goal --}}
        <div>
            <x-input-label for="cal_goal" :value="__('Globales Kalorienziel')" :required="true"/>
            <p class="my-1 text-gray-400 text-sm">{{ __('Wird verwendet, wenn an einem Tag kein spezifisches Ziel angegeben wird.') }}</p>
            <div class="w-6/12 flex gap-2 items-center">
                <x-number-input id="cal_goal" name="cal_goal" min="0" max="10000" step="1" class="flex-grow mt-1" :value="old('cal_goal', $user->user_stats->global_calorie_goal)"
                                required autofocus/>
                <p class="mt-1 w-1/12">kcal</p>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('cal_goal')"/>
        </div>

        {{-- Height --}}
        <div>
            <x-input-label for="height" :value="__('Größe')"/>
            <div class="w-6/12 flex gap-2 items-center">
                <x-number-input id="height" name="height" min="0" max="300" step="1" class="flex-grow mt-1" :value="old('height', $user->user_stats->height)"
                                autofocus/>
                <p class="mt-1 w-1/12">cm</p>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('height')"/>
        </div>

        {{-- Start Weight --}}
        <div>
            <x-input-label for="start_weight" :value="__('Startgewicht')"/>
            <div class="w-6/12 flex gap-2 items-center">
                <x-number-input id="start_weight" name="start_weight" min="0" max="200" step=".1" class="flex-grow mt-1" :value="old('start_weight', $user->user_stats->start_weight)"
                                autofocus/>
                <p class="mt-1 w-1/12">kg</p>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('start_weight')"/>
        </div>

        {{-- Target Weight --}}
        <div>
            <x-input-label for="target_weight" :value="__('Zielgewicht')"/>
            <div class="w-6/12 flex gap-2 items-center">
                <x-number-input id="target_weight" name="target_weight" min="0" max="200" step=".1" class="flex-grow mt-1" :value="old('target_weight', $user->user_stats->target_weight)"
                                autofocus/>
                <p class="mt-1 w-1/12">kg</p>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('target_weight')"/>
        </div>

        {{-- step goal --}}
        <div>
            <x-input-label for="step_goal" :value="__('Persönliches m-Ziel')"/>
            <div class="w-6/12 flex gap-2 items-center">
                <x-number-input id="step_goal" name="step_goal" min="0" max="20000" step="1" class="flex-grow mt-1" :value="old('step_goal', $user->user_stats->step_goal)"
                                autofocus/>
                <p class="mt-1 w-1/12">m</p>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('step_goal')"/>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Speichern') }}</x-primary-button>

            @if (session('status'))
                <p  x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 4000)"
                    class="text-sm text-gray-400">

                @if (session('status') === 'profile-updated')
                    {{ __('Gespeichert.') }}
                @elseif (session('status') === 'profile-no-changes')
                    {{ __('Keine Veränderungen.') }}
                @endif

                @if (session('info'))
                    {{ session('info') }}
                @endif

                </p>
            @endif
        </div>
    </form>
</section>
