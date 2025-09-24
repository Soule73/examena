<?php

namespace App\Http\Controllers\Auth;

use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Helpers\PermissionHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\HasFlashMessages;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\RateLimiter;
use App\Http\Requests\Admin\EditUserRequest;
use App\Services\Admin\UserManagementService;
use Illuminate\Validation\ValidationException;

/**
 * Class LoginController
 *
 * Handles user authentication logic, including login and logout functionality.
 *
 * @package App\Http\Controllers\Auth
 */
class LoginController extends Controller
{
    use HasFlashMessages;

    public function __construct(
        public readonly UserManagementService $userService
    ) {}

    /**
     * Display the login form view to the user.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return Inertia::render('Auth/Login');
    }

    /**
     * Handle an authentication attempt.
     *
     * Validates the login request and attempts to authenticate the user using the provided credentials.
     * On successful authentication, redirects the user to their intended destination.
     * On failure, redirects back with input and error messages.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request  The validated login request instance containing user credentials.
     * @return \Illuminate\Http\RedirectResponse  Redirect response to the intended location or back to the login form with errors.
     */
    public function login(LoginRequest $request): \Illuminate\Http\RedirectResponse
    {
        $this->ensureIsNotRateLimited($request);

        $data = $request->validated();

        $credentials = [
            'email' => $data['email'],
            'password' => $data['password'],
        ];
        $remember = $data['remember'] ?? false;

        if (Auth::attempt($credentials, $remember)) {

            /** @var \Illuminate\Http\Request $request */
            $request->session()->regenerate();

            RateLimiter::clear($this->throttleKey($request));

            return redirect()->intended(PermissionHelper::getDashboardRoute());
        }

        RateLimiter::hit($this->throttleKey($request));

        throw ValidationException::withMessages([
            'email' => 'Ces identifiants ne correspondent à aucun de nos enregistrements.',
        ]);
    }

    public function profile(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            abort(401, 'Utilisateur non authentifié');
        }

        return Inertia::render('Auth/Profile', [
            'user' => $user->load('roles', 'permissions'),
        ]);
    }

    public function editProfile(EditUserRequest $request)
    {
        try {
            $user = Auth::user();
            $data = $request->validated();

            $this->userService->update($user, $data);

            return $this->flashSuccess('Profil mis à jour avec succès.');
        } catch (\Exception $e) {
            return $this->flashError('error', "Erreur lors de la mise à jour du profil. " . $e->getMessage());
        }
    }

    /**
     * Log the user out of the application.
     *
     * This method invalidates the user's session and regenerates the session token
     * to prevent session fixation. It also performs any additional logout logic
     * required by the application.
     *
     * @param \Illuminate\Http\Request $request The current HTTP request instance.
     * @return \Illuminate\Http\RedirectResponse Redirect response after logout.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('welcome');
    }

    /**
     * Ensure that the login request is not rate limited.
     *
     * This method checks if the incoming authentication request has exceeded the allowed number of attempts.
     * If the request is rate limited, it throws a validation exception with an appropriate error message.
     *
     * @param \Illuminate\Http\Request $request The current HTTP request instance.
     *
     * @throws \Illuminate\Validation\ValidationException If the request is rate limited.
     *
     * @return void
     */
    public function ensureIsNotRateLimited(Request $request)
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'email' => 'Trop de tentatives de connexion. Veuillez réessayer dans ' . ceil($seconds / 60) . ' minutes.',
        ]);
    }

    /**
     * Generate a unique throttle key for the login attempt.
     *
     * This key is typically used to rate limit login attempts based on the user's
     * credentials and request information, such as IP address or email.
     *
     * @param \Illuminate\Http\Request $request The current HTTP request instance.
     * @return string The generated throttle key for the request.
     */
    public function throttleKey(Request $request)
    {
        return Str::transliterate(Str::lower($request->input('email')) . '|' . $request->ip());
    }
}
