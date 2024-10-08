@vite(['resources/js/login.js', 'resources/css/login.css'])

{{-- Session Status --}}
<x-auth-session-status class="mb-4" :status="session('status')" />

<form method="POST" action="{{ route('login') }}" class="login_form p-6 rounded-lg">
    @csrf

    <div class="login_inputs" id="login_inputs" aria-hidden="true" data-hidden="true">
        {{-- Email Address --}}
        <div>
            <x-input-label for="email" :value="__('E-Mail')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" disabled/>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Password --}}
        <div class="mt-4">
            <x-input-label for="password" :value="__('Passwort')" />

            <x-text-input id="password" class="block mt-1 w-full"
                          type="password"
                          name="password"
                          required autocomplete="current-password" disabled/>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Remember Me --}}
        <div class="flex items-center justify-between mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember" disabled>
                <span class="ms-2 text-sm text-gray-300">{{ __('Login merken') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-300 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Passwort vergessen?') }}
                </a>
            @endif
        </div>
    </div>

    <div class="login_btn_cont">
        <div class="login_layout"></div>
        <x-primary-button class="mt-4" id="login_btn">
            {{ __('Login') }}
        </x-primary-button>
    </div>
</form>
