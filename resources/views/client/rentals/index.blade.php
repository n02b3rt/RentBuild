@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Moje wypożyczenia</h2>

        <h4>Aktualne</h4>
        @foreach($currentRentals as $rental)
            <div>
                <strong>{{ $rental->equipment->name }}</strong> – do {{ $rental->end_date }}
                <a href="{{ route('client.rentals.show', $rental->id) }}">Szczegóły</a>
            </div>
        @endforeach

        <h4>Przyszłe</h4>
        @foreach($futureRentals as $rental)
            <div>{{ $rental->equipment->name }} od {{ $rental->start_date }}</div>
        @endforeach

        <h4>Przeszłe</h4>
        @foreach($pastRentals as $rental)
            <div>{{ $rental->equipment->name }} zakończone {{ $rental->end_date }}</div>
        @endforeach
    </div>
@endsection
