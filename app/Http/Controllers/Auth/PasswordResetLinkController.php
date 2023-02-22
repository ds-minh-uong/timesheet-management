<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ResetPassword;
use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        if (!User::where('email', $request->email)->first()) {
            return back()->withErrors([
                'email' => 'This email has not been registered',
            ]);
        }

        $token = Str::random(32);
        PasswordReset::insert([
            'email' => $request->email,
            'token' => $token
        ]);

//        $resetUrl = url( '/reset-password'. '?token=' . $token);
        $resetUrl = URL::temporarySignedRoute('password.reset', now()->addMinutes(3), ['user' => 1]);
        Mail::to($request->only('email'))->send(new ResetPassword($resetUrl . '?token=' . $token));

        return back()->with('message', 'We have e-mailed your password reset link!');
    }
}
