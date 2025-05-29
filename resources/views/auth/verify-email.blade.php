@extends('layouts.guest')

@section('content')
    <div class="mb-4 text-sm text-gray-600">
        Dziękujemy za rejestrację! Twoje konto zostało utworzone.
        <br>
        Aby aktywować konto, kliknij w <span class="font-semibold">link weryfikacyjny</span>, który został <span class="font-semibold">zalogowany w systemie</span> (plik logów).
        <br>
        Jeśli chcesz, możemy go wygenerować ponownie.
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            Nowy link weryfikacyjny został zapisany do logów (ponownie).
        </div>
    @endif

    <div class="mt-4 flex flex-col sm:flex-row items-center justify-between gap-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-primary-button>
                Wygeneruj link ponownie
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                Wyloguj się
            </button>
        </form>
    </div>
@endsection
