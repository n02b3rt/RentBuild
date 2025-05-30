@extends('layouts.admin')

@section('admin-content')
    <div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow-lg">

        <h1 class="text-3xl font-extrabold mb-8 text-[#f56600]">Reklamacja nr {{ $rental->id }}</h1>

        <section class="mb-8 p-6 border border-gray-200 rounded-lg bg-[#fff7f0]">
            <h2 class="text-2xl font-semibold mb-4 text-[#f56600]">Szczegóły wypożyczenia</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-gray-800">
                <div><span class="font-semibold">Sprzęt:</span> {{ $rental->equipment->name }}</div>
                <div><span class="font-semibold">Użytkownik:</span> {{ $rental->user->name }} ({{ $rental->user->email }})</div>
                <div><span class="font-semibold">Okres wypożyczenia:</span> {{ $rental->start_date->format('Y-m-d') }} – {{ $rental->end_date?->format('Y-m-d') ?? 'Brak' }}</div>
                <div><span class="font-semibold">Całkowita cena:</span> {{ number_format($rental->total_price, 2, ',', ' ') }} zł</div>
            </div>
        </section>

        <section class="mb-8 p-6 border border-gray-200 rounded-lg bg-[#fff7f0]">
            <h2 class="text-2xl font-semibold mb-4 text-[#f56600]">Status reklamacji</h2>
            <div class="text-gray-800 space-y-2">
                <p><span class="font-semibold">Aktualny status:</span> <span class="capitalize">{{ str_replace('_', ' ', $rental->status) }}</span></p>
                <p><span class="font-semibold">Podstatus:</span> <span class="capitalize">{{ $rental->complaintStatus() ?? 'Brak' }}</span></p>
            </div>
        </section>

        <section class="mb-8 p-6 border border-gray-200 rounded-lg bg-white shadow-sm">
            <h2 class="text-2xl font-semibold mb-4 text-[#f56600]">Treść reklamacji / Notatki</h2>
            <pre class="whitespace-pre-wrap bg-gray-100 p-4 rounded text-gray-700 max-h-64 overflow-auto">{{ $rental->notes }}</pre>
        </section>

        <section class="mb-8 p-6 border border-gray-200 rounded-lg bg-white shadow-sm">
            <h2 class="text-2xl font-semibold mb-4 text-[#f56600]">Rozpatrz reklamację</h2>

            <form method="POST" action="{{ route('admin.rentals.complaints.resolve', $rental) }}" class="space-y-6">
                @csrf

                <div>
                    <label for="decision" class="block font-semibold mb-2">Wybierz decyzję:</label>
                    <select name="decision" id="decision" required
                            class="border border-gray-300 rounded p-3 w-full max-w-xs focus:outline-none focus:ring-2 focus:ring-[#f56600]">
                        <option value="weryfikacja" {{ $rental->complaintStatus() === 'weryfikacja' ? 'selected' : '' }}>Weryfikacja</option>
                        <option value="odrzucono" {{ $rental->complaintStatus() === 'odrzucono' ? 'selected' : '' }}>Odrzucono</option>
                        <option value="przyjeto" {{ $rental->complaintStatus() === 'przyjeto' ? 'selected' : '' }}>Przyjęto</option>
                    </select>
                </div>

                <button type="submit"
                        class="bg-[#f56600] hover:bg-[#d25100] text-white font-semibold py-3 px-6 rounded transition">
                    Zatwierdź decyzję
                </button>
            </form>

            @if(session('success'))
                <p class="mt-4 text-green-600 font-semibold">{{ session('success') }}</p>
            @endif

            @if($errors->any())
                <div class="mt-4 text-red-600 font-semibold space-y-1">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
@endsection
