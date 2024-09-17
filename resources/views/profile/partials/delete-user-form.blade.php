<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-white">
            {{ __('Konto löschen') }}
        </h2>

        <p class="mt-1 text-sm text-gray-400">
            {{ __('Wenn du dein Konto löschst, werden alle eingetragenen Daten deines Profils unwiderruflich gelöscht. Lade dir von dem Löschen alle Daten & Informationen herunter, die du behalten möchtest.') }}
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >{{ __('Konto löschen') }}</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-white">
                {{ __('Bist du dir sicher, dass du dein Konto löschen möchtest?') }}
            </h2>

            <p class="mt-1 text-sm text-gray-400">
                {{ __('Wenn du dein Konto löschst, werden alle eingetragenen Daten deines Profils unwiderruflich gelöscht. Bitte gebe dein Passwort ein, um deine Daten unwiderruflich zu löschen.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="{{ __('Passwort') }}"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Abbrechen') }}
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    {{ __('Konto löschen') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
