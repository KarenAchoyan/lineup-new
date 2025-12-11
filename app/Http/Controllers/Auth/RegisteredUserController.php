<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\x{0531}-\x{0556}\x{0561}-\x{0587}\s]+$/u', // Only Armenian letters and spaces
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:'.User::class,
                'regex:/^[a-zA-Z0-9._%+\-@]+$/', // Only Latin letters, numbers, and email special chars
            ],
            'phone' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^(\+995[0-9]{9}|5[0-9]{8})$/', // Georgian phone: +995XXXXXXXXX or 5XXXXXXXX
            ],
            'password' => [
                'required',
                'confirmed',
                Rules\Password::defaults(),
                'regex:/^[a-zA-Z0-9!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]+$/', // Only Latin letters, numbers, and special chars
            ],
        ], [
            'name.regex' => 'Անունը պետք է պարունակի միայն հայատառ տառեր:',
            'phone.regex' => 'Հեռախոսահամարը պետք է լինի վրացական ֆորմատով: +995XXXXXXXXX կամ 5XXXXXXXX',
            'email.regex' => 'Email-ը չպետք է պարունակի հայերեն նիշեր:',
            'password.regex' => 'Գաղտնաբառը չպետք է պարունակի հայերեն նիշեր:',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Redirect to email verification notice
        return redirect(route('verification.notice'));
    }
}
