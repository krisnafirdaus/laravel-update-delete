<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use App\Exceptions\Handler;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;
        // $token = $user->createToken('full-access')->plainTextToken;
        // $token = $user->createToken('read-only',. \\....)->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Berhasil Registrasi',
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
            ]
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
         $validated = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password wajib diisi',
        ]);

          $user = User::where('email', $validated['email'])->first();

          if(!$user || !Hash::check($validated['password'], $user->password)){
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah'
            ], 404);
          }

          $token = $user->createToken('auth_token')->plainTextToken;

           return response()->json([
            'success' => true,
            'message' => 'Berhasil Registrasi',
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
            ]
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil logout'
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        if($request->user() === null){
            return response()->json([
                'success' => false,
                'message' => 'Kamu tidak terautenikasi'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data User',
            'data' => $request->user()
        ]);
    }
}
