@extends('layouts.app')

@section('content')
    <div class="min-h-[400px] flex items-center justify-center px-4">
        <div class="w-full max-w-md bg-white shadow-md rounded-lg p-6">
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">🔐 Weryfikacja dwuetapowa</h2>

            @if ($errors->any())
                <div class="mb-4 text-sm text-red-600 bg-red-100 border border-red-200 rounded p-3">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('2fa.login') }}">
                @csrf

                <div class="mb-4">
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-1">
                        Kod 2FA z aplikacji
                    </label>
                    <input id="code" name="code" type="text" inputmode="numeric"
                           class="block w-full rounded-md border border-gray-300 shadow-sm focus:ring-orange-500 focus:border-orange-500"
                           placeholder="np. 123456" autofocus>
                </div>

                <div class="text-center my-2 text-gray-500 font-medium">lub</div>

                <div class="mb-6">
                    <label for="recovery_code" class="block text-sm font-medium text-gray-700 mb-1">
                        Kod zapasowy
                    </label>
                    <input id="recovery_code" name="recovery_code" type="text"
                           class="block w-full rounded-md border border-gray-300 shadow-sm focus:ring-orange-500 focus:border-orange-500"
                           placeholder="np. A1B2C3D4">
                </div>

                <button type="submit"
                        class="w-full bg-[#f56600] hover:bg-orange-600 text-white font-semibold py-2 px-4 rounded-md transition duration-200">
                    🔓 Zaloguj się
                </button>
            </form>
        </div>
    </div>
@endsection
