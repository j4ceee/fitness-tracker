@section('title')
    {{ __('Admin Tools') }}
@endsection

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">
            {{ __('Admin Tools') }}
        </h2>
    </x-slot>

    @include('components.status-msg')

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="flex justify-end mb-6">
                <x-blue-button-link @class(["text_icon_button"]) href="{{ route('users.create') }}">
                    <p>User hinzufügen</p><img src="{{ route('image.show', 'noun-plus-6413839.svg') }}" alt="">
                </x-blue-button-link>
            </div>

            <div class="user_list_bg">
                <div class="user_grid">
                    <div class="ausgabe_user_head">
                        <p><span>{{__('Benutzer')}}</span></p>
                        <p class="actions_header"><span>{{__('Aktionen')}}</span></p>
                    </div>
                    <div class="user_list">
                        @foreach ($users as $user)
                            <div class="user_card">
                                <div class="user_card_content">
                                    <div class="user_info user_info_grid">
                                        <p class="user_grid_items"><strong>{{__('Name')}}</strong>:</p> <p class="user_grid_items">{{ $user->name }}</p><br>
                                        <p class="user_grid_items"><strong>{{__('E-Mail')}}</strong>:</p> <a class="underline" href="mailto:{{ $user->email }}">{{ $user->email }}</a><br>
                                        <p class="user_grid_items"><strong>{{__('Rolle')}}</strong>:</p>
                                        @if ($user->admin == 1)
                                            <p class="user_grid_items"><strong class="text-green-600">&#x2B24; {{__('Admin')}}</strong></p>
                                        @else
                                            <p class="user_grid_items"><strong class="text-slate-600">&#x2B24; {{__('Benutzer')}}</strong></p>
                                        @endif
                                        <br>

                                                @if ($user->user_stats->group_code !== null)
                                                    <p class="user_grid_items"><strong>{{__('Gruppe')}}</strong>:</p>
                                                        <p class="user_grid_items"><code class="p-1 bg-gray-900 rounded-md">
                                                            <strong class="text-blue-400">{{ $user->user_stats->group_code }}</strong>
                                                        </code></p>
                                                @endif
                                    </div>
                                    <div class="flex h-full items-center gap-3 user_actions">
                                        <x-secondary-button-link @class(["admin-users-action"]) href="{{ route('users.edit', $user->id) }}">
                                            <img class="admin-users-icons" src="{{ route('image.show', 'noun-edit-1047822.svg') }}" title="'{{ $user->name }}' bearbeiten" alt="'{{ $user->name }}' bearbeiten">
                                        </x-secondary-button-link>

                                        <x-danger-button @class(["admin-users-danger"]) x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion-{{ $user->id }}')">
                                            <img class="admin-users-icons" src="{{ route('image.show', 'noun-trash-2025467.svg') }}" title="'{{ $user->name }}' löschen" alt="'{{ $user->name }}' löschen">
                                        </x-danger-button>

                                        <x-modal name="confirm-user-deletion-{{ $user->id }}" :show="$errors->userDeletion->isNotEmpty()" focusable>
                                            <form method="post" action="{{ route('users.destroy', $user->id) }}" class="p-6">
                                                @csrf
                                                @method('delete')

                                                <h2 class="text-lg font-medium text-white">
                                                    {{ __('Möchtest du den Benutzer "' . $user->name . '" wirklich löschen?') }}
                                                </h2>

                                                <p class="mt-1 text-sm text-gray-400">
                                                    {{ __('Dies wird den Benutzer und alle dessen eingetragenen Daten löschen:') }}
                                                </p>

                                                <ul class="mt-1 text-sm text-gray-200">
                                                    <li class="mt-1">{{ __('Benutzer: ') . $user->name }}</li>
                                                    <li class="mt-1">{{ __('E-Mail: ') . $user->email }}</li>
                                                    <li class="mt-1">{{ __('Alle Profildaten') }}</li>
                                                    <li class="mt-1">{{ __('Alle getrackten Tage') }}</li>
                                                    <li class="mt-1">{{ __('Alle Statistiken (z.B. Punktzahl)') }}</li>
                                                </ul>

                                                <div class="mt-6 flex justify-end">
                                                    <x-secondary-button x-on:click="$dispatch('close')">
                                                        {{ __('Abbrechen') }}
                                                    </x-secondary-button>

                                                    <x-danger-button class="ms-3">
                                                        {{ __('Benutzer löschen') }}
                                                    </x-danger-button>
                                                </div>
                                            </form>
                                        </x-modal>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
