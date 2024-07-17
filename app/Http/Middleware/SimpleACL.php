<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class SimpleACL
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $role = Role::where('id', $user->role_id)->first();

            if ($user->role_id == config('app.roles.root')) {
                return $next($request);
            }
        }
        $prefix = Route::getCurrentRoute()->action['prefix'];
        $permissions = [];
        if (strpos($prefix, 'admin') !== false) {
            if (!Auth::check()) return redirect()->route('login');

            if (!empty($role->can)) {
                $permissions = json_decode($role->can, true);
            } else {
                abort(401);
            }
            $namespace = 'App\Http\Controllers\Admin';
            $controllerAndAction = explode('@', str_replace($namespace . '\\', '', Route::getCurrentRoute()->action['controller']));
            if (isset($permissions[$controllerAndAction[0]])) {
                if (in_array($controllerAndAction[1], $permissions[$controllerAndAction[0]])) {
                    return $next($request);
                }
            }

            abort(401);
        }

        return $next($request);
    }
}
