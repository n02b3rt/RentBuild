@extends('layouts.app')

@section('content')
    <section class="max-w-[1140px] mx-auto px-6 py-12 bg-white rounded-lg shadow-lg" x-data="{
        openCurrent: true, openFuture: false, openFinished: false, openPending: true
    }">
        <h1 class="text-3xl font-bold mb-8 text-gray-900">Moje wypożyczenia</h1>

        @php
            $pendingRentals = $rentals->where('status', 'oczekujace');
            $currentRentals = $rentals->where('status', 'aktualne');
            $futureRentals = $rentals->where('status', 'nadchodzace');
            $finishedRentals = $rentals->where('status', 'przeszle');
        @endphp

        {{-- Oczekujące --}}
        <section class="mb-8 border rounded-lg shadow-sm">
            <header class="cursor-pointer bg-yellow-100 px-6 py-4 flex justify-between items-center"
                    @click="openPending = !openPending">
                <h2 class="text-xl font-semibold text-yellow-700">Oczekujące wypożyczenia</h2>
                <svg :class="{'transform rotate-180': openPending}" class="w-5 h-5 text-yellow-700 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" >
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </header>
            <div x-show="openPending" x-transition class="px-6 py-4 bg-yellow-50 space-y-4">
                @if($pendingRentals->isEmpty())
                    <p class="text-yellow-700 italic">Brak oczekujących wypożyczeń.</p>
                @else
                    @foreach($pendingRentals as $rental)
                        <div class="border border-yellow-300 rounded-lg p-5 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between bg-yellow-50">
                            <div>
                                <h3 class="font-semibold text-lg text-gray-900">{{ $rental->equipment->name }}</h3>
                                <p class="text-sm text-gray-700">
                                    <strong>Okres:</strong> {{ $rental->start_date->format('Y-m-d') }} – {{ $rental->end_date->format('Y-m-d') }}
                                </p>
                                <p class="text-sm text-gray-700 italic">{{ $rental->notes ?? 'Brak uwag' }}</p>
                            </div>
                            <div class="mt-4 md:mt-0 flex items-center space-x-4">
                                <span class="px-3 py-1 rounded-full bg-yellow-200 text-yellow-800 text-xs font-semibold">
                                    {{ ucfirst($rental->status) }}
                                </span>
                                <span class="font-semibold text-yellow-700 text-lg">
                                    {{ number_format($rental->total_price, 2, ',', ' ') }} zł
                                </span>
                                <form method="POST" action="#">
                                    @csrf
                                    <button type="submit"
                                            class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-6 rounded transition">
                                        Anuluj
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </section>

        {{-- Aktualne --}}
        <section class="mb-8 border rounded-lg shadow-sm">
            <header class="cursor-pointer bg-orange-100 px-6 py-4 flex justify-between items-center"
                    @click="openCurrent = !openCurrent">
                <h2 class="text-xl font-semibold text-orange-700">Aktualne wypożyczenia</h2>
                <svg :class="{'transform rotate-180': openCurrent}" class="w-5 h-5 text-orange-700 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" >
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </header>
            <div x-show="openCurrent" x-transition class="px-6 py-4 bg-orange-50 space-y-4">
                @if($currentRentals->isEmpty())
                    <p class="text-gray-600 italic">Brak aktualnych wypożyczeń.</p>
                @else
                    @foreach($currentRentals as $rental)
                        <div class="border border-orange-300 rounded-lg p-5 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between bg-orange-50">
                            <div>
                                <h3 class="font-semibold text-lg text-gray-900">{{ $rental->equipment->name }}</h3>
                                <p class="text-sm text-gray-700">
                                    <strong>Okres:</strong> {{ $rental->start_date->format('Y-m-d') }} – {{ $rental->end_date->format('Y-m-d') }}
                                </p>
                                <p class="text-sm text-gray-700 italic">{{ $rental->notes ?? 'Brak uwag' }}</p>
                            </div>
                            <div class="mt-4 md:mt-0 flex items-center space-x-4">
                                <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-800 text-xs font-semibold">
                                    {{ ucfirst($rental->status) }}
                                </span>
                                <span class="font-semibold text-orange-600 text-lg">
                                    {{ number_format($rental->total_price, 2, ',', ' ') }} zł
                                </span>
                                <form method="POST" action="#">
                                    @csrf
                                    <button type="submit"
                                            class="bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2 px-6 rounded transition">
                                        Zakończ
                                    </button>
                                </form>
                                <form method="GET" action="#">
                                    <button type="submit"
                                            class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-6 rounded transition">
                                        Złóż reklamację
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </section>

        {{-- Nadchodzące --}}
        <section class="mb-8 border rounded-lg shadow-sm">
            <header class="cursor-pointer bg-blue-100 px-6 py-4 flex justify-between items-center"
                    @click="openFuture = !openFuture">
                <h2 class="text-xl font-semibold text-blue-700">Nadchodzące wypożyczenia</h2>
                <svg :class="{'transform rotate-180': openFuture}" class="w-5 h-5 text-blue-700 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" >
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </header>
            <div x-show="openFuture" x-transition class="px-6 py-4 bg-blue-50 space-y-4">
                @if($futureRentals->isEmpty())
                    <p class="text-gray-600 italic">Brak nadchodzących wypożyczeń.</p>
                @else
                    @foreach($futureRentals as $rental)
                        <div class="border border-blue-300 rounded-lg p-5 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between bg-blue-50">
                            <div>
                                <h3 class="font-semibold text-lg text-gray-900">{{ $rental->equipment->name }}</h3>
                                <p class="text-sm text-gray-700">
                                    <strong>Okres:</strong> {{ $rental->start_date->format('Y-m-d') }} – {{ $rental->end_date->format('Y-m-d') }}
                                </p>
                                <p class="text-sm text-gray-700 italic">{{ $rental->notes ?? 'Brak uwag' }}</p>
                            </div>
                            <div class="mt-4 md:mt-0 flex items-center space-x-4">
                                <span class="px-3 py-1 rounded-full bg-gray-200 text-gray-800 text-xs font-semibold">
                                    {{ ucfirst($rental->status) }}
                                </span>
                                <span class="font-semibold text-blue-600 text-lg">
                                    {{ number_format($rental->total_price, 2, ',', ' ') }} zł
                                </span>
                                <form method="POST" action="#">
                                    @csrf
                                    <button type="submit"
                                            class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-6 rounded transition">
                                        Anuluj
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </section>

        {{-- Zakończone --}}
        <section class="border rounded-lg shadow-sm">
            <header class="cursor-pointer bg-green-100 px-6 py-4 flex justify-between items-center"
                    @click="openFinished = !openFinished">
                <h2 class="text-xl font-semibold text-green-700">Zakończone wypożyczenia</h2>
                <svg :class="{'transform rotate-180': openFinished}" class="w-5 h-5 text-green-700 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" >
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </header>
            <div x-show="openFinished" x-transition class="px-6 py-4 bg-green-50 space-y-4">
                @if($finishedRentals->isEmpty())
                    <p class="text-gray-600 italic">Brak zakończonych wypożyczeń.</p>
                @else
                    @foreach($finishedRentals as $rental)
                        <div class="border border-green-300 rounded-lg p-5 shadow-sm bg-green-50">
                            <h3 class="font-semibold text-lg text-gray-900">{{ $rental->equipment->name }}</h3>
                            <p class="text-sm text-gray-700">
                                <strong>Okres:</strong> {{ $rental->start_date->format('Y-m-d') }} – {{ $rental->end_date->format('Y-m-d') }}
                            </p>
                            <p class="text-sm text-gray-700 italic">{{ $rental->notes ?? 'Brak uwag' }}</p>
                            <p class="font-semibold text-green-700 text-lg">
                                {{ number_format($rental->total_price, 2, ',', ' ') }} zł
                            </p>
                        </div>
                    @endforeach
                @endif
            </div>
        </section>
    </section>
@endsection
