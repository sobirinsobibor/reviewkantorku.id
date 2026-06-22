<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Laravel\Socialite\Facades\Socialite;
use Faker\Factory;

class LoginController extends Controller
{
    public function login_public(){
        return redirect('/');
    }

    public function google_redirect(){
        return Socialite::driver('google')->redirect();
    }

    public function google_callback(){
        $googleUser = Socialite::driver('google')->user();
        $user = User::whereEmail($googleUser->email)->first();
        
        if (!$user) {
            $faker = Factory::create();

            do {
                $username = Str::slug($faker->words(2, true), '-');
                $username .= '-' . rand(10, 999);
            } while (User::where('username', $username)->exists());

            $user = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'username' => strtolower($username),
                'is_admin' => 0,
                'is_active' => 1
            ]);

            Auth::login($user); // login dulu sebelum redirect
            return redirect('/my-profile'); // langsung redirect, stop di sini
        }
        
        if($user->is_active == 0){
            return 'akun tidak aktif';
        }

        Auth::login($user);
        return redirect('/');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return Inertia::location('/'); // ← ganti ini
    }
}
