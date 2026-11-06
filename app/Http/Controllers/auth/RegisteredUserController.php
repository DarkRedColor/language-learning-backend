<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\StatusUpdate;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * @deprecated
 */
class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return View
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @param Request $request
     * @return RedirectResponse
     *
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        Password::defaults(function () {
            return Password::min(8)
                ->mixedCase()
                ->numbers()
                ->uncompromised();
        });

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email:rfc,dns|max:255|unique:users',
            'password' =>['required', Password::defaults(),'required_with:repeat-password',' same:repeat-password'],//'required|string|required_with:repeat-password|same:repeat-password|min:8|max:30',
            'repeat-password' => 'required|string',
        ]);
        $token = \Str::random(16);


        Auth::login($user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'language_set' => 1,
            'remember_token' => $token,
        ]));


        $user->assignRole("guest");
        event(new Registered($user));

//        $this->emailSend($user);

        return redirect("/");
    }

    public function emailConfirmation($validate)
    {

        User::where('remember_token', $validate)->firstOrFail()->update([
            'email_verified_at' => now(),
            'remember_token' => null,
        ]);

        return redirect(route('season-index'));
    }
    public function emailSend ($user) {
        $details = [
            'greeting' => 'Hi '.$user->name,
            'body' => 'use this activation code for verify your email address',
            'thanks' => 'thank you',
            'token' => $user->remember_token,
        ];

        Notification::send($user, new StatusUpdate($details));
    }
}
