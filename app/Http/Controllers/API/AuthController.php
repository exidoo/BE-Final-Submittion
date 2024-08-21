<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\Request;
use App\Models\Roles;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function register(RegisterRequest $request)
    {
        // Ambil role 'user' dari tabel roles
        $roleUser = Roles::where('name', 'user')->first();

        if (!$roleUser) {
            return response()->json(['error' => 'Role user tidak ditemukan'], 404);
        }

        // Buat user baru
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $roleUser->id,
        ]);

        // Buat token JWT
        $token = JWTAuth::fromUser($user);

        return response()->json([
            "message" => "Register berhasil!",
            "user" => $user,
            "token" => $token,
        ]);
    }

    /**
     * Login.
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Login gagal, email atau password salah'], 401);
        }

        // Load the authenticated user with the role relation
        $user = User::with('role')->where('email', $request['email'])->first();

        return response()->json([
            "message" => "Login berhasil!",
            "token" => $token,
            "user" => $user,
        ]);
    }

    /**
     * Current User.
     */
    public function getUser()
    {
        $currentUser = auth()->user()->with('role', 'profile', 'borrows')->find(auth()->user()->id);
        return response()->json([
            "message" => "Berhasil get user",
            "user" => $currentUser,
        ]);
    }

    /**
     * Logout.
     */
    public function logout()
    {
        auth()->logout();

        return response()->json([
            "message" => "Logout berhasil!",
        ]);
    }
}
