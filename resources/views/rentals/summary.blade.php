@extends('layouts.app')

@section('content')
    <section class="max-w-[1140px] mx-auto px-4 py-12">
        <h1 class="text-3xl font-bold mb-4">Podsumowanie wypożyczenia</h1>

        <div class="bg-white p-6 rounded shadow grid grid-cols-3 gap-6">
            <div class="col-span-1 flex flex-col items-center space-y-6">
                <img src="/{{ $equipment->thumbnail }}" alt="{{ $equipment->name }}" class="h-64 object-contain rounded">
            </div>

            <div class="col-span-2">
                <h2 class="text-2xl font-semibold mb-2">{{ $equipment->name }}</h2>
                <p><strong>Cena za dzień:</strong> {{ number_format($equipment->finalPrice(), 2, ',', ' ') }} zł</p>
                <p><strong>Dostępność:</strong> {{ ucfirst($equipment->availability) }}</p>
                <p><strong>Stan techniczny:</strong> {{ ucfirst($equipment->technical_state) }}</p>

                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('client.rentals.store') }}" method="POST" class="mt-6" id="rental-form" novalidate>
                    @csrf
                    <input type="hidden" name="equipment_id" value="{{ $equipment->id }}">

                    <div class="flex gap-6">
                        <div class="w-1/2">
                            <label for="start_date" class="block font-medium mb-1">Data rozpoczęcia:</label>
                            <input
                                type="date"
                                name="start_date"
                                id="start_date"
                                required
                                min="{{ now()->toDateString() }}"
                                class="border p-2 rounded block mb-3 w-full"
                                value="{{ old('start_date') }}"
                            >

                            <label for="end_date" class="block font-medium mb-1">Data zakończenia:</label>
                            <input
                                type="date"
                                name="end_date"
                                id="end_date"
                                required
                                min="{{ now()->toDateString() }}"
                                class="border p-2 rounded block mb-4 w-full"
                                value="{{ old('end_date') }}"
                            >

                            <div class="mb-4">
                                <input
                                    type="checkbox"
                                    name="with_operator"
                                    id="with_operator"
                                    value="1"
                                    class="mr-2"
                                    {{ old('with_operator') ? 'checked' : '' }}
                                >
                                <label for="with_operator" class="font-medium">Potrzebuję operatora</label>
                            </div>

                            <p id="total_price" class="font-semibold text-lg mb-4 hidden"></p>
                        </div>

                        <div class="w-1/2">
                            <label for="notes" class="block font-medium mb-1">Notatki do wypożyczenia (opcjonalnie):</label>
                            <textarea
                                name="notes"
                                id="notes"
                                rows="7"
                                class="border border-gray-300 p-3 rounded-md block w-full
                                   focus:outline-none focus:ring-2 focus:ring-[#f56600] focus:border-[#f56600]
                                   resize-none placeholder-gray-400 shadow-sm"
                                placeholder="Np. szczególne uwagi, preferencje..."
                            >{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <button type="submit"
                            class="mt-6 bg-[#f56600] hover:bg-[#f98800] text-white font-semibold py-2 px-6 rounded w-full">
                        Podsumowanie i płatność
                    </button>
                </form>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const startInput       = document.getElementById('start_date');
            const endInput         = document.getElementById('end_date');
            const withOperatorInput= document.getElementById('with_operator');
            const totalPriceEl     = document.getElementById('total_price');

            const dailyPrice    = {{ $equipment->finalPrice() }};
            const operatorDaily = {{ $equipment->operator_daily_rate }};

            function updateTotalPrice() {
                const start = new Date(startInput.value);
                const end   = new Date(endInput.value);

                if (startInput.value && endInput.value && end >= start) {
                    const diffTime     = end - start;
                    const diffDays     = Math.floor(diffTime / (1000 * 60 * 60 * 24)) + 1;
                    const baseCost     = diffDays * dailyPrice;
                    const operatorCost = withOperatorInput.checked ? diffDays * operatorDaily : 0;
                    const total = (baseCost + operatorCost).toFixed(2);

                    const breakdown = [
                        `${diffDays} dni × ${dailyPrice.toFixed(2).replace('.', ',')} zł = ${baseCost.toFixed(2).replace('.', ',')} zł`,
                    ];

                    if (withOperatorInput.checked) {
                        breakdown.push(
                            `${diffDays} dni × ${operatorDaily.toFixed(2).replace('.', ',')} zł = ${operatorCost.toFixed(2).replace('.', ',')} zł`
                        );
                    }

                    totalPriceEl.innerHTML = `
                Cena całkowita: <strong>${total.replace('.', ',')} zł</strong><br>
                <small>${breakdown.join('<br>')}</small>
            `;
                    totalPriceEl.classList.remove('hidden');
                } else {
                    totalPriceEl.textContent = '';
                    totalPriceEl.classList.add('hidden');
                }
            }

            startInput.addEventListener('change', () => {
                if (startInput.value) {
                    endInput.min = startInput.value;
                    if (endInput.value < startInput.value) {
                        endInput.value = startInput.value;
                    }
                }
                updateTotalPrice();
            });

            endInput.addEventListener('change', () => {
                if (startInput.value && endInput.value && endInput.value < startInput.value) {
                    endInput.value = startInput.value;
                }
                updateTotalPrice();
            });

            withOperatorInput.addEventListener('change', updateTotalPrice);

            updateTotalPrice();
        });
    </script>
@endsection
