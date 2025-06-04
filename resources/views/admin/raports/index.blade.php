@extends('layouts.admin')

@section('admin-content')
    <h1 class="text-3xl font-bold mb-8 text-[#f56600]">Raport wypożyczeń – ostatnie 30 dni</h1>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
        <div class="bg-green-100 border border-green-300 rounded-xl p-4">
            <h2 class="text-lg font-semibold text-green-800">Łączny przychód</h2>
            <p class="text-2xl font-bold mt-2">{{ number_format($totalRevenue, 2) }} zł</p>
        </div>
        <div class="bg-blue-100 border border-blue-300 rounded-xl p-4">
            <h2 class="text-lg font-semibold text-blue-800">Liczba wypożyczeń</h2>
            <p class="text-2xl font-bold mt-2">{{ $totalRentals }}</p>
        </div>
        <div class="bg-orange-100 border border-orange-300 rounded-xl p-4">
            <h2 class="text-lg font-semibold text-orange-800">Średnia wartość</h2>
            <p class="text-2xl font-bold mt-2">{{ number_format($averageRental, 2) }} zł</p>
        </div>
        <div class="bg-red-100 border border-red-300 rounded-xl p-4">
            <h2 class="text-lg font-semibold text-red-800">Straty z reklamacji</h2>
            <p class="text-2xl font-bold mt-2">{{ number_format($totalComplaintLosses, 2) }} zł</p>
        </div>
    </div>

    @if ($topUser)
        <div class="bg-gray-100 border border-gray-300 rounded-xl p-4 mb-8">
            <h2 class="text-lg font-semibold text-gray-800">Najlepszy klient</h2>
            <p class="mt-2 text-xl font-medium">{{ $topUser['user'] }}</p>
            <p class="text-gray-600">Suma wypożyczeń: {{ number_format($topUser['total'], 2) }} zł</p>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
        <div>
            <h2 class="text-xl font-semibold text-[#f56600] mb-4">Najczęściej wypożyczane sprzęty</h2>
            <ul class="bg-white border border-gray-300 rounded-lg divide-y divide-gray-200">
                @foreach ($topEquipment as $item)
                    <li class="flex justify-between px-4 py-2 hover:bg-gray-50">
                        <span>{{ $item['equipment'] }}</span>
                        <span class="font-semibold text-gray-600">{{ $item['count'] }} razy</span>
                    </li>
                @endforeach
                @if ($topEquipment->isEmpty())
                    <li class="px-4 py-2 italic text-gray-600">Brak danych</li>
                @endif
            </ul>
        </div>

        <div>
            <h2 class="text-xl font-semibold text-[#f56600] mb-4">Najczęściej reklamowane sprzęty</h2>
            <ul class="bg-white border border-gray-300 rounded-lg divide-y divide-gray-200">
                @forelse ($topComplainedEquipment as $item)
                    <li class="flex justify-between items-center px-4 py-2 hover:bg-gray-50">
                        <div>
                            <span class="font-medium">{{ $item['equipment'] }}</span>
                            <span class="text-gray-500 text-sm ml-2">({{ $item['count'] }} reklamacji)</span>
                        </div>
                        <span class="font-semibold text-red-600">{{ number_format($item['loss'], 2) }} zł</span>
                    </li>
                @empty
                    <li class="px-4 py-2 italic text-gray-600">
                        Brak reklamowanych urządzeń w ciągu ostatnich 30 dni.
                    </li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="mb-10">
        <h2 class="text-xl font-semibold text-[#f56600] mb-2">Przychód dzienny</h2>
        <p class="text-gray-700 mb-4">
            Średnio: <span class="font-semibold">{{ number_format($averageDailyRevenue, 2) }} zł</span>,
            Maksimum: <span class="font-semibold">{{ number_format($maxDailyRevenue, 2) }} zł</span> ({{ $maxDailyRevenueDate }})
        </p>
        <canvas id="salesChart" height="80"></canvas>
    </div>

    <div class="mb-10">
        <h2 class="text-xl font-semibold text-[#f56600] mb-2">Liczba wypożyczeń dziennie</h2>
        <p class="text-gray-700 mb-4">
            Średnio: <span class="font-semibold">{{ number_format($averageDailyCount, 1) }}</span>,
            Maksimum: <span class="font-semibold">{{ $maxDailyCount }}</span> ({{ $maxDailyCountDate }})
        </p>
        <canvas id="countChart" height="60"></canvas>
    </div>

    <div class="mb-10">
        <h2 class="text-xl font-semibold text-[#f56600] mb-2">Reklamacje vs pozostałe</h2>
        <p class="text-gray-700 mb-4">
            {{ $complaintChartSummary }}
        </p>
        <canvas id="complaintChart" height="60"></canvas>
    </div>

    <div class="mb-10">
        <h2 class="text-xl font-semibold text-[#f56600] mb-2">Rozkład statusów wypożyczeń</h2>
        <p class="text-gray-700 mb-4">
            Najczęściej: <span class="font-semibold">{{ $statusChartSummary }}</span>
        </p>
        <canvas id="statusChart" height="80"></canvas>
    </div>

    <div class="mb-10">
        <h2 class="text-xl font-semibold text-[#f56600] mb-2">Reklamacje w topowanych urządzeniach</h2>
        <p class="text-gray-700 mb-4">
            (zestawienie liczby reklamacji dla każdego z top 5 najczęściej reklamowanych)
        </p>
        <canvas id="complainedEquipChart" height="80"></canvas>
    </div>

    <h2 class="text-xl font-semibold text-[#f56600] mt-10 mb-4">Lista wypożyczeń</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full border border-gray-300 rounded-lg divide-y divide-gray-200 text-sm">
            <thead class="bg-[#f56600] text-white">
            <tr>
                <th class="px-4 py-3 text-left">Data</th>
                <th class="px-4 py-3 text-left">Użytkownik</th>
                <th class="px-4 py-3 text-left">Sprzęt</th>
                <th class="px-4 py-3 text-left">Kwota</th>
                <th class="px-4 py-3 text-left">Status</th>
            </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($rentals as $rental)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">{{ $rental->created_at->format('Y-m-d') }}</td>
                    <td class="px-4 py-3">{{ $rental->user->name ?? 'Brak' }}</td>
                    <td class="px-4 py-3">{{ $rental->equipment->name ?? 'Brak' }}</td>
                    <td class="px-4 py-3">{{ number_format($rental->total_price, 2) }} zł</td>
                    <td class="px-4 py-3 capitalize">
                        {{ str_replace('_', ' ', $rental->status) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center italic py-4 text-gray-600">Brak danych</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const labels        = {!! json_encode($dailySales->pluck('date')) !!};
        const revenue       = {!! json_encode($dailySales->pluck('total')) !!};

        const counts        = {!! json_encode($dailySales->pluck('count')) !!};

        const complaints    = {!! json_encode(array_values($complaintStats)) !!};

        const statusLabels  = {!! json_encode($statusDistribution->keys()) !!};
        const statusCounts  = {!! json_encode($statusDistribution->values()) !!};

        const complainedLabels = {!! json_encode($topComplainedEquipment->pluck('equipment')) !!};
        const complainedCounts = {!! json_encode($topComplainedEquipment->pluck('count')) !!};


        new Chart(document.getElementById('salesChart'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Przychód (zł)',
                    data: revenue,
                    borderColor: '#f56600',
                    backgroundColor: 'rgba(245, 102, 0, 0.1)',
                    tension: 0.2,
                    fill: true
                }]
            }
        });

        new Chart(document.getElementById('countChart'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Liczba wypożyczeń',
                    data: counts,
                    backgroundColor: '#60a5fa'
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        new Chart(document.getElementById('complaintChart'), {
            type: 'pie',
            data: {
                labels: ['Reklamacyjne', 'Pozostałe'],
                datasets: [{
                    data: complaints,
                    backgroundColor: ['#dc2626', '#16a34a']
                }]
            }
        });

        new Chart(document.getElementById('statusChart'), {
            type: 'bar',
            data: {
                labels: statusLabels,
                datasets: [{
                    label: 'Liczba zamówień',
                    data: statusCounts,
                    backgroundColor: '#f56600'
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        new Chart(document.getElementById('complainedEquipChart'), {
            type: 'bar',
            data: {
                labels: complainedLabels,
                datasets: [{
                    label: 'Reklamacje',
                    data: complainedCounts,
                    backgroundColor: '#e11d48'
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Liczba reklamacji' }
                    },
                    x: {
                        title: { display: true, text: 'Urządzenie' }
                    }
                }
            }
        });
    </script>
@endsection
