<?php
// app/Http/Controllers/AuthController.php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Mostrar formulario de login
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Procesar el login
     */
    public function login(Request $request)
    {
        // Validar los datos enviados
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ], [
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El formato del email no es válido',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres'
        ]);

        // Intentar autenticar al usuario
        $credentials = $request->only('email', 'password');
        
        if (Auth::attempt($credentials)) {
            $user = auth()->user();
            
            // Verificar si el usuario está activo
            if (!$user->active) {
                Auth::logout();
                return back()->with('error', 'Tu cuenta está desactivada. Contacta al administrador.');
            }
            
            // Regenerar la sesión por seguridad
            $request->session()->regenerate();
            
            // Redirigir según el rol
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard')->with('success', 'Bienvenido, ' . $user->name);
            } else {
                return redirect()->route('ventas.index')->with('success', 'Bienvenido, ' . $user->name);
            }
        }

        // Si las credenciales son incorrectas
        return back()->with('error', 'Email o contraseña incorrectos')->withInput();
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login')->with('success', 'Sesión cerrada correctamente');
    }

    /**
     * Mostrar formulario de registro (solo para admins)
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Procesar el registro de nuevos empleados
     */
    public function register(Request $request)
    {
        // Validar los datos
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|in:admin,empleado'
        ]);

        // Crear el nuevo usuario
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'active' => true
        ]);

        return redirect()->route('admin.empleados')->with('success', 'Empleado registrado correctamente');
    }
}