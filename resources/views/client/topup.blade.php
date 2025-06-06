@extends('layouts.app')

@section('content')
    <div class="flex justify-center py-10">
        <div class="w-full max-w-lg bg-white shadow-lg rounded-2xl p-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Doładuj konto</h1>

            {{-- Wyświetlamy status z sesji --}}
            @if(session('status'))
                <div class="bg-green-100 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Błędy walidacji --}}
            @if($errors->any())
                <div class="bg-red-100 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('client.topup.form') }}" method="POST" class="space-y-6">
                @csrf

                {{-- Pole na kwotę --}}
                <div>
                    <label for="amount" class="block text-gray-700 font-medium mb-2">Kwota (PLN)</label>
                    <input
                        type="number"
                        step="0.01"
                        name="amount"
                        id="amount"
                        value="{{ old('amount') }}"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
                        placeholder="np. 50.00"
                        required
                    >
                </div>

                {{-- Pola z danymi karty (symulacja) --}}
                <div>
                    <label for="card_number" class="block text-gray-700 font-medium mb-2">Numer karty</label>
                    <input
                        type="text"
                        name="card_number"
                        id="card_number"
                        value="{{ old('card_number') }}"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
                        placeholder="1234 5678 9012 3456"
                        required
                    >
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="card_expiry" class="block text-gray-700 font-medium mb-2">Data ważności (MM/YY)</label>
                        <input
                            type="text"
                            name="card_expiry"
                            id="card_expiry"
                            value="{{ old('card_expiry') }}"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
                            placeholder="05/26"
                            required
                        >
                    </div>
                    <div>
                        <label for="card_cvc" class="block text-gray-700 font-medium mb-2">CVC</label>
                        <input
                            type="text"
                            name="card_cvc"
                            id="card_cvc"
                            value="{{ old('card_cvc') }}"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
                            placeholder="123"
                            required
                        >
                    </div>
                </div>

                <button
                    type="submit"
                    class="w-full bg-blue-600 text-white font-semibold rounded-lg px-4 py-3 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                >
                    Przejdź dalej
                </button>
            </form>
        </div>
    </div>
@endsection
