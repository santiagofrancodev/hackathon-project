<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            Log::error('Google OAuth callback failed', ['error' => $e->getMessage()]);

            return redirect()->route('login')
                ->withErrors(['email' => 'No se pudo iniciar sesión con Google. Intente nuevamente.']);
        }

        // Find or create user
        $user = User::where('email', $googleUser->getEmail())->first();

        if (! $user) {
            // Register new user
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'password' => Hash::make(uniqid()),
                'provider' => 'google',
                'provider_id' => $googleUser->getId(),
            ]);

            $user->markEmailAsVerified();
        } else {
            // Update provider info for existing user
            $user->update([
                'provider' => 'google',
                'provider_id' => $googleUser->getId(),
            ]);
        }

        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('success', 'Inicio de sesión exitoso.');
    }
}
