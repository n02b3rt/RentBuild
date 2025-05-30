@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto px-4 py-8">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Kody zapasowe</h2>

        @if (session('status'))
            <div class="mb-4 p-3 bg-green-100 border border-green-300 text-green-800 rounded">
                {{ session('status') }}
            </div>
        @endif

        <p class="text-gray-700 mb-4">
            Zachowaj te kody w bezpiecznym miejscu. Każdy z nich działa tylko raz:
        </p>

        <ul class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-gray-100 p-4 rounded shadow">
            @foreach($codes as $code)
                <li class="text-sm font-mono bg-white px-3 py-2 rounded border border-gray-300 shadow-sm">
                    {{ $code }}
                </li>
            @endforeach
        </ul>

        <form method="POST" action="{{ route('2fa.recovery.regenerate') }}" class="mt-6">
            @csrf
            <button type="submit"
                    class="bg-[#f56600] text-white px-4 py-2 rounded-md hover:bg-orange-600 transition font-semibold">
                Wygeneruj nowe kody
            </button>
        </form>

        <div class="flex flex-col sm:flex-row gap-3 mt-6">
            <a href="{{ route('profile.edit') }}"
               class="text-sm px-4 py-2 rounded-md border border-gray-400 text-gray-800 hover:bg-gray-100 transition text-center">
                ⬅️ Powrót do profilu
            </a>
        </div>
    </div>
@endsection
