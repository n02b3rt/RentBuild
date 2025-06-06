@extends('layouts.app')

@section('content')
    <section class="max-w-[1140px] mx-auto px-4 py-12">
        <h1 class="text-3xl font-bold mb-4">Podsumowanie wypożyczenia</h1>

        <div class="bg-white p-6 rounded shadow grid grid-cols-3 gap-6">
            <div class="col-span-1 flex items-start justify-center">
                <img
                    src="/{{ $equipment->thumbnail }}"
                    alt="{{ $equipment->name }}"
                    class="h-64 object-contain rounded"
                >
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

                <form action="{{ route('client.rentals.store') }}" method="POST" class="mt-4 max-w-full">
                    @csrf
                    <input type="hidden" name="equipment_id" value="{{ $equipment->id }}">

                    {{-- Ukryte pola, wypełniane przez JavaScript --}}
                    <input type="hidden" name="discount_percent" id="discount_percent" value="0">
                    <input type="hidden" name="discount_amount" id="discount_amount" value="0">
                    <input type="hidden" name="discounted_total" id="discounted_total" value="0">

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

                    {{-- Wyświetlenie kwoty przed i po rabacie --}}
                    <p id="total_price" class="font-semibold text-lg mb-4 hidden"></p>

                    <button
                        type="submit"
                        class="bg-[#f56600] hover:bg-[#f98800] text-white font-semibold py-2 px-6 rounded w-full"
                    >
                        Podsumowanie i płatność
                    </button>
                </form>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const startInput        = document.getElementById('start_date');
            const endInput          = document.getElementById('end_date');
            const withOperatorInput = document.getElementById('with_operator');
            const totalPriceEl      = document.getElementById('total_price');

            // 1. Ceny pobrane z backendu:
            const dailyPrice    = {{ $equipment->finalPrice() }};
            const operatorDaily = {{ $equipment->operator_daily_rate }};

            // 2. Liczba zakończonych wypożyczeń (przekazana z PHP):
            const currentRentalsCount = {{ Auth::user()->rentals_count }};

            // 3. Logika zniżek lojalnościowych:
            function getDiscountPercent(rentalsCount) {
                if (rentalsCount === 0) return 20;
                if (((rentalsCount + 1) % 20) === 0) return 50;
                if (((rentalsCount + 1) % 5) === 0) return 25;
                return 0;
            }

            function updateTotalPrice() {
                const startVal = startInput.value;
                const endVal   = endInput.value;

                if (!startVal || !endVal) {
                    totalPriceEl.textContent = '';
                    totalPriceEl.classList.add('hidden');
                    return;
                }

                const start = new Date(startVal);
                const end   = new Date(endVal);

                if (end < start) {
                    totalPriceEl.textContent = '';
                    totalPriceEl.classList.add('hidden');
                    return;
                }

                // 4. Obliczamy liczbę dni:
                const diffTime = end - start;
                const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24)) + 1;

                // 5. Koszty bazowe:
                const baseCost     = parseFloat((diffDays * dailyPrice).toFixed(2));
                const operatorCost = withOperatorInput.checked
                    ? parseFloat((diffDays * operatorDaily).toFixed(2))
                    : 0;
                const rawTotal     = parseFloat((baseCost + operatorCost).toFixed(2));

                // 6. Rabat lojalnościowy:
                const discountPercent = getDiscountPercent(currentRentalsCount);
                const discountAmount  = parseFloat((rawTotal * (discountPercent / 100)).toFixed(2));
                const discountedTotal = parseFloat((rawTotal - discountAmount).toFixed(2));

                // 7. Przygotowujemy breakdown:
                const breakdownLines = [
                    `${diffDays} dni × ${dailyPrice.toFixed(2).replace('.', ',')} zł = ${baseCost.toFixed(2).replace('.', ',')} zł`
                ];
                if (withOperatorInput.checked) {
                    breakdownLines.push(
                        `${diffDays} dni × ${operatorDaily.toFixed(2).replace('.', ',')} zł = ${operatorCost.toFixed(2).replace('.', ',')} zł`
                    );
                }

                // 8. Składamy HTML:
                let html = `
                    Cena przed rabatem: <strong>${rawTotal.toFixed(2).replace('.', ',')} zł</strong><br>
                    <small>${breakdownLines.join('<br>')}</small>
                `;

                if (discountPercent > 0) {
                    html += `
                        <div class="mt-2 p-3 bg-green-50 border border-green-200 rounded">
                            <p>
                                <strong>Zniżka lojalnościowa:</strong>
                                To będzie Twoje <strong>${currentRentalsCount + 1}.</strong> wypożyczenie,
                                otrzymujesz <strong>–${discountPercent}%</strong>.
                            </p>
                            <p><strong>Kwota rabatu:</strong> –${discountAmount.toFixed(2).replace('.', ',')} zł</p>
                            <p><strong>Do zapłaty po rabacie:</strong>
                                <span class="text-red-600 font-bold">${discountedTotal.toFixed(2).replace('.', ',')} zł</span>
                            </p>
                        </div>
                    `;
                } else {
                    html += `
                        <p class="mt-2 text-sm text-gray-600">
                            Brak zniżki lojalnościowej dla tego wypożyczenia.
                        </p>
                    `;
                }

                totalPriceEl.innerHTML = html;
                totalPriceEl.classList.remove('hidden');

                // 9. Ustawiamy wartości w ukrytych polach:
                document.getElementById('discount_percent').value = discountPercent;
                document.getElementById('discount_amount').value  = discountAmount.toFixed(2);
                document.getElementById('discounted_total').value = discountedTotal.toFixed(2);
            }

            // 10. Nasłuchujemy na zmiany pól:
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
