<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('users.manage_user');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'boolean'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'admin' => $request->role,
        ]);

        return redirect(route('admin.index'))->with('success', 'Der Benutzer "'.$user->name.'" wurde erfolgreich erstellt.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        return view('users.manage_user', ['user' => $user]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class.',email,'.$id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'boolean'],
        ]);

        $user = User::findOrFail($id);

        $wasChanged = false;

        if ($user->name !== $request->name) {
            $user->name = $request->name;
            $wasChanged = true;
        }
        if ($user->email !== $request->email) {
            $user->email = $request->email;
            $wasChanged = true;
        }
        if ($request->password !== null) {
            $user->password = bcrypt($request->password);
            $wasChanged = true;
        }
        if ($user->admin != $request->role) {

            // check if user is last admin
            if ($user->admin && User::where('admin', true)->count() === 1) {
                return redirect(route('admin.index'))->with('error', 'Der Benutzer "'.$user->name.'" kann nicht geändert werden, da er der letzte Administrator ist.');
            }

            $user->admin = $request->role;
            $wasChanged = true;
        }

        if ($wasChanged) {
            $user->save();
            return redirect(route('admin.index'))->with('success', 'Der Benutzer "'.$user->name.'" wurde erfolgreich aktualisiert.');
        }
        else {
            return redirect(route('admin.index'))->with('info', 'Es wurden keine Änderungen am Benutzer "'.$user->name.'" vorgenommen.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        // check if user is last admin
        if ($user->admin && User::where('admin', true)->count() === 1) {
            return redirect(route('admin.index'))->with('error', 'Der Benutzer "'.$user->name.'" kann nicht gelöscht werden, da er der letzte Administrator ist.');
        }

        $userName = $user->name;

        // handle current user deletion
        if (Auth::id() === $user->id) {
            Auth::logout();
        }

        $user->delete();

        return redirect(route('admin.index'))->with('success', 'Der Benutzer "'.$userName.'" wurde erfolgreich gelöscht.');
    }
}
