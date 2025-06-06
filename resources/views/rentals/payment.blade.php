{{-- resources/views/rentals/payment.blade.php --}}

@extends('layouts.app')

@section('content')
    <section class="max-w-[1140px] mx-auto px-4 py-12">
        <h1 class="text-3xl font-bold mb-6">Płatność za wypożyczenie sprzętu</h1>

        <div class="bg-white p-6 rounded shadow max-w-lg mx-auto">
            @php
                // Dane przekazane z kontrolera:
                // $rental, $totalPrice, $discountPercent, $discountAmount

                // Jeśli dodatkowo potrzebujemy breakdownu:
                $data               = session('rental_data', []);
                $equipment          = $rental->equipment;
                $days               = $data['days']                 ?? 0;
                $dailyEqRate        = $data['equipment_daily_rate'] ?? 0;
                $equipmentCost      = $data['equipment_cost']       ?? 0;
                $operatorDailyRate  = $data['operator_daily_rate']  ?? 0;
                $operatorCost       = $data['operator_cost']        ?? 0;
                $notes              = $data['notes']                ?? null;
            @endphp

            {{-- 1. Informacje o sprzęcie --}}
            <p>
                <strong>Sprzęt:</strong> {{ $equipment->name }}
                @if($data['with_operator'] ?? false)
                    <span class="text-sm text-gray-600">+ operator</span>
                @endif
            </p>

            {{-- 2. Notatki (np. “Wypożyczenie z operatorem”) --}}
            @if($notes)
                <p><em class="text-sm text-gray-500">{{ $notes }}</em></p>
            @endif

            {{-- 3. Okres wypożyczenia --}}
            <p class="mt-2">
                <strong>Okres wypożyczenia:</strong>
                {{ $data['start_date'] }} – {{ $data['end_date'] }}
            </p>

            {{-- 4. Breakdown kosztów przed rabatem --}}
            <div class="text-sm text-gray-600 mt-4 mb-4 space-y-1">
                <p>
                    {{ $days }} dni × {{ number_format($dailyEqRate, 2, ',', ' ') }} zł =
                    {{ number_format($equipmentCost, 2, ',', ' ') }} zł
                </p>
                @if($data['with_operator'] ?? false)
                    <p>
                        {{ $days }} dni × {{ number_format($operatorDailyRate, 2, ',', ' ') }} zł =
                        {{ number_format($operatorCost, 2, ',', ' ') }} zł
                    </p>
                @endif
            </div>

            {{-- 5. Kwota przed rabatem --}}
            <p class="mt-4">
                <strong>Kwota przed rabatem:</strong>
                <span class="text-lg font-semibold">
                    {{ number_format($equipmentCost + $operatorCost, 2, ',', ' ') }} zł
                </span>
            </p>

            {{-- 6. Jeżeli jest zniżka lojalnościowa, pokazujemy procent i kwotę rabatu --}}
            @if($discountPercent > 0)
                <div class="mt-2 p-4 bg-green-50 border border-green-200 rounded">
                    <p>
                        <strong>Zniżka lojalnościowa:</strong>
                        {{ $discountPercent }}%
                    </p>
                    <p>
                        <strong>Kwota rabatu:</strong>
                        –{{ number_format($discountAmount, 2, ',', ' ') }} zł
                    </p>
                    <p class="mt-2">
                        <strong>Kwota do zapłaty po rabacie:</strong>
                        <span class="text-xl font-bold text-red-600">
                            {{ number_format($totalPrice, 2, ',', ' ') }} zł
                        </span>
                    </p>
                </div>
            @else
                <p class="mt-2 text-sm text-gray-600">
                    Brak zniżki lojalnościowej.
                </p>
            @endif

            {{-- 7. Formularz płatności --}}
            @if(Auth::user()->account_balance >= $totalPrice)
                <form method="POST" action="{{ route('client.rentals.processPayment') }}" class="mt-6">
                    @csrf

                    {{-- Ukryte pole: cena do pobrania (po rabacie) --}}
                    <input type="hidden" name="total_price" value="{{ $totalPrice }}">

                    <button
                        type="submit"
                        class="bg-[#f56600] hover:bg-[#f98800] text-white font-semibold py-2 px-6 rounded w-full"
                    >
                        Zapłać {{ number_format($totalPrice, 2, ',', ' ') }} zł i potwierdź wypożyczenie
                    </button>
                </form>

                <p class="text-sm text-gray-600 text-center mt-2">
                    Masz saldo: {{ number_format(Auth::user()->account_balance, 2, ',', ' ') }} zł –
                    potrzebujesz: {{ number_format($totalPrice, 2, ',', ' ') }} zł
                </p>
            @else
                <div class="flex flex-col gap-4 mt-6">
                    <a
                        href="{{ route('client.account.topup.form') }}"
                        class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-6 rounded w-full text-center"
                    >
                        Brak środków – Doładuj konto
                    </a>

                    {{-- Formularz na kod BIWO --}}
                    <form
                        method="POST"
                        action="{{ route('client.rentals.payWithBiwo') }}"
                        class="bg-yellow-100 p-4 rounded shadow-inner"
                    >
                        @csrf
                        <label for="code" class="block text-sm font-medium mb-1">
                            Lub zapłać kodem BIWO
                        </label>
                        <input
                            type="text"
                            name="code"
                            id="code"
                            required
                            class="border p-2 rounded w-full mb-3"
                            placeholder="Wpisz kod BIWO"
                        >
                        <button
                            type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-6 rounded w-full"
                        >
                            Zapłać kodem BIWO
                        </button>
                    </form>

                    <p class="text-sm text-gray-600 text-center">
                        Masz {{ number_format(Auth::user()->account_balance, 2, ',', ' ') }} zł –
                        potrzebujesz {{ number_format($totalPrice, 2, ',', ' ') }} zł
                    </p>
                </div>
            @endif
        </div>
    </section>
@endsection
