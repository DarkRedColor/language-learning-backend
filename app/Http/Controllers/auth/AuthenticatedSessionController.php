<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SignInRequest;
use App\Models\User;
use Google\Client;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return View
     */
    public function create()
    {
        return view('auth.sign-in');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param SignInRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(SignInRequest $request)
    {
        $client = new Client([
            'client_id' => config('values.google_oauth_client_id'),
            'client_secret' => config('values.google_oauth_secret_key')
        ]);

        $redirectUri = $request->header('Origin') ?? $request->header('referer');
        $client->setRedirectUri($redirectUri);
        $token = $client->fetchAccessTokenWithAuthCode($request->code);
        if (!isset($token['id_token'])) {
            return response()->json(['message' => 'Invalid code', 'success' => false], 403);
        }
        $idToken = $token['id_token'];
        $payload = $client->verifyIdToken($idToken);
        $rememberToken = \Str::random(16);
        if ($payload) {
            $data = [
                'email' => $payload['email'],
                'name' => $payload['given_name'] ?? 'Unknown',
                'language_set' => 1,
                'remember_token' => $rememberToken,
                'password' => Hash::make(\Str::random(16))
            ];
            $user = User::where('email', $payload['email'])->first();
            if (!$user) {
                $user = User::create($data);
                $user->assignRole("guest");
                event(new Registered($user));
            }
            auth()->login($user);
        }

        $request->session()->regenerate();

        return response()->json(['redirectUri' => RouteServiceProvider::HOME, 'success' => true]);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
