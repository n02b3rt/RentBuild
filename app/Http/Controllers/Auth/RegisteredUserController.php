<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Wyświetla widok rejestracji.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Obsługuje żądanie rejestracji.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name'         => ['required', 'string', 'max:255'],
            'last_name'          => ['required', 'string', 'max:255'],
            'phone'              => ['nullable', 'string', 'max:20'],
            'email'              => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'           => ['required', 'confirmed', Rules\Password::defaults()],
            'address'            => ['required', 'string', 'max:1024'],
            'same_address'       => ['nullable'], // checkbox
            'shipping_address'   => ['nullable', 'string', 'max:1024'],
        ]);

        $isSame = ($validated['same_address'] ?? false) === 'on';

        $shippingAddress = $isSame
            ? $validated['address']
            : ($validated['shipping_address'] ?? null);

//        dd($validated, $request->all());

        $user = User::create([
            'first_name'        => $validated['first_name'],
            'last_name'         => $validated['last_name'],
            'phone'             => $validated['phone'] ?? null,
            'email'             => $validated['email'],
            'password'          => Hash::make($validated['password']),
            'address'           => $validated['address'],
            'shipping_address'  => $shippingAddress,
            'payment_token'     => Str::uuid(),
            'payment_provider'  => 'rentbuild',
            'role'              => 'klient',
            'account_balance'   => 0,
            'rentals_count'     => 0,
        ]);

        $user->email_verified_at = now();
        $user->save();


        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
