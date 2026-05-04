<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;

class AuthController extends Controller
{
    private static array $users = [
        [
            'id' => 1,
            'name' => 'Sakura no Uta',
            'email' => 'readsnu@absensi.com',
            'password' => 'password123',
        ],
        [
            'id' => 2,
            'name' => 'Summer Pockets',
            'email' => 'readsumpock@absensi.com',
            'password' => 'password123',
        ],
    ];

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $emails = collect(self::$users)->pluck('email')->toArray();
        if (count($emails) !== count(array_unique($emails))) {
            return response()->json([
                'message' => 'Konfigurasi pengguna tidak valid: terdapat duplikasi email.',
            ], 500);
        }

        $user = collect(self::$users)->firstWhere('email', $request->email);

        if (!$user || $user['password'] !== $request->password) {
            return response()->json([
                'message' => 'Email atau password salah.',
            ], 401);
        }

        $userModel = new User();
        $userModel->id = $user['id'];
        $userModel->name = $user['name'];
        $userModel->email = $user['email'];

        $token = JWTAuth::fromUser($userModel);

        return response()->json([
            'message' => 'Login berhasil.',
            'token' => $token,
        ], 200);
    }
}