@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow-lg">
        <div class="flex align-middle">
            <a href="{{ route('client.rentals.complaints.index') }}"
               class="inline-flex mr-4 items-center text-[#f56600] hover:text-[#d25100] mb-6 font-semibold">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <h1 class="text-3xl font-extrabold mb-8 text-[#f56600]">Szczegóły reklamacji nr {{ $rental->id }}</h1>
        </div>


        <section class="mb-6 p-6 border rounded bg-[#fff7f0]">
            <h2 class="text-2xl font-semibold mb-4 text-[#f56600]">Szczegóły wypożyczenia</h2>
            <p><strong>Sprzęt:</strong> {{ $rental->equipment->name }}</p>
            <p><strong>Okres wypożyczenia:</strong> {{ $rental->start_date->format('Y-m-d') }} – {{ $rental->end_date?->format('Y-m-d') ?? 'Brak' }}</p>
            <p><strong>Status wypożyczenia:</strong> {{ str_replace('_', ' ', $rental->status) }}</p>
            <p><strong>Całkowita cena:</strong> {{ number_format($rental->total_price, 2, ',', ' ') }} zł</p>
        </section>

        <section class="mb-6 p-6 border rounded bg-white shadow-sm">
            <h2 class="text-2xl font-semibold mb-4 text-[#f56600]">Reklamacja</h2>
            @if($rental->isComplaint())
                <p><strong>Status reklamacji:</strong> {{ $rental->status }}</p>
                <p><strong>Podstatus:</strong> {{ $rental->complaintStatus() ?? 'Brak' }}</p>
                <pre class="bg-gray-100 p-4 rounded whitespace-pre-wrap mt-4">{{ $rental->notes }}</pre>
            @else
                <p>Brak zgłoszonej reklamacji dla tego wypożyczenia.</p>
            @endif
        </section>

    </div>
@endsection
