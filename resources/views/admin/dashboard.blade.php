{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.admin')

@section('admin-content')
    <div class="container mx-auto px-4 py-6">
        {{-- Nagłówek --}}
        <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold mb-6 text-[#f56600]">
            Raport finansowy – ostatnie 30 dni
        </h1>

        {{-- 1) Kafelki z podstawowymi wskaźnikami --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-green-100 border border-green-300 rounded-xl p-4 sm:p-6">
                <h2 class="text-base sm:text-lg font-semibold text-green-800">Łączny przychód</h2>
                <p class="text-xl sm:text-2xl font-bold mt-2 text-green-700">
                    {{ number_format($totalRevenue, 2, ',', ' ') }} zł
                </p>
            </div>

            <div class="bg-blue-100 border border-blue-300 rounded-xl p-4 sm:p-6">
                <h2 class="text-base sm:text-lg font-semibold text-blue-800">Liczba wypożyczeń</h2>
                <p class="text-xl sm:text-2xl font-bold mt-2 text-blue-700">
                    {{ $totalRentals }}
                </p>
            </div>

            <div class="bg-orange-100 border border-orange-300 rounded-xl p-4 sm:p-6">
                <h2 class="text-base sm:text-lg font-semibold text-orange-800">Średnia wartość</h2>
                <p class="text-xl sm:text-2xl font-bold mt-2 text-orange-700">
                    {{ number_format($averageRental, 2, ',', ' ') }} zł
                </p>
            </div>

            <div class="bg-red-100 border border-red-300 rounded-xl p-4 sm:p-6">
                <h2 class="text-base sm:text-lg font-semibold text-red-800">Koszt operatorów</h2>
                <p class="text-xl sm:text-2xl font-bold mt-2 text-red-700">
                    {{ number_format($operatorCost, 2, ',', ' ') }} zł
                </p>
            </div>
        </div>

        {{-- 2) Najlepszy klient --}}
        <div class="mb-8 bg-white shadow rounded-lg p-4 sm:p-6">
            <h3 class="text-lg sm:text-xl font-semibold mb-4">Najlepszy klient (ostatnie 30 dni)</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2 text-sm sm:text-base">
                <div class="flex flex-col sm:flex-row">
                    <span class="w-full sm:w-40 text-gray-600">Użytkownik:</span>
                    <span class="font-medium break-words">{{ $topUserName }}</span>
                </div>
                <div class="flex flex-col sm:flex-row">
                    <span class="w-full sm:w-40 text-gray-600">Liczba wypożyczeń:</span>
                    <span class="font-medium">{{ $topUserCount }}</span>
                </div>
                <div class="flex flex-col sm:flex-row">
                    <span class="w-full sm:w-40 text-gray-600">Suma brutto:</span>
                    <span class="font-medium">{{ number_format($topUserBrutto, 2, ',', ' ') }} zł</span>
                </div>
                <div class="flex flex-col sm:flex-row">
                    <span class="w-full sm:w-40 text-gray-600">Koszt operatora:</span>
                    <span class="font-medium">{{ number_format($topUserOpCost, 2, ',', ' ') }} zł</span>
                </div>
                <div class="flex flex-col sm:flex-row">
                    <span class="w-full sm:w-40 text-gray-600">Suma netto:</span>
                    <span class="font-medium">{{ number_format($topUserNetto, 2, ',', ' ') }} zł</span>
                </div>
            </div>
        </div>

        {{-- 3) Wykres dziennych przychodów --}}
        <div class="bg-white shadow rounded-lg p-4 sm:p-6 mb-8">
            <h3 class="text-lg sm:text-xl font-semibold mb-4">Przychód wg dni</h3>
            <div class="w-full overflow-x-auto">
                <canvas id="revenueChart" class="w-full h-64 sm:h-72"></canvas>
            </div>
        </div>

        {{-- 4) Okres --}}
        <div class="mb-4 text-gray-600 text-sm sm:text-base">
            Okres: <span class="font-medium">{{ $startDate->format('Y-m-d') }}</span>
            – <span class="font-medium">{{ $now->format('Y-m-d') }}</span>
        </div>

        {{-- 5) Tabela wypożyczeń --}}
        <div class="bg-white shadow rounded-lg p-4 sm:p-6 mb-8 overflow-x-auto">
            <h3 class="text-lg sm:text-xl font-semibold mb-4">Szczegóły wypożyczeń (ostatnie 30 dni)</h3>
            <table class="min-w-full table-auto text-sm sm:text-base">
                <thead>
                <tr class="bg-gray-200 text-gray-700">
                    <th class="px-2 sm:px-4 py-2 text-left">
                        <a href="{{ route('admin.dashboard', array_merge(request()->query(), ['sort' => 'date', 'direction' => ($sort === 'date' && $direction === 'asc') ? 'desc' : 'asc'])) }}" class="flex items-center">
                            <span>Data</span>
                            @if($sort === 'date')
                                @if($direction === 'asc')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                @endif
                            @endif
                        </a>
                    </th>
                    <th class="px-2 sm:px-4 py-2 text-left">
                        <a href="{{ route('admin.dashboard', array_merge(request()->query(), ['sort' => 'equipment_name', 'direction' => ($sort === 'equipment_name' && $direction === 'asc') ? 'desc' : 'asc'])) }}" class="flex items-center">
                            <span>Sprzęt</span>
                            @if($sort === 'equipment_name')
                                @if($direction === 'asc')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                @endif
                            @endif
                        </a>
                    </th>
                    <th class="px-2 sm:px-4 py-2 text-left">
                        <a href="{{ route('admin.dashboard', array_merge(request()->query(), ['sort' => 'user_name', 'direction' => ($sort === 'user_name' && $direction === 'asc') ? 'desc' : 'asc'])) }}" class="flex items-center">
                            <span>Użytkownik</span>
                            @if($sort === 'user_name')
                                @if($direction === 'asc')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                @endif
                            @endif
                        </a>
                    </th>
                    <th class="px-2 sm:px-4 py-2 text-right">
                        <a href="{{ route('admin.dashboard', array_merge(request()->query(), ['sort' => 'brutto', 'direction' => ($sort === 'brutto' && $direction === 'asc') ? 'desc' : 'asc'])) }}" class="flex justify-end items-center">
                            <span>Brutto (zł)</span>
                            @if($sort === 'brutto')
                                @if($direction === 'asc')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                @endif
                            @endif
                        </a>
                    </th>
                    <th class="px-2 sm:px-4 py-2 text-right">
                        <a href="{{ route('admin.dashboard', array_merge(request()->query(), ['sort' => 'operator_cost', 'direction' => ($sort === 'operator_cost' && $direction === 'asc') ? 'desc' : 'asc'])) }}" class="flex justify-end items-center">
                            <span>Koszt operatora (zł)</span>
                            @if($sort === 'operator_cost')
                                @if($direction === 'asc')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                @endif
                            @endif
                        </a>
                    </th>
                    <th class="px-2 sm:px-4 py-2 text-right">
                        <a href="{{ route('admin.dashboard', array_merge(request()->query(), ['sort' => 'netto', 'direction' => ($sort === 'netto' && $direction === 'asc') ? 'desc' : 'asc'])) }}" class="flex justify-end items-center">
                            <span>Netto (zł)</span>
                            @if($sort === 'netto')
                                @if($direction === 'asc')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                @endif
                            @endif
                        </a>
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach($rentalDetails as $row)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-2 sm:px-4 py-2">{{ $row['date'] }}</td>
                        <td class="px-2 sm:px-4 py-2">{{ Str::limit($row['equipment_name'], 20) }}</td>
                        <td class="px-2 sm:px-4 py-2">{{ Str::limit($row['user_name'], 20) }}</td>
                        <td class="px-2 sm:px-4 py-2 text-right">{{ number_format($row['brutto'], 2, ',', ' ') }}</td>
                        <td class="px-2 sm:px-4 py-2 text-right">{{ number_format($row['operator_cost'], 2, ',', ' ') }}</td>
                        <td class="px-2 sm:px-4 py-2 text-right">{{ number_format($row['netto'], 2, ',', ' ') }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            {{-- Paginacja --}}
            <div class="mt-4">
                {{ $rentalDetails->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

    {{-- Skrypt Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('revenueChart').getContext('2d');

            const labels = {!! json_encode($dailySales->pluck('date')) !!};
            const dataTotals = {!! json_encode($dailySales->pluck('total')) !!};

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Przychód (zł)',
                        data: dataTotals,
                        fill: true,
                        tension: 0.2,
                        borderColor: '#f56600',
                        backgroundColor: 'rgba(245, 102, 0, 0.1)',
                        pointBackgroundColor: '#f56600',
                        pointRadius: 4,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Data'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Przychód [zł]'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        });
    </script>
@endsection
