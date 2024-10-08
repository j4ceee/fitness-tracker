<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaderboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $lb_array = [];

        $users = User::all()->sortBy('name');

        foreach ($users as $user) {
            $user_stats = $user->user_stats;

            // get current user_monthlies
            $user_monthlies = $user->user_monthlies()->where('month', date('Y-m-01'))->first();

            // get today
            $today = $user->days()->where('date', date('Y-m-d'))->first();

            $lb_array[] = [
                'rank' => "0",
                'name' => $user->name,
                'total_score' => $user_stats->points_total,
                'month_score' => $user_monthlies ? $user_monthlies->points_month : "0",
                'day_score' => $today ? $today->points : "0",
                'days_index_url' => route('days.index', ['userId' => $user->id]),
            ];
        }

        $lb_array = json_encode($lb_array);

        return view('leaderboard', compact('lb_array'));
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
    public function store(Request $request)
    {
        //
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
}
