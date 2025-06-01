@extends('layouts.app')

@section('content')
    <div class="max-w-xl mx-auto py-10">
        <h2 class="text-lg font-semibold mb-4">Ustawienia 2FA</h2>

        <div class="mb-6">
            <p>Zeskanuj poniższy kod QR za pomocą aplikacji Google Authenticator lub innej.</p>
            <div class="mt-4">{!! $qrSvg !!}</div>
            <p class="mt-4 text-sm">Jeśli nie możesz zeskanować, wpisz ten kod ręcznie:</p>
            <code class="block bg-gray-100 p-2 rounded mt-2">{{ $secret }}</code>
        </div>

        <form method="POST" action="{{ route('2fa.confirm') }}">
            @csrf

            <div class="mb-4">
                <label for="code" class="block font-medium">Kod z aplikacji</label>
                <input id="code" type="text" name="code" required autofocus
                       class="mt-1 block w-full border-gray-300 rounded">
                @error('code')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                Potwierdź i aktywuj 2FA
            </button>

        </form>
    </div>
@endsection
