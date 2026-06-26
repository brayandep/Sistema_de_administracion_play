<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserLoginLog;
class LoginController extends Controller
{
    /**
     * Mostrar el formulario de inicio de sesión.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Procesar el inicio de sesión.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate(
            [
                'username' => [
                    'required',
                    'string',
                    'max:50',
                ],
                'password' => [
                    'required',
                    'string',
                ],
            ],
            [
                'username.required' => 'Ingresa tu nombre de usuario.',
                'password.required' => 'Ingresa tu contraseña.',
            ]
        );

        $remember = $request->boolean('remember');

        $authenticated = Auth::attempt(
            [
                'username' => $credentials['username'],
                'password' => $credentials['password'],
                'active' => true,
            ],
            $remember
        );

        if ($authenticated) {
            $request->session()->regenerate();
            UserLoginLog::create([
    'user_id' => Auth::id(),
    'event' => 'login',
    'ip_address' => $request->ip(),
    'user_agent' => $request->userAgent(),
]);
            return redirect()
                ->intended(route('dashboard'))
                ->with('success', 'Bienvenido al sistema.');
        }

        return back()
            ->withErrors([
                'username' => 'El usuario o la contraseña son incorrectos.',
            ])
            ->onlyInput('username');
    }

    /**
     * Cerrar la sesión.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        if (Auth::check()) {
    UserLoginLog::create([
        'user_id' => Auth::id(),
        'event' => 'logout',
        'ip_address' => $request->ip(),
        'user_agent' => $request->userAgent(),
    ]);
}
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'La sesión se cerró correctamente.');
    }
}