@extends('layouts.guest')

@section('content')
    <form method="POST" action="{{ route('register') }}" x-data="{ sameAddress: true }">
        @csrf

        {{-- Imię + Nazwisko --}}
        <div class="flex space-x-4">
            <div class="w-1/2">
                <x-input-label for="first_name" :value="'Imię'" />
                <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full" required />
                <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
            </div>

            <div class="w-1/2">
                <x-input-label for="last_name" :value="'Nazwisko'" />
                <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full" required />
                <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
            </div>
        </div>

        {{-- Telefon --}}
        <div class="mt-4">
            <x-input-label for="phone" :value="'Numer telefonu'" />
            <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        {{-- Email --}}
        <div class="mt-4">
            <x-input-label for="email" :value="'Email'" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" required />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Hasło --}}
        <div class="mt-4">
            <x-input-label for="password" :value="'Hasło'" />
            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Potwierdź hasło --}}
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="'Potwierdź hasło'" />
            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        {{-- Adres zamieszkania --}}
        <div class="mt-4">
            <x-input-label for="address" :value="'Adres zamieszkania'" />
            <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" required />
            <x-input-error :messages="$errors->get('address')" class="mt-2" />
        </div>

        {{-- Checkbox: adres dostawy taki sam --}}
        <div class="mt-4 flex items-center">
            <input type="checkbox" id="sameAddress" name="same_address" x-model="sameAddress" class="..." checked>
            <label for="sameAddress" class="ms-2 text-sm text-gray-600">Adres dostawy taki sam jak zamieszkania</label>
        </div>

        {{-- Adres dostawy, jeśli inny --}}
        <div class="mt-4" x-show="!sameAddress">
            <x-input-label for="shipping_address" :value="'Adres dostawy'" />
            <x-text-input id="shipping_address" name="shipping_address" type="text" class="mt-1 block w-full" />
            <x-input-error :messages="$errors->get('shipping_address')" class="mt-2" />
        </div>

        {{-- Przycisk rejestracji --}}
        <div class="mt-6">
            <x-primary-button class="w-full justify-center">
                Zarejestruj się
            </x-primary-button>
        </div>

        {{-- Link do logowania --}}
        <div class="mt-4 text-center">
            <span class="text-sm text-gray-600">Masz już konto?</span>
            <a href="{{ route('login') }}" class="text-sm text-orange-600 hover:underline ml-1">
                Zaloguj się
            </a>
        </div>
    </form>
@endsection
