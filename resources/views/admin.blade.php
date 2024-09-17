<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">
            {{ __('Admin Tools') }}
        </h2>
    </x-slot>

    @include('components.status-msg')

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="table_background">
                <table class="ausgabe-admin">
                    <thead class="ausgabe-user-head">
                    <tr>
                        <th>Benutzer</th>
                        <th>Aktionen</th>
                    </tr>
                    </thead>
                    <tbody class="ausgabe-user-body">
                    @foreach ($users as $user)
                        <tr>
                            <td>
                                <div class="user_grid">
                                    <p class="user_grid_left"><strong>Username</strong>:</p> <p>{{ $user->name }}</p>
                                    <p class="user_grid_left"><strong>E-Mail</strong>:</p> <a class="underline" href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                                    <p class="user_grid_left"><strong>Rolle</strong>:</p>
                                    @if ($user->admin == 1)
                                        <p><strong class="text-green-600">&#x2B24; Admin</strong></p>
                                    @elseif ($user->d_id)
                                        <p><strong class="text-slate-600">&#x2B24; Dozent</strong></p>
                                    @else
                                        <p><strong class="text-slate-600">&#x2B24; Benutzer</strong></p>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="flex h-full items-center gap-3">
                                        <x-secondary-button-link @class(["admin-users-action"]) href="">
                                            <img class="admin-users-icons" src="{{ route('image.show', 'noun-edit-1047822.svg') }}" title="'{{ $user->name }}' bearbeiten" alt="'{{ $user->name }}' bearbeiten">
                                        </x-secondary-button-link>

                                        <x-danger-button @class(["admin-users-danger"]) x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion-{{ $user->u_id }}')">
                                            <img class="admin-users-icons" src="{{ route('image.show', 'noun-trash-2025467.svg') }}" title="'{{ $user->name }}' löschen" alt="'{{ $user->name }}' löschen">
                                        </x-danger-button>

                                        <x-modal name="confirm-user-deletion-{{ $user->u_id }}" :show="$errors->userDeletion->isNotEmpty()" focusable>
                                            <form method="post" action="" class="p-6">
                                                @csrf
                                                @method('delete')

                                                <h2 class="text-lg font-medium text-gray-900">
                                                        {{ __('Möchtest du den Benutzer "' . $user->name . '" wirklich löschen?') }}
                                                </h2>

                                                <p class="mt-1 text-sm text-gray-600">
                                                        {{ __('Dies wird den Benutzer und alle dessen eingetragenen Daten löschen:') }}
                                                </p>

                                                <ul class="mt-1 text-sm">
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
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>
