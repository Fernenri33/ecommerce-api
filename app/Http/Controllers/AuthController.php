<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\CartService;
use DB;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            // Primero normalizar y hashear el email para validar
            $emailToValidate = $request->input('email');
            $normalized = Str::lower(trim($emailToValidate));
            $hash = hash_hmac('sha256', $normalized, config('app.key'));

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => [
                    'required',
                    'email',
                    function ($attribute, $value, $fail) use ($hash) {
                        if (User::where('email_hash', $hash)->exists()) {
                            $fail('El email ya está registrado');
                        }
                    }
                ],
                'password' => 'required|string|min:8|confirmed',
            ], [
                'name.required' => 'El nombre es obligatorio',
                'last_name.required' => 'El apellido es obligatorio',
                'email.required' => 'El email es obligatorio',
                'email.email' => 'El email debe ser válido',
                'password.required' => 'La contraseña es obligatoria',
                'password.min' => 'La contraseña debe tener al menos 8 caracteres',
                'password.confirmed' => 'Las contraseñas no coinciden',
            ]);

        [$user, $cart] = DB::transaction(function () use ($validated, $hash) {
            $user = User::create([
                'name'       => $validated['name'],
                'last_name'  => $validated['last_name'],
                'email'      => $validated['email'],
                'email_hash' => $hash,
                'rol_id'     => 2,
                'password'   => Hash::make($validated['password']),
            ]);

            // Garantiza UN solo carrito activo por usuario (crea si no existe)
            $cart = app(CartService::class)->getOrCreateActiveCart($user->id);

            return [$user, $cart];
        });

            $token = $user->createToken(
                'auth_token',
                ['*'],
                now()->addDays(7)
            )->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Usuario registrado exitosamente',
                'content' => [
                    'rol' => $user->rol->name,
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'expires_in' => 7 * 24 * 60 * 60,
                ]
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function login(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ], [
                'email.required' => 'El email es obligatorio',
                'email.email' => 'El email debe ser válido',
                'password.required' => 'La contraseña es obligatoria',
            ]);

            $normalized = Str::lower(trim($validated['email']));
            $hash = hash_hmac('sha256', $normalized, config('app.key'));

            $user = User::where('email_hash', $hash)->first();

            if (!$user || !Hash::check($validated['password'], $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Credenciales incorrectas'
                ], 401);
            }

            $user->tokens()->delete();

            $token = $user->createToken(
                'auth_token',
                ['*'],
                now()->addDays(7)
            )->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login exitoso',
                'content' => [
                    'rol' => $user->rol->name,
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'expires_in' => 7 * 24 * 60 * 60,
                ]
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }
    public function logout(Request $request){
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autenticado'
                ], 401);
            }

            // Revoca el token usado en la petición (Sanctum)
            $current = $user->currentAccessToken();
            if ($current) {
                $current->delete();
            } else {
                // fallback: eliminar todos los tokens del usuario
                $user->tokens()->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Logout exitoso'
            ], 200);
        } catch (\Throwable $e) {
            \Log::error('Error en logout', ['err' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error al cerrar sesión'
            ], 500);
        }
    }
}
