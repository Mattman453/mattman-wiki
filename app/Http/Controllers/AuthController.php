<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use MongoDB\Driver\Exception\BulkWriteException;
use MongoDB\Laravel\Auth\User as Authenticatable;

class AuthController extends Controller
{
    public function login(Request $request) {
        try {
            $credentials = $request->validate([
                'email' => ['required' => 'email'],
                'password' => ['required', 'string'],
            ]);
        } catch (ValidationException $exception) {
            return response()->json([
                'error' => 'Email or Password is incorrect or missing. Please try again.',
            ], 400);
        }

        $credentials['email'] = strtolower($credentials['email']);
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return response()->json([
                'redirect' => session()->get(
                    'url.intended',
                    Auth::user()->hasVerifiedEmail() ? route('game.home') : route('verification.notice')
                ),
            ], 302);
        }

        return response()->json([
            'error' => 'The provided credentials do not match our records.',
        ], 400);
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json([
            'redirect' => session()->get('url.intended', route('game.home')),
        ], 302);
    }

    public function register(Request $request) {
        try {
            $newCredentials = $request->validate([
                'email' => [ 'required', 'email', 'min:3', 'max:255' ],
                'password' => [ 'required', 'string', 'min:8', 'max:30' ],
            ]);
            $newCredentials['email'] = strtolower($newCredentials['email']);
            $user = User::create($newCredentials);
            Auth::login($user);
            event(new Registered($user));
            Auth::user()->email_verification_sent_at = Carbon::now()->valueOf();
            Auth::user()->sendEmailVerificationNotification();
            Auth::user()->save();

            return response()->json([
                'redirect' => route('verification.notice'),
            ], 302);
        } catch (ValidationException $exception) {
            return response()->json([
                'error' => 'Email or Password is not valid. Please try again.',
            ], 400);
        } catch (BulkWriteException $exception) {
            return response()->json([
                'error' => 'The email used for registration already exists.',
            ], 400);
        } catch (\Exception $exception) {
            return response()->json([
                'error' => 'Something went wrong!',
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    public function verifyEmail(EmailVerificationRequest $request) {
        try {
            $request->fulfill();
            unset(Auth::user()->email_verification_sent_at);
            Auth::user()->save();
        } catch (\Exception $exception) {
            Log::error($exception);
        }
        return redirect(route('game.home'));
    }

    public function sendEmailVerification(Request $request) {
        try {
            Log::debug("Verifying email...");
            if (Auth::user()->hasVerifiedEmail()) {
                Log::debug("Already verified.");
                return response()->json([
                    'success' => 'Email already verified, redirecting...',
                    'redirect' => route('game.home'),
                ]);
            }
            Log::debug("Sending Email...");
            return ($this->sendVerificationNotice($request->user())) ?
                response()->json([
                    'success' => 'New verification link sent!',
                    'verificationsentAt' => Auth::user()->email_verification_sent_at,
                ], 200) :
                response()->json([
                    'error' => 'Please wait before requesting another verification link.',
                ], 400);
        } catch (\Exception $exception) {
            return response()->json([
                'error' => 'Something went wrong!',
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    private function sendVerificationNotice(Authenticatable $user) {
        Log::debug("Checking whether to send.");
        $nextAvailableVerification = ($user->email_verification_sent_at != null) ?
            Carbon::createFromTimestampMs($user->email_verification_sent_at)->addSeconds(config('auth.email_verification.throttle')) :
            Carbon::now();

        if ($nextAvailableVerification->isPast()) {
            $user->sendEmailVerificationNotification();
            $user->email_verification_sent_at = Carbon::now()->valueOf();
            $user->save();
            return true;
        }
        return false;
    }

    public function getLoginView(Request $request) {
        if (Auth::check()) {
            if (Auth::user()->hasVerifiedEmail()) {
                return redirect(route('game.home'));
            }
            return redirect(route('verification.notice'));
        }

        return Inertia::render('Auth/Login');
    }

    public function getRegisterView(Request $request) {
        if (Auth::check()) {
            if (Auth::user()->hasVerifiedEmail()) {
                return redirect(route('game.home'));
            }
            return redirect(route('verification.notice'));
        }

        return Inertia::render('Auth/Register');
    }

    public function getVerifyEmailView(Request $request) {
        if (Auth::user()->hasVerifiedEmail()) return redirect(route('game.home'));
        return Inertia::render('Auth/Verify');
    }
}
