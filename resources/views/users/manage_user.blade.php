<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ isset($user) ? __('Benutzer bearbeiten') : __('Benutzer erstellen')}}
        </h2>
    </x-slot>

    <div class="py-12 sm:max-w-md mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="p-4 sm:p-8 light_bg shadow rounded-lg">
            @if (isset($user))
                <form method="POST" action="{{ route('users.update', $user->id) }}">
                @method('PATCH')
            @else
                <form method="POST" action="{{ route('users.store') }}">
            @endif

            @csrf

            <div class="flex gap-20 flex-wrap justify-evenly">
                @if (isset($user))
                    @include('users.data_user', ['user' => $user])
                @else
                    @include('users.data_user')
                @endif
            </div>


            <div class="flex items-center justify-end mt-8">
                <x-primary-button class="ms-4">
                    {{ isset($user) ? __('Benutzer aktualisieren') : __('Benutzer erstellen')}}
                </x-primary-button>
            </div>
        </form>
        </div>
    </div>
</x-app-layout>
