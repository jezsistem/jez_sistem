<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class GoogleController extends Controller
{
    /**
     * Redirect user to Google OAuth
     */
    public function redirectToGoogle()
    {
        try {
            return Socialite::driver('google')->redirect();
        } catch (\Exception $e) {
            Log::error('Google redirect error: ' . $e->getMessage());
            return redirect('/login')->with('error', 'Unable to connect to Google. Please try again.');
        }
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            Log::info('Google login attempt', [
                'email' => $googleUser->email,
                'google_id' => $googleUser->id,
                'name' => $googleUser->name
            ]);
            
            // 1. Check if user exists by Google ID
            $userByGoogleId = User::where('google_id', $googleUser->id)->first();
            
            if ($userByGoogleId) {
                // User already exists with this Google ID
                Log::info('User found by Google ID', ['user_id' => $userByGoogleId->id]);
                Auth::login($userByGoogleId);
                return redirect()->intended('/dashboard');
            }
            
            // 2. Check if user exists by email
            $userByEmail = User::where('email', $googleUser->email)->first();
            
            if ($userByEmail) {
                // User exists with same email, link Google account
                Log::info('Linking existing user with Google', ['user_id' => $userByEmail->id]);
                
                $userByEmail->update([
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                    'provider' => 'both', // User can login with both methods
                    'google_linked' => true,
                    'google_linked_at' => now(),
                ]);
                
                Auth::login($userByEmail);
                return redirect()->intended('/dashboard')->with('success', 'Your account has been linked with Google successfully!');
            }
            
            // 3. Create new user
            Log::info('Creating new user from Google', ['email' => $googleUser->email]);
            
            $newUser = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'google_id' => $googleUser->id,
                'avatar' => $googleUser->avatar,
                'provider' => 'google',
                'password' => Hash::make(Str::random(16)), // Random password for security
                'email_verified_at' => now(), // Google emails are verified
                'google_linked' => true,
                'google_linked_at' => now(),
            ]);
            
            Auth::login($newUser);
            return redirect()->intended('/dashboard')->with('success', 'Welcome! Your account has been created successfully.');
            
        } catch (\Exception $e) {
            Log::error('Google login error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect('/login')->with('error', 'Google login failed. Please try again or use your email and password.');
        }
    }

    /**
     * Link existing account with Google
     */
    public function linkGoogleAccount(Request $request)
    {
        try {
            if (!Auth::check()) {
                return redirect('/login');
            }
            
            $user = Auth::user();
            
            if ($user->google_id) {
                return back()->with('info', 'Your account is already linked with Google.');
            }
            
            // Redirect to Google OAuth for linking
            return Socialite::driver('google')
                ->with(['prompt' => 'select_account'])
                ->redirect();
                
        } catch (\Exception $e) {
            Log::error('Google link error: ' . $e->getMessage());
            return back()->with('error', 'Unable to link Google account. Please try again.');
        }
    }

    /**
     * Unlink Google account
     */
    public function unlinkGoogleAccount(Request $request)
    {
        try {
            if (!Auth::check()) {
                return redirect('/login');
            }
            
            $user = Auth::user();
            
            if (!$user->google_id) {
                return back()->with('info', 'Your account is not linked with Google.');
            }
            
            // Check if user has local password
            if (!$user->password || $user->password === '') {
                return back()->with('error', 'Cannot unlink Google account. Please set a password first.');
            }
            
            $user->update([
                'google_id' => null,
                'avatar' => null,
                'provider' => 'local',
                'google_linked' => false,
                'google_linked_at' => null,
            ]);
            
            return back()->with('success', 'Google account unlinked successfully.');
            
        } catch (\Exception $e) {
            Log::error('Google unlink error: ' . $e->getMessage());
            return back()->with('error', 'Unable to unlink Google account. Please try again.');
        }
    }
}
