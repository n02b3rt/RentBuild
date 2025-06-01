@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto p-8 bg-white rounded-lg shadow-lg">

        <h1 class="text-3xl font-extrabold mb-8 text-[#f56600]">
            Zgłoś reklamację dla: {{ $rental->equipment->name }}
        </h1>

        <section class="mb-8 p-6 border border-gray-200 rounded-lg bg-[#fff7f0]">
            <h2 class="text-2xl font-semibold mb-4 text-[#f56600]">Szczegóły wypożyczenia</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-gray-800">
                <div><span class="font-semibold">Sprzęt:</span> {{ $rental->equipment->name }}</div>
                <div><span class="font-semibold">Użytkownik:</span> {{ $rental->user->name }} ({{ $rental->user->email }})</div>
                <div><span class="font-semibold">Okres wypożyczenia:</span><br/> {{ $rental->start_date->format('Y-m-d') }} – {{ $rental->end_date?->format('Y-m-d') ?? 'Brak' }}</div>
                <div><span class="font-semibold">Całkowita cena:</span> {{ number_format($rental->total_price, 2, ',', ' ') }} zł</div>
                <div><span class="font-semibold">Status wypożyczenia:</span> <span class="capitalize">{{ str_replace('_', ' ', $rental->status) }}</span></div>
            </div>
        </section>

        <section class="mb-8 p-6 border border-gray-200 rounded-lg bg-white shadow-sm">
            <h2 class="text-2xl font-semibold mb-4 text-[#f56600]">Opis reklamacji</h2>

            <form method="POST" action="{{ route('client.rentals.complaint.store', $rental) }}" class="space-y-6">
                @csrf

                <div>
                    <label for="description" class="block text-gray-800 font-semibold mb-3">Opisz problem ze sprzętem:</label>
                    <textarea
                        name="description"
                        id="description"
                        rows="6"
                        required
                        class="w-full border border-gray-300 rounded-md p-3 resize-none
                        focus:outline-none focus:ring-2 focus:ring-[#f56600] @error('description') border-red-500 @enderror"
                    >{{ old('description') }}</textarea>
                    @error('description')
                    <p class="text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center space-x-4">
                    <button
                        type="submit"
                        class="bg-[#f56600] hover:bg-[#d25100] text-white font-semibold py-3 px-6 rounded-md transition"
                    >
                        Wyślij reklamację
                    </button>
                    <a
                        href="{{ route('client.rentals.index') }}"
                        class="text-gray-600 hover:underline"
                    >
                        Anuluj
                    </a>
                </div>
            </form>
        </section>
    </div>
@endsection
