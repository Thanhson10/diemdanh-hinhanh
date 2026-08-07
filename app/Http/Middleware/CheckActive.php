<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckActive
{
    public function handle($request, Closure $next)
    {
        if (Auth::guard('giangvien')->check()) {

            $user = Auth::guard('giangvien')->user()->fresh();

            if ($user->is_active == 0) {
                Auth::guard('giangvien')->logout();

                return redirect()->route('login')
                    ->with('error', 'Tài khoản đã bị khóa!');
            }
        }

        return $next($request);
    }
}
