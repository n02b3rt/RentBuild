@extends('layouts.admin')

@section('admin-content')
    <div class="container mx-auto py-8">
        <h1 class="text-3xl font-bold mb-8 text-[#f56600]">
            Raport finansowy – ostatnie 30 dni
        </h1>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
            <div class="bg-green-100 border border-green-300 rounded-xl p-6">
                <h2 class="text-lg font-semibold text-green-800">Łączny przychód</h2>
                <p class="text-3xl font-bold mt-3 text-green-700">
                    {{ number_format($totalRevenue, 2, ',', ' ') }} zł
                </p>
            </div>

            <div class="bg-blue-100 border border-blue-300 rounded-xl p-6">
                <h2 class="text-lg font-semibold text-blue-800">Liczba wypożyczeń</h2>
                <p class="text-3xl font-bold mt-3 text-blue-700">
                    {{ $totalRentals }}
                </p>
            </div>

            <div class="bg-orange-100 border border-orange-300 rounded-xl p-6">
                <h2 class="text-lg font-semibold text-orange-800">Średnia wartość</h2>
                <p class="text-3xl font-bold mt-3 text-orange-700">
                    {{ number_format($averageRental, 2, ',', ' ') }} zł
                </p>
            </div>

            <div class="bg-red-100 border border-red-300 rounded-xl p-6">
                <h2 class="text-lg font-semibold text-red-800">Koszt operatorów</h2>
                <p class="text-3xl font-bold mt-3 text-red-700">
                    {{ number_format($operatorCost, 2, ',', ' ') }} zł
                </p>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6 mb-10">
            <h3 class="text-xl font-semibold mb-4">Przychód wg dni</h3>
            <canvas id="revenueChart" height="100"></canvas>
        </div>

        <div class="mb-6 text-gray-600 text-sm">
            Okres: <span class="font-medium">{{ $startDate->format('Y-m-d') }}</span> – <span class="font-medium">{{ $now->format('Y-m-d') }}</span>
        </div>

        <div class="bg-white shadow rounded-lg p-6 mb-10 overflow-x-auto">
            <h3 class="text-xl font-semibold mb-4">Szczegóły wypożyczeń (ostatnie 30 dni)</h3>
            <table class="min-w-full table-auto">
                <thead>
                <tr class="bg-gray-200 text-gray-700">
                    <th class="px-4 py-2 text-left">
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

                    {{-- Sprzęt --}}
                    <th class="px-4 py-2 text-left">
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

                    {{-- Użytkownik --}}
                    <th class="px-4 py-2 text-left">
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

                    {{-- Brutto --}}
                    <th class="px-4 py-2 text-right">
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

                    {{-- Koszt operatora --}}
                    <th class="px-4 py-2 text-right">
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

                    {{-- Netto --}}
                    <th class="px-4 py-2 text-right">
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
                        <td class="px-4 py-3">{{ $row['date'] }}</td>
                        <td class="px-4 py-3">{{ $row['equipment_name'] }}</td>
                        <td class="px-4 py-3">{{ $row['user_name'] }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($row['brutto'], 2, ',', ' ') }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($row['operator_cost'], 2, ',', ' ') }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($row['netto'], 2, ',', ' ') }}</td>
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
