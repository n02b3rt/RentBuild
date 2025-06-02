@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-3xl font-bold mb-6">Krok 2: Wybierz sprzęt</h1>

        <form method="POST" action="{{ route('admin.rentals.create.step2.select') }}">
            @csrf

            {{-- Ukryte pole z id użytkownika --}}
            <input type="hidden" name="user_id" value="{{ $user->id }}">

            <div class="grid gap-6 2xl:grid-cols-2 xl:grid-cols-1">
                @if($equipments->isEmpty())
                    <div class="flex flex-col items-center text-gray-500 text-xl mt-10 col-span-2">
                        <svg xmlns="http://www.w3.org/2000/svg" height="126px" viewBox="0 -960 960 960" width="126px" fill="#f56600">
                            <path d="M620-520q25 0 42.5-17.5T680-580q0-25-17.5-42.5T620-640q-25 0-42.5 17.5T560-580q0 25 17.5 42.5T620-520Zm-280 0q25 0 42.5-17.5T400-580q0-25-17.5-42.5T340-640q-25 0-42.5 17.5T280-580q0 25 17.5 42.5T340-520Zm140 100q-68 0-123.5 38.5T276-280h66q22-37 58.5-58.5T480-360q43 0 79.5 21.5T618-280h66q-25-63-80.5-101.5T480-420Zm0 340q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-400Zm0 320q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Z"/>
                        </svg>
                        Żaden sprzęt nie pasuje do wybranych filtrów.
                    </div>
                @else
                    @foreach($equipments as $equipment)
                        <div class="flex items-center border rounded-lg overflow-hidden shadow-sm bg-white mb-6 p-4">
                            <div class="w-1/3 mr-6 flex items-center justify-center" style="height:150px;">
                                <img src="/{{ $equipment->thumbnail }}" alt="{{ $equipment->name }}" class="object-scale-down h-full w-full">
                            </div>
                            <div class="flex-1">
                                <h2 class="text-2xl font-bold mb-1">{{ $equipment->name }}</h2>
                                <p class="text-sm text-gray-500 mb-1">wypożyczono: {{ $equipment->number_of_rentals }}</p>
                                @if($equipment->isPromotionActive())
                                    <p><span class="line-through text-red-500">{{ number_format($equipment->rental_price, 2) }} zł</span> <span class="text-gray-500"> cena z 30 dni</span></p>
                                    <p class="text-3xl font-bold mb-2">{{ number_format($equipment->finalPrice(), 2) }} zł</p>
                                @else
                                    <p class="text-3xl font-bold mb-2">Cena: {{ number_format($equipment->rental_price, 2) }} zł</p>
                                @endif
                                <p class="text-sm">kategoria: {{ $equipment->category }}</p>
                                <p class="text-sm">Dostępność: {{ $equipment->availability }}</p>
                                <p class="text-sm">Stan: {{ $equipment->technical_state }}</p>
                                @if($equipment->isPromotionActive())
                                    <p class="inline-block bg-[#f56600] py-1 px-3 rounded text-white mt-2">
                                        Upust: {{ $equipment->discount }}%
                                    </p>
                                @endif
                            </div>
                            <div class="ml-6">
                                <button type="submit" name="selected_equipment" value="{{ $equipment->id }}" class="bg-[#f56600] hover:bg-orange-600 text-white font-semibold py-2 px-6 rounded">
                                    Wybierz
                                </button>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </form>
    </div>
@endsection
