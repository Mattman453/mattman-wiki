<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use MongoDB\Driver\Exception\BulkWriteException;
use MongoDB\Laravel\Auth\User as Authenticatable;

class AuthController extends Controller
{
    /**
     * Try to login the user. if successful, regenerate the session and allow the user into sections that require being logged in.
     * Should be sent as POST
     * 
     * @param Request $request info containing the email and password.
     * @return JsonResponse either a redirection to intended page on success or a 400 HTTP error showing user value error.
     */
    public function login(Request $request) : JsonResponse {
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

    /**
     * Logs a user out, invalidates all info stored in their session and regenerates their csrf token. Should be sent as POST.
     * 
     * @param Request $request info allowing the logout, invalidation, and regeneration of the token. Should be included with any request sent to server.
     * @return JsonResponse redirect to either the intended url if set or home if not.
     */
    public function logout(Request $request) : JsonResponse {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json([
            'redirect' => session()->get('url.intended', route('game.home')),
        ], 302);
    }

    /**
     * Create a new user and send out an email to verify their account. Should be sent as a POST request.
     * Fails if credentials already exist, are not within set parameters, or something catastrophic happens.
     * 
     * @param Request $request carrier of information to attempt registration with.
     * @return JsonResponse redirection to verification page on success or a message containing information on error.
     */
    public function register(Request $request) : JsonResponse {
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

    /**
     * Should be accessed from email sent to user. Should be a POST request.
     * 
     * @param EmailVerificationRequest $request specific request containing info to allow server to verify email exists.
     * @return RedirectResponse redirect to home page.
     */
    public function verifyEmail(EmailVerificationRequest $request) : RedirectResponse {
        try {
            $request->fulfill();
            unset(Auth::user()->email_verification_sent_at);
            Auth::user()->save();
        } catch (\Exception $exception) {
            Log::error($exception);
        }
        return redirect(route('game.home'));
    }

    /**
     * Allows user to request a new email be sent for verification. Should be a POST request.
     * 
     * @param Request $request contains info on the user being verified.
     * @return JsonResponse redirection if already verified, info if email sent successfully,
     *      or error if either not enough time has passed or the server cannot perform an action.
     */
    public function sendEmailVerification(Request $request) : JsonResponse {
        try {
            if (Auth::user()->hasVerifiedEmail()) {
                Log::debug("Already verified.");
                return response()->json([
                    'success' => 'Email already verified, redirecting...',
                    'redirect' => route('game.home'),
                ], 302);
            }
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

    /**
     * Actual function to send the email. Attempts to see if last time sent was in the past and send if true.
     * 
     * @param Authenticatable $user the user who wants to send a new verification email.
     * @return bool returns true if able to send a new email or false otherwise.
     */
    private function sendVerificationNotice(Authenticatable $user) : bool {
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

    /**
     * Get the view for the Login page. Should be a GET request.
     * 
     * @return Response The Login view if a user is not logged in.
     * @return RedirectResponse a redirect to either the home page if the email is verified or the verification page if not verified.
     */
    public function getLoginView() : Response | RedirectResponse {
        if (Auth::check()) {
            if (Auth::user()->hasVerifiedEmail()) {
                return redirect(route('game.home'));
            }
            return redirect(route('verification.notice'));
        }

        return Inertia::render('Auth/Login', [
            'login' => true,
        ]);
    }

    /**
     * Get the view for sending new verification emails. Should be a GET request.
     * 
     * @return Response The view for sending if the email is not verified.
     * @return RedirectResponse the redirection to home if the email is already verified.
     */
    public function getVerifyEmailView() : Response | RedirectResponse {
        if (Auth::user()->hasVerifiedEmail()) return redirect(route('game.home'));
        return Inertia::render('Auth/Verify', [
            'verificationSentAt' => Auth::user()->email_verification_sent_at,
            'throttle' => config('auth.email_verification.throttle', 120),
        ]);
    }
}
