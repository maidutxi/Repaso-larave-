<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
   
        public function register(Request $request)
    {
        // Validación
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Crear usuario
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('Repaso')->plainTextToken;

        return response()->json([
            'message' => 'Usuario registrado correctamente',
            'token' => $token,
        ], 201);
    
    }

    public function login(Request $request)
    {
        // Validar credenciales
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Crear token
            $token = $user->createToken('NombreDeTuAplicación')->plainTextToken;

            return response()->json([
                'message' => 'Inicio de sesión exitoso',
                'token' => $token,
            ]);
        }

        return response()->json(['message' => 'Credenciales incorrectas'], 401);
    }

    // Cerrar sesión
    public function logout(Request $request)
    {
        // Revocar el token del usuario actual
        $request->user()->tokens->each(function ($token) {
            $token->delete();
        });

        return response()->json(['message' => 'Sesión cerrada correctamente']);
    }
}
