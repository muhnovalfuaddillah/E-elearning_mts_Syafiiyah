<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function loginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string'],
            'password' => ['required', 'min:4'],
        ], [
            'email.required' => 'Email atau NIS wajib diisi',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 4 karakter'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $loginInput = $request->email;

        // Cek jika input login bukan format email, maka asumsikan sebagai NIS
        if (!filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
            $loginInput = $loginInput . '@syafiiyah.sch.id';
        }

        $credentials = [
            'email' => $loginInput,
            'password' => $request->password
        ];

        if (Auth::attempt($credentials, $request->remember ?? false)) {

            $request->session()->regenerate();

            // Redirect berdasarkan role
            $redirectUrl = $this->getRedirectUrlByRole(Auth::user()->role);

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil!',
                'redirect' => $redirectUrl
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Email/NIS atau password salah.'
        ], 401);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    private function getRedirectUrlByRole($role)
    {
        switch ($role) {
            case 'admin':
                return route('admin.dashboard');
            case 'guru':
                return route('guru.dashboard');
            case 'siswa':
                return route('siswa.dashboard');
            default:
                return route('login');
        }
    }
}