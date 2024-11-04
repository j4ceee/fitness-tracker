<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class GroupMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user_id = $request->route()->parameter('userId');
        $user = User::find($user_id);

        if ($user->user_stats->group_code == null || $user->user_stats->group_code == Auth::user()->user_stats->group_code) {
            return $next($request);
        }

        return redirect()->route('dashboard'); // redirect to the dashboard if the user is not in the same group
    }
}
