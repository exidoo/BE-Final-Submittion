<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class isAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        $userAdmin = User::where('name', 'admin')->first();
        if ($user && $user->role && $user->role->name === 'admin' || $user->role->name === 'owner') {
            return $next($request);
        }

        return response()->json([
            "message" => "Anda tidak bisa mengakses halaman admin"
        ], 401);
    }
    }

