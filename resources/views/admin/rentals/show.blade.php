@extends('layouts.admin')

@section('admin-content')
    <div class="max-w-3xl mx-auto bg-white shadow-md rounded-lg p-6">
        <h1 class="text-4xl font-bold text-[#f56600] mb-6 text-center">Szczegóły zamówienia #{{ $rental->id }}</h1>

        <div class="grid grid-cols-2 gap-6">
            <div class="bg-gray-100 p-4 rounded-md shadow-sm">
                <p class="text-lg"><strong>Użytkownik:</strong></p>
                <p class="text-xl font-semibold">{{ $rental->user->first_name }} {{ $rental->user->last_name }}</p>
            </div>

            <div class="bg-gray-100 p-4 rounded-md shadow-sm">
                <p class="text-lg"><strong>Sprzęt:</strong></p>
                <p class="text-xl font-semibold">{{ $rental->equipment->name }}</p>
            </div>

            <div class="bg-gray-100 p-4 rounded-md shadow-sm">
                <p class="text-lg"><strong>Status:</strong></p>
                <p class="text-xl font-semibold capitalize">{{ str_replace('_', ' ', $rental->status) }}</p>
            </div>

            <div class="bg-gray-100 p-4 rounded-md shadow-sm">
                <p class="text-lg"><strong>Data rozpoczęcia:</strong></p>
                <p class="text-xl font-semibold">{{ $rental->start_date->format('Y-m-d H:i') }}</p>
            </div>

            <div class="bg-gray-100 p-4 rounded-md shadow-sm">
                <p class="text-lg"><strong>Data zakończenia:</strong></p>
                <p class="text-xl font-semibold">{{ $rental->end_date->format('Y-m-d H:i') }}</p>
            </div>

            <div class="bg-gray-100 p-4 rounded-md shadow-sm">
                <p class="text-lg"><strong>Cena całkowita:</strong></p>
                <p class="text-xl font-semibold text-green-600">{{ number_format($rental->total_price, 2, ',', ' ') }} zł</p>
            </div>
        </div>

        @if($rental->notes)
            <div class="bg-gray-50 p-6 mt-6 rounded-md shadow-md">
                <p class="text-lg"><strong>Uwagi:</strong></p>
                <p class="text-gray-700">{{ $rental->notes }}</p>
            </div>
        @endif

        <div class="mt-6 flex justify-between items-center">
            <a href="{{ session('previous_url', route('admin.rentals.list.index')) }}" class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded shadow">
                Powrót
            </a>

            <div class="flex gap-3">
                <a href="{{ route('admin.rentals.edit', $rental) }}"
                   class="bg-[#f56600] hover:bg-[#f98800] text-white py-2 px-6 rounded shadow font-semibold">
                    Edytuj
                </a>

                @if($rental->status === 'oczekujace')
                    <form action="{{ route('admin.rentals.approve', $rental) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded shadow">
                            Zatwierdź
                        </button>
                    </form>

                    <form action="{{ route('admin.rentals.reject', $rental) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                class="bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded shadow">
                            Odrzuć
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection
