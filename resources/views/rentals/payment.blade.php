@extends('layouts.app')

@section('content')
    <section class="max-w-[1140px] mx-auto px-4 py-12">
        <h1 class="text-3xl font-bold mb-6">Płatność za wypożyczenie sprzętu</h1>

        <div class="bg-white p-6 rounded shadow max-w-lg mx-auto">
            <p><strong>Sprzęt:</strong>
                {{ $rental->equipment->name }}

                @if($rental->with_operator)
                    <span class="text-sm text-gray-600">+ operator</span>
                @endif
            </p>
            <p><strong>Okres wypożyczenia:</strong> {{ $rental->start_date->format('Y-m-d') }} – {{ $rental->end_date->format('Y-m-d') }}</p>

            @php
                $days = $rental->start_date->diffInDays($rental->end_date) + 1;
                $dailyPrice = $rental->equipment->finalPrice();
                $equipmentCost = round($days * $dailyPrice, 2);
                $operatorCost = $rental->with_operator ? round($days * 350, 2) : 0;
                $totalCost = $rental->total_price;
            @endphp

            <p><strong>Łączna kwota:</strong> <span class="text-lg font-semibold">{{ number_format($totalCost, 2, ',', ' ') }} zł</span></p>

            <div class="text-sm text-gray-600 mt-2 mb-4">
                <p>{{ $days }} dni × {{ number_format($dailyPrice, 2, ',', ' ') }} zł = {{ number_format($equipmentCost, 2, ',', ' ') }} zł</p>
                @if($rental->with_operator)
                    <p>{{ $days }} dni × 350 zł = {{ number_format($operatorCost, 2, ',', ' ') }} zł</p>
                @endif
            </div>

            @if(Auth::user()->account_balance >= $totalCost)
                <form method="POST" action="{{ route('client.rentals.processPayment', $rental) }}" class="mt-6">
                    @csrf
                    <button type="submit" class="bg-[#f56600] hover:bg-[#f98800] text-white font-semibold py-2 px-6 rounded w-full">
                        Zapłać i potwierdź wypożyczenie
                    </button>
                </form>
            @else
                <div class="flex flex-col gap-4 mt-6">
                    <a href="{{ route('client.account.topup.form') }}" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-6 rounded w-full text-center">
                        Brak środków – Doładuj konto
                    </a>

                    <form method="POST" action="#" class="bg-yellow-100 p-4 rounded shadow-inner">
                        @csrf
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Lub zapłąć kodem BIWO</label>
                        <input type="text" name="code" id="code" required
                               class="border p-2 rounded w-full mb-3"
                               placeholder="Wpisz kod BIWO">

                        <button type="submit"
                                class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-6 rounded w-full text-center">
                            Zapłać kodem BIWO
                        </button>
                    </form>

                    <p class="text-sm text-gray-600 text-center">
                        Masz {{ number_format(Auth::user()->account_balance, 2, ',', ' ') }} zł – potrzebujesz {{ number_format($totalCost, 2, ',', ' ') }} zł
                    </p>
                </div>
            @endif
        </div>
    </section>
@endsection
