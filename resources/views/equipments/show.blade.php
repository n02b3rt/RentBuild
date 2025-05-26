@extends('layouts.app')

@section('content')
    <section class="max-w-[1140px] mx-auto px-4 py-12">
        <div class="flex bg-white p-10 rounded-md shadow-md">
            <div class="max-h-[500px] w-1/2">
                <h1 class="text-4xl font-bold mb-4 text-center">{{ $equipment->name }}</h1>
                <img src="/{{ $equipment->thumbnail }}" alt="{{ $equipment->name }}" class="mb-6 h-[400px] w-auto mx-auto">
            </div>

            <div class="w-1/2 flex flex-col justify-center">
                <div>
                    <span class="text-xs text-gray-500">
                        Najniższa cena z 30 dni {{ number_format($equipment->rental_price, 2) }} zł
                    </span>
                    <p class="text-xl mb-2">
                        <strong>Cena:</strong>
                        @if($equipment->discount)
                            <span>
                                <span class="line-through">
                                    {{ number_format($equipment->rental_price, 2, ',', ' ') }} zł
                                </span>
                                <span class="text-green-600 ml-3">
                                    {{ number_format($equipment->finalPrice(), 2, ',', ' ') }} zł
                                </span>
                            </span>
                        @else
                            <span>
                                {{ number_format($equipment->rental_price, 2, ',', ' ') }} zł
                            </span>
                        @endif
                    </p>

                    <p class="text-lg mb-2">
                        <strong>Dostępność:</strong> {{ ucfirst($equipment->availability) }}
                    </p>
                    <p class="text-lg mb-2">
                        <strong>Stan techniczny:</strong> {{ ucfirst($equipment->technical_state) }}
                    </p>
                    <p class="text-lg mb-2">
                        <strong>Kategoria:</strong> {{ $equipment->category }}
                    </p>
                    <p class="text-lg mb-4">
                        <strong>Liczba wypożyczeń:</strong> {{ $equipment->number_of_rentals }}
                    </p>

                    <p class="text-xl mb-6">
                        {{ $equipment->description }}
                    </p>

                    @if($equipment->isAvailable())
                        <form action="{{ route('client.rentals.summary', $equipment->id) }}" method="GET" class="mb-4 flex justify-center">
                            <button type="submit"
                                    class="bg-[#f56600] hover:bg-[#f98800] text-white font-semibold py-2 px-6 rounded">
                                ZAMÓW
                            </button>
                        </form>
                    @else
                        <span>
                                Produkt nie dostępny
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
