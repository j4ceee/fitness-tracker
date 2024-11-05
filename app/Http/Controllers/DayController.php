<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserMonthly;
use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Validation\Rule;
use Throwable;

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
            ->orderBy('date', 'asc')
            ->get();

        // get today
        $day = $user->days()
            ->where('date', now()->startOfDay())
            ->first();

        $userMonthly = $user->user_monthlies()->where('month', now()->subMonths($page)->startOfMonth())->first();

        return view('days.index', compact('user', 'days', 'day', 'userMonthly', 'page'));
    }

    public function indexMy(int $page = 0): View
    {
        $user = Auth::user();

        return $this->index($user->id, $page);
    }

    public function indexDay(int $userId, string $date): View|RedirectResponse
    {
        // check if date is valid
        if (!strtotime($date)) {
            if ($userId == Auth::id()) {
                return redirect()->route('days.my')->with('error', 'Ungültiges Datum.');
            }
            return redirect()->route('days.index', $userId)->with('error', 'Ungültiges Datum.');
        }

        $user = User::findOrFail($userId);

        $day = $user->days()
            ->where('date', $date)
            ->first();

        return view('days.view', compact('user', 'day'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $user = Auth::user();

        $date = now()->format('Y-m-d');

        return view('days.create', compact('user', 'date'));
    }

    /**
     * Store a newly created resource in storage.
     * @throws Throwable
     */
    public function store(Request $request): RedirectResponse
    {
        return DB::transaction(function () use ($request) {

            $request->validate([
                'date' => ['required', 'date'],
            ]);

            $date = date('Y-m-d', strtotime($request->date));

            // check if day already exists for the user
            $day = $request->user()->days()->where('date', $date)->first();

            if ($day) {
                return redirect()->route('days.my')->with('error', 'Der Tag wurde bereits angelegt.');
            }

            // cast is_cheat_day, took_alcohol, took_fast_food, took_sweets to boolean
            $request->merge([
                'is_cheat_day' => $request->has('is_cheat_day'),
                'took_alcohol' => $request->has('took_alcohol'),
                'took_fast_food' => $request->has('took_fast_food'),
                'took_sweets' => $request->has('took_sweets'),
            ]);

            $this->validateDay($request);

            $userMonthly = $request->user()->user_monthlies()->where('month', date('Y-m-01', strtotime($date)))->first();
            // check if the user has a user_monthlies entry for the month of the day
            if (!$userMonthly) {
                $userMonthly = UserMonthly::create([
                    'user_id' => $request->user()->id,
                    'month' => date('Y-m-01', strtotime($date)),
                ]);
            } else {
                // check if the user has any cheat days left
                $cheatDays = $userMonthly->cheat_days_used;
                if ($cheatDays >= Config::get('constants.max_cheat_days') && $request->is_cheat_day) {
                    return redirect()->route('days.my')->with('error', 'Du hast keine Cheat-Days mehr übrig: ' . $cheatDays . ' von ' . Config::get('constants.max_cheat_days') . ' verbraucht.');
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
                $request->is_cheat_day,
                $request->took_alcohol,
                $request->took_fast_food,
                $request->took_sweets
            );

            try {
                $request->user()->days()->create([
                    'date' => $date,
                    'weight' => $request->weight,
                    'training_duration' => $request->training_duration,
                    'day_calorie_goal' => $dayCalorieGoal,
                    'percentage_of_goal' => $caloriePercentage,
                    'calories' => $request->calories,
                    'water' => $request->water,
                    'steps' => $request->steps,
                    'meals_warm' => $request->meals_warm,
                    'meals_cold' => $request->meals_cold,
                    'is_cheat_day' => $request->is_cheat_day,
                    'took_alcohol' => $request->took_alcohol,
                    'took_fast_food' => $request->took_fast_food,
                    'took_sweets' => $request->took_sweets,
                    'points' => $points,
                ]);
            } catch (\Exception $e) {
                \Log::error('Error creating day record: ' . $e->getMessage());
                return redirect()->route('days.my')->with('error', 'Ein Fehler ist aufgetreten beim Speichern des Tages: ' . $e->getMessage());
            }

            // update user_monthlies
            $userMonthly->points_month += $points;
            $userMonthly->cheat_days_used += ($request->is_cheat_day);
            $userMonthly->save();

            $this->recalculateTotalPoints($request->user());

            // check if day is today
            $date = new Carbon($date);
            if ($date->isToday()) {
                return redirect()->route('dashboard')
                    ->with('status', 'day-updated');
            } else {
                return redirect()->route('days.my')
                    ->with('status', 'day-updated')
                    ->with('success', 'Der Tag ' .  date('d.m.Y', strtotime($date)) . ' wurde erfolgreich angelegt.');
            }
        });
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
    public function edit(string $id): View|RedirectResponse
    {
        $user = Auth::user();

        try {
            $day = $user->days()->findOrFail($id); // fail if the day does not belong to the user
        }
        catch (ModelNotFoundException) {
            return redirect()->route('days.my')->with('error', 'Der Tag konnte nicht gefunden werden.');
        }

        return view('days.edit', compact('day', 'user'));
    }

    /**
     * Update the specified resource in storage.
     * @throws Throwable
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        return DB::transaction(function () use ($request, $id) {
            // get the day
            try {
                $day = $request->user()->days()->findOrFail($id);
            } catch (ModelNotFoundException) {
                return redirect()->route('days.my')->with('error', 'Der Tag konnte nicht gefunden werden.');
            }

            $userMonthly = $request->user()->user_monthlies()->where('month', date('Y-m-01', strtotime($day->date)))->first();

            $request->merge([
                'is_cheat_day' => $request->has('is_cheat_day'),
                'took_alcohol' => $request->has('took_alcohol'),
                'took_fast_food' => $request->has('took_fast_food'),
                'took_sweets' => $request->has('took_sweets'),
            ]);

            $this->validateDay($request);

            // check if the user has a user_monthlies entry for the current month
            if (!$userMonthly) {
                $userMonthly = UserMonthly::create([
                    'user_id' => $request->user()->id,
                    'month' => date('Y-m-01', strtotime($day->date)),
                ]);
            }

            // previous values
            $oldPoints = $day->points;
            $wasCheatDay = $day->is_cheat_day;
            $oldMonthlyPoints = $userMonthly->points_month;

            $cheatDayDifference = ($request->is_cheat_day ? 1 : 0) - ($wasCheatDay ? 1 : 0);
            $newCheatDays = $userMonthly->cheat_days_used + $cheatDayDifference;

            if ($newCheatDays > Config::get('constants.max_cheat_days')) {
                return redirect()->route('days.my')->with('error', 'Du hast keine Cheat-Days mehr übrig: ' . $userMonthly->cheat_days_used . ' von ' . Config::get('constants.max_cheat_days') . ' verbraucht.');
            }

            if ($request->day_calorie_goal) {
                $dayCalorieGoal = $request->day_calorie_goal;
            } else if ($request->user()->user_stats->global_calorie_goal) {
                $dayCalorieGoal = $request->user()->user_stats->global_calorie_goal;
            } else {
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
                $request->is_cheat_day,
                $request->took_alcohol,
                $request->took_fast_food,
                $request->took_sweets
            );

            $pointsDifference = $points - $oldPoints;
            $newMonthlyPoints = $oldMonthlyPoints + $pointsDifference;

            // update day
            $day->weight = $request->weight;
            $day->training_duration = $request->training_duration;
            $day->day_calorie_goal = $dayCalorieGoal;
            $day->percentage_of_goal = $caloriePercentage;
            $day->calories = $request->calories;
            $day->water = $request->water;
            $day->steps = $request->steps;
            $day->meals_warm = $request->meals_warm;
            $day->meals_cold = $request->meals_cold;
            $day->is_cheat_day = $request->is_cheat_day;
            $day->took_alcohol = $request->took_alcohol;
            $day->took_fast_food = $request->took_fast_food;
            $day->took_sweets = $request->took_sweets;
            $day->points = $points;
            $day->save();

            // update user_monthlies
            $userMonthly->points_month = $newMonthlyPoints;
            $userMonthly->cheat_days_used = $newCheatDays;
            $userMonthly->save();

            $this->recalculateTotalPoints($request->user());

            // check if day is today
            if ($day->date->isToday()) {
                return redirect()->route('dashboard')
                    ->with('status', 'day-updated');
            } else {
                return redirect()->route('days.edit', $day->id)
                    ->with('status', 'day-updated');
            }
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = Auth::user();

        try {
            $day = $user->days()->findOrFail($id);
        } catch (ModelNotFoundException) {
            return redirect()->route('days.my')->with('error', 'Der Tag konnte nicht gefunden werden.');
        }

        $userMonthly = $user->user_monthlies()->where('month', date('Y-m-01', strtotime($day->date)))->first();

        $userMonthly->points_month -= $day->points;
        $userMonthly->cheat_days_used -= ($day->is_cheat_day ? 1 : 0);
        $userMonthly->save();

        $date = $day->date;

        $day->delete();

        $this->recalculateTotalPoints($user);

        return redirect()->route('days.my')->with('success', 'Der Tag ' . date('d.m.Y', strtotime($date)) . ' wurde gelöscht.');
    }

    public function calculatePoints(float $caloriePercent, int $trainingDuration, float $steps, float $water, int $warmMeals, int $coldMeals, bool $cheatDay, bool $tookAlcohol, bool $tookFastFood, bool $tookSweets): int
    {
        $points = 0;

        // calculate points for calorie percentage
        $caloriePoints = Config::get('constants.points.calorie_percentage');
        foreach ($caloriePoints as $point) {
            $threshold = $point[0];
            if (count($point) == 2 && $caloriePercent >= $threshold) {
                $points += $point[1];
                break;
            } elseif (count($point) == 1 && !$cheatDay) {
                $points += $point[0];
                break;
            }
        }

        // calculate points for training duration (2 points per 30 minutes), round down
        $trainingConfig = Config::get('constants.points.training_duration');
        $points += floor($trainingDuration / $trainingConfig['interval']) * $trainingConfig['points'];

        // calculate points for water intake
        $waterPoints = Config::get('constants.points.water');
        foreach ($waterPoints as $point) {
            $threshold = $point[0];
            if (count($point) == 2 && $water >= $threshold) {
                // when the water intake is higher than or equal to the threshold
                $points += $point[1];
                break;
            } elseif (count($point) == 1 && !$cheatDay) {
                // when it's the last (negative) point, and it's not a cheat day
                $points += $point[0];
                break;
            }
        }

        // calculate points for steps (1 point per 3km), round down
        $points += floor($steps / Config::get('constants.points.steps_per_point'));

        // calculate points for warm meals (2 points per meal)
        $points += $warmMeals * Config::get('constants.points.warm_meal_points');

        // calculate points for cold meals (1 point per meal)
        $points += $coldMeals * Config::get('constants.points.cold_meal_points');

        if (!$cheatDay) {
            // calculate points for alcohol
            if ($tookAlcohol) {
                $points += Config::get('constants.points.alcohol_points');
            }

            // calculate points for fast food
            if ($tookFastFood) {
                $points += Config::get('constants.points.fast_food_points');
            }

            // calculate points for sweets
            if ($tookSweets) {
                $points += Config::get('constants.points.sweets_points');
            }
        }

        return $points;
    }

    public function validateDay(Request $request): void
    {
        $request->validate([
            'day_calorie_goal' => ['nullable', 'numeric', 'integer', 'min:' . Config::get('constants.calorie_goal.min'), 'max:' . Config::get('constants.calorie_goal.max')],
            'calories' => ['required', 'numeric', 'integer', 'min:' . Config::get('constants.calorie_goal.min'), 'max:' . Config::get('constants.calorie_goal.max')],
            'meals_warm' => ['required', 'numeric', 'integer', 'min:' . Config::get('constants.meals.min'), 'max:' . Config::get('constants.meals.max')],
            'meals_cold' => ['required', 'numeric', 'integer', 'min:' . Config::get('constants.meals.min'), 'max:' . Config::get('constants.meals.max')],
            'water' => ['required', 'numeric', Rule::in(Config::get('constants.water.options'))],
            'training_duration' => ['required', 'numeric', 'integer', Rule::in(Config::get('constants.training_duration.options'))],
            'steps' => ['required', 'numeric', Rule::in(Config::get('constants.steps.options'))],
            'is_cheat_day' => ['required', 'boolean'],
            'took_alcohol' => ['required', 'boolean'],
            'took_fast_food' => ['required', 'boolean'],
            'took_sweets' => ['required', 'boolean'],
            'weight' => ['nullable', 'numeric', 'regex:/^\d+(\.\d{1})?$/', 'min:' . Config::get('constants.weight.min'), 'max:' . Config::get('constants.weight.max')],
        ]);
    }

    public function recalculateAllPoints(User $user): void
    {
        $userMonthlies = $user->user_monthlies()->get();

        foreach ($userMonthlies as $userMonthly) {
            $this->recalculateDaysMonthPoints($userMonthly);
            $this->recalculateMonthlyPoints($userMonthly);
        }

        $this->recalculateTotalPoints($user);
    }

    public function recalculateTotalPoints(User $user): void {
        $userMonthlies = $user->user_monthlies()->get();
        $user->user_stats->points_total = $userMonthlies->sum('points_month');
        $user->user_stats->save();
    }

    public function recalculateMonthlyPoints(UserMonthly $userMonthly): void
    {
        $points = 0;

        $days = $userMonthly->user->days()
            ->where('date', '>=', $userMonthly->month)
            ->where('date', '<', $userMonthly->month->addMonth())
            ->get();

        foreach ($days as $day) {
            $points += $day->points;
        }

        $userMonthly->points_month = $points;
        $userMonthly->save();
    }

    public function recalculateDaysMonthPoints(UserMonthly $userMonthly): void {
        $days = $userMonthly->user->days()
            ->where('date', '>=', $userMonthly->month)
            ->where('date', '<', $userMonthly->month->addMonth())
            ->get();

        foreach ($days as $day) {
            $day_points = $this->calculatePoints(
                $day->percentage_of_goal,
                $day->training_duration,
                $day->steps,
                $day->water,
                $day->meals_warm,
                $day->meals_cold,
                $day->is_cheat_day,
                $day->took_alcohol,
                $day->took_fast_food,
                $day->took_sweets
            );

            if ($day_points != $day->points) {
                $day->points = $day_points;
                $day->save();
            }
        }
    }
}
