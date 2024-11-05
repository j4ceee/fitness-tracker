<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Validation\Rules;

class ProfileController extends Controller
{
    public function dashboard(int $dayInt = 0): View
    {
        $user = Auth::user();

        // when dayInt is 0, show today's dashboard
        if ($dayInt === 0) {
            $date = now()->format('Y-m-d');
        }
        else {
            $date = now()->subDays($dayInt)->format('Y-m-d');
        }

        // get today
        $day = $user->days()
            ->where('date', $date)
            ->first();

        $userMonthly = $user->user_monthlies()
            ->where('month', now()->format('Y-m-01'))
            ->first();

        return view('dashboard', compact('day', 'date', 'dayInt', 'userMonthly'));
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        return view('profile.edit', compact('user'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // validate the request
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class.',email,'.$request->user()->id],
            'gender' => ['required', 'string', Rule::in(['m', 'f', 'o'])],
            'height' => ['nullable', 'numeric', 'integer', 'min:0', 'max:300'],
            'start_weight' => ['nullable', 'numeric', 'regex:/^\d+(\.\d{1})?$/', 'min:0', 'max:200'],
            'target_weight' => ['nullable', 'numeric', 'regex:/^\d+(\.\d{1})?$/', 'min:0', 'max:200'],
            'step_goal' => ['nullable', 'numeric', 'integer', 'min:0', 'max:20000'],
            'cal_goal' => ['required', 'numeric', 'integer', 'min:0', 'max:10000'],
            'group_code' => 'nullable|string|max:5',
        ]);

        $request->merge([
            'group_code' => strtolower($request->group_code),
        ]);

        $user_stats = $request->user()->user_stats;

        //$debugLog = 'Updated: ';

        // compare old values with new ones & only update if they are different
        $wasChanged = false;

        if ($request->user()->name !== $request->name) {
            $request->user()->name = $request->name;
            $wasChanged = true;
            //$debugLog .= 'name, ';
        }
        if ($request->user()->email !== $request->email) {
            $request->user()->email = $request->email;
            $wasChanged = true;
            //$debugLog .= 'email, ';
        }

        $wasChangedStats = false;
        if ($user_stats->gender !== $request->gender) {
            $user_stats->gender = $request->gender;
            $wasChangedStats = true;
            //$debugLog .= 'gender, ';
        }
        if ($request->height !== null && $user_stats->height != $request->height) {
            $user_stats->height = $request->height;
            $wasChangedStats = true;
            //$debugLog .= 'height, ';
        }
        if ($request->start_weight !== null && $user_stats->start_weight != $request->start_weight) {
            $user_stats->start_weight = $request->start_weight;
            $wasChangedStats = true;
            //$debugLog .= 'start_weight, ';
        }
        if ($request->target_weight !== null && $user_stats->target_weight != $request->target_weight) {
            $user_stats->target_weight = $request->target_weight;
            $wasChangedStats = true;
            //$debugLog .= 'target_weight, ';
        }
        if ($request->step_goal !== null && $user_stats->step_goal != $request->step_goal) {
            $user_stats->step_goal = $request->step_goal;
            $wasChangedStats = true;
            //$debugLog .= 'step_goal, ';
        }
        if ($user_stats->global_calorie_goal != $request->cal_goal) {
            $user_stats->global_calorie_goal = $request->cal_goal;
            $wasChangedStats = true;
            //$debugLog .= 'cal_goal, ';
        }

        if ($request->group_code !== $user_stats->group_code) {
            $user_stats->group_code = $request->group_code;
            $wasChangedStats = true;
            //$debugLog .= 'group_code, ';
        }

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        if ($wasChanged) {
            $request->user()->save();
        }
        if ($wasChangedStats) {
            $user_stats->save();
        }

        if ($wasChanged || $wasChangedStats) {
            return Redirect::route('profile.edit')
                ->with('status', 'profile-updated');
                //->with('info', $debugLog);
        }
        else {
            return Redirect::route('profile.edit')->with('status', 'profile-no-changes');
        }
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // check if user is last admin
        if ($user->admin && User::where('admin', true)->count() === 1) {
            return redirect(route('profile.edit'))->with('error', 'Dein Benutzerkonto kann nicht gelöscht werden, da du der letzte Administrator bist.');
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
