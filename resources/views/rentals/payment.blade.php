@extends('layouts.app')

@section('content')
    <section class="max-w-[1140px] mx-auto px-4 py-12">
        <h1 class="text-3xl font-bold mb-6">Płatność za wypożyczenie sprzętu</h1>

        <div class="bg-white p-6 rounded shadow max-w-lg mx-auto">
            @php
                // Pobieramy dane z sesji, które zapisaliśmy w store()
                $data = session('rental_data', []);
                $equipment = $rental->equipment;

                $days              = $data['days']                   ?? 0;
                $dailyEqRate       = $data['equipment_daily_rate']   ?? 0;
                $equipmentCost     = $data['equipment_cost']         ?? 0;
                $operatorDailyRate = $data['operator_daily_rate']    ?? 0;
                $operatorCost      = $data['operator_cost']          ?? 0;
                $totalCost         = $data['total_price']            ?? 0;
                $notes             = $data['notes']                  ?? null;
            @endphp

            <p>
                <strong>Sprzęt:</strong> {{ $equipment->name }}
                @if($data['with_operator'] ?? false)
                    <span class="text-sm text-gray-600">+ operator</span>
                @endif
            </p>

            @if($notes)
                <p><em class="text-sm text-gray-500">{{ $notes }}</em></p>
            @endif

            <p class="mt-2">
                <strong>Okres wypożyczenia:</strong>
                {{ $data['start_date'] }} – {{ $data['end_date'] }}
            </p>

            <p class="mt-4">
                <strong>Łączna kwota:</strong>
                <span class="text-lg font-semibold">{{ number_format($totalCost, 2, ',', ' ') }} zł</span>
            </p>

            <div class="text-sm text-gray-600 mt-2 mb-4 space-y-1">
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

            @if(Auth::user()->account_balance >= $totalCost)
                <form method="POST" action="{{ route('client.rentals.processPayment') }}" class="mt-6">
                    @csrf
                    <button
                        type="submit"
                        class="bg-[#f56600] hover:bg-[#f98800] text-white font-semibold py-2 px-6 rounded w-full"
                    >
                        Zapłać i potwierdź wypożyczenie
                    </button>
                </form>
            @else
                <div class="flex flex-col gap-4 mt-6">
                    <a
                        href="{{ route('client.topup.form') }}"
                        class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-6 rounded w-full text-center"
                    >
                        Brak środków – Doładuj konto
                    </a>

                    {{-- Formularz na kod BIWO --}}
                    <x-biwo-payment />

                    <p class="text-sm text-gray-600 text-center">
                        Masz {{ number_format(Auth::user()->account_balance, 2, ',', ' ') }} zł –
                        potrzebujesz {{ number_format($totalCost, 2, ',', ' ') }} zł
                    </p>
                </div>
            @endif
        </div>
    </section>
@endsection
