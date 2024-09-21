<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserMonthly;
use Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class DayController extends Controller
{
    /**
     * List all days of a month depending on the user & page.
     * Page 0 = current month
     * 1 = previous month
     * 2 = 2 months ago
     * ...
     */
    public function index(int $userId, int $page = 0): View
    {
        $user = User::findOrFail($userId);

        $days = $user->days()
            ->where('date', '>=', now()->subMonths($page)->startOfMonth()) // where date is after the start of the month now - page months
            ->where('date', '<=', now()->subMonths($page)->endOfMonth()) // where date is before the end of the month now - page months
            ->get();

        $day = $user->days()
            ->where('date', now()->startOfDay())
            ->first();

        return view('days.index', compact('user', 'days', 'day', 'page'));
    }

    public function indexMy(int $page = 0): View
    {
        $user = Auth::user();

        return $this->index($user->id, $page);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $today = now()->startOfDay();
        // check if day already exists for the user
        $day = $request->user()->days()->where('date', $today)->first();

        if ($day) {
            redirect()->route('days.my')->with('error', 'Der Tag wurde bereits angelegt.');
        }

        $request->validate([
            'day_calorie_goal' => ['nullable', 'numeric', 'integer', 'min:0', 'max:10000'],
            'calories' => ['required', 'numeric', 'integer', 'min:0', 'max:10000'],
            'meals_warm' => ['required', 'numeric', 'integer', 'min:0', 'max:5'],
            'meals_cold' => ['required', 'numeric', 'integer', 'min:0', 'max:5'],
            'water' => ['required', 'numeric', Rule::in([0, 0.5, 1, 1.5, 2, 2.5, 3])],
            'training_duration' => ['required', 'numeric', 'integer', Rule::in(range(0, 210, 15))],
            'steps' => ['required', 'numeric', Rule::in(range(0, 40, 0.5))],
            'is_cheat_day' => [Rule::in([0, 1])],
            'weight' => ['nullable', 'numeric', 'regex:/^\d+(\.\d{1})?$/', 'min:0', 'max:200'],
        ]);

        // check if the user has a user_monthlies entry for the current month
        if (!$request->user()->user_monthlies()->where('month', now()->startOfMonth())->first()) {
            UserMonthly::create([
                'user_id' => $request->user()->id,
                'month' => now()->startOfMonth(),
            ]);
        }
        else {
            // check if the user has any cheat days left
            $cheatDays = $request->user()->user_monthlies()->where('month', now()->startOfMonth())->first()->cheat_days;
            if ($cheatDays >= 4 && $request->is_cheat_day) {
                return redirect()->route('days.my')->with('error', 'Du hast keine Cheat-Days mehr übrig.');
            }
        }

        $dayCalorieGoal = $request->day_calorie_goal ?? $request->user()->user_stats->global_calorie_goal ?? null;

        if (!$dayCalorieGoal) {
            return redirect()->route('days.my')->with('error', 'Es wurde kein Kalorienziel festgelegt (weder global noch für den Tag).');
        }

        $caloriePercentage = $request->calories / $dayCalorieGoal;
        $caloriePercentage = round($caloriePercentage, 4);
        $caloriePercentage = min(1, max(0, $caloriePercentage));

        $points = $this->calculatePoints(
            $caloriePercentage,
            $request->training_duration,
            $request->steps,
            $request->water,
            $request->meals_warm,
            $request->meals_cold,
            $request->is_cheat_day ?? false
        );

        try {
            $day = $request->user()->days()->create([
                'date' => $today,
                'weight' => $request->weight,
                'training_duration' => $request->training_duration,
                'day_calorie_goal' => $dayCalorieGoal,
                'percentage_of_goal' => $caloriePercentage,
                'calories' => $request->calories,
                'water' => $request->water,
                'steps' => $request->steps,
                'meals_warm' => $request->meals_warm,
                'meals_cold' => $request->meals_cold,
                'is_cheat_day' => $request->is_cheat_day ?? false,
                'points' => $points,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error creating day record: ' . $e->getMessage());
            return redirect()->route('days.my')->with('error', 'Ein Fehler ist aufgetreten beim Speichern des Tages: ' . $e->getMessage());
        }

        // update user_monthlies
        $userMonthly = $request->user()->user_monthlies()->where('month', now()->startOfMonth())->first();
        $userMonthly->update([
            'points' => $userMonthly->points + $points,
            'cheat_days' => $userMonthly->cheat_days + ($request->is_cheat_day ? 1 : 0),
        ]);

        return redirect()->route('days.my')->with('status', 'day-updated');
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function calculatePoints(float $caloriePercent, int $trainingDuration, float $steps, float $water, int $warmMeals, int $coldMeals, bool $cheatDay): int
    {
        $points = 0;

        // calculate points for calorie percentage
        if ($caloriePercent == 1) { // reached the goal
            $points += 2;
        }
        else if ($caloriePercent >= 0.9) { // missing 10% of the goal
            $points += 1;
        }
        else if ($caloriePercent > 0.6) { // missing 40% of the goal
            $points += 0;
        }
        else if (!$cheatDay) { // missing more than 40% of the goal
            $points -= 1;
        }

        // calculate points for training duration (2 points per 30 minutes), round down
        $points += floor($trainingDuration / 30) * 2;

        // calculate points for water intake
        if ($water >= 3) {
            $points += 3;
        }
        else if ($water >= 2) {
            $points += 2;
        }
        else if ($water >= 1.5) {
            $points += 1;
        }
        else if ($water >= 1) {
            $points += 0;
        }
        else if (!$cheatDay){
            $points -= 1;
        }

        // calculate points for steps (1 point per 10km)
        $points += floor($steps / 10);

        // calculate points for warm meals (2 points per meal)
        $points += $warmMeals * 2;

        // calculate points for cold meals (1 point per meal)
        $points += $coldMeals;

        return $points;
    }
}
