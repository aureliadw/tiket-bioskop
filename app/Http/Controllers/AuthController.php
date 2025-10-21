<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validation
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 6 karakter'
        ]);

        $credentials = $request->only('email', 'password');

        // Attempt login with remember me option
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            // Regenerate session for security
            $request->session()->regenerate();

            $user = Auth::user();

            // Check if user is active
            if (!$user->status_aktif) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Akun Anda tidak aktif. Hubungi administrator.',
                ])->onlyInput('email');
            }

            // Welcome message
            $welcomeMessage = "Selamat datang, {$user->nama_lengkap}!";

            // Redirect based on role
            switch ($user->role) {
                case 'admin':
                    return redirect()->route('admin.dashboard')
                        ->with('success', $welcomeMessage);
                    
                case 'kasir':
                    return redirect()->route('kasir.dashboard')
                        ->with('success', $welcomeMessage);
                    
                case 'owner':
                    return redirect()->route('owner.dashboard')
                        ->with('success', $welcomeMessage);
                    
                case 'pelanggan':
                default:
                    return redirect()->intended(route('home'))
                        ->with('success', $welcomeMessage);
            }
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    /**
     * Tampilkan halaman register
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Proses register
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20|regex:/^[0-9+\-() ]+$/',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi',
            'nama_lengkap.max' => 'Nama lengkap maksimal 255 karakter',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'phone.regex' => 'Format nomor telepon tidak valid',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        // Create new user
        $user = User::create([
            'nama_lengkap' => $validated['nama_lengkap'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => 'pelanggan',
            'status_aktif' => true,
        ]);

        // Auto login after registration
        Auth::login($user);

        return redirect()->route('home')
            ->with('success', "Selamat datang, {$user->nama_lengkap}! Akun Anda berhasil dibuat.");
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil logout.');
    }
}
