@extends('layouts.app')

@section('content')
    <section class="max-w-[1140px] mx-auto px-4 py-12">
        <h1 class="text-3xl font-bold mb-4">Moje wypożyczenia</h1>

        @if($rentals->isEmpty())
            <p>Nie masz żadnych wypożyczeń.</p>
        @else
            <table class="min-w-full bg-white border">
                <thead>
                <tr>
                    <th class="border px-4 py-2">Sprzęt</th>
                    <th class="border px-4 py-2">Data rozpoczęcia</th>
                    <th class="border px-4 py-2">Data zakończenia</th>
                    <th class="border px-4 py-2">Status</th>
                    <th class="border px-4 py-2">Uwagi</th>
                    <th class="border px-4 py-2">Cena całkowita</th>
                </tr>
                </thead>
                <tbody>
                @foreach($rentals as $rental)
                    <tr>
                        <td class="border px-4 py-2">{{ $rental->equipment->name }}</td>
                        <td class="border px-4 py-2">{{ $rental->start_date->format('Y-m-d') }}</td>
                        <td class="border px-4 py-2">{{ $rental->end_date->format('Y-m-d') }}</td>
                        <td class="border px-4 py-2">{{ ucfirst($rental->status) }}</td>
                        <td class="border px-4 py-2">{{ $rental->notes ?? '-' }}</td>
                        <td class="border px-4 py-2">{{ number_format($rental->total_price, 2, ',', ' ') }} zł</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </section>
@endsection
