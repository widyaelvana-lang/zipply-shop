<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman form login
     */
    public function showLogin()
    {
        // Mengarah ke file di resources/views/auth/login.blade.php
        return view('auth.login'); 
    }

    /**
     * Memproses data dari form login
     */
    public function login(Request $request)
    {
        // Validasi data input dari form
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // MENCOBA PROSES OTENTIKASI
        try {
            // 1. Cari data user berdasarkan email yang diinput
            $user = \App\Models\User::where('email', $credentials['email'])->first();

            if ($user) {
                // KONDISI A: Jika password di database berupa teks biasa atau MD5
                if ($user->password === $credentials['password'] || $user->password === md5($credentials['password'])) {
                    Auth::login($user);
                    $request->session()->regenerate();
                    return redirect()->intended('/admin/dashboard');
                }

                // KONDISI B: Jika password di database sudah berupa Bcrypt (enkripsi bawaan Laravel)
                if (\Illuminate\Support\Facades\Hash::check($credentials['password'], $user->password)) {
                    Auth::login($user);
                    $request->session()->regenerate();
                    return redirect()->intended('/admin/dashboard');
                }
            }
            
            // Jika password/email salah tapi tidak ada error sistem, lempar pesan gagal
            return back()->with('error', 'Email atau password yang Anda masukkan salah.');

        } catch (\Exception $e) {
            // BLOK CATCH WAJIB ADA: Menangkap jika ada error database / model User rusak
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    /**
     * Fungsi untuk Log out / Keluar dari sistem
     */
    public function logout(Request $request) 
    {
        // 1. Keluarkan pengguna dari autentikasi Laravel
        Auth::logout();

        // 2. Hancurkan sesi (session) yang tersimpan di browser
        $request->session()->invalidate();

        // 3. Buat ulang token CSRF baru agar aman untuk login berikutnya
        $request->session()->regenerateToken();

        // 4. Kembali ke halaman login utama dengan aman menggunakan alias name
        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar dari sistem.');
    }
}