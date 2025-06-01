@extends('layouts.app')

@section('content')
    <section class="max-w-[1140px] mx-auto px-4 py-12">
        <h1 class="text-3xl font-bold mb-4">Podsumowanie wypożyczenia</h1>

        <div class="bg-white p-6 rounded shadow grid grid-cols-3 gap-6">
            <div class="col-span-1 flex items-start justify-center">
                <img src="/{{ $equipment->thumbnail }}" alt="{{ $equipment->name }}" class="h-64 object-contain rounded">
            </div>

            <div class="col-span-2">
                <h2 class="text-2xl font-semibold mb-2">{{ $equipment->name }}</h2>
                <p><strong>Cena za dzień:</strong> {{ number_format($equipment->finalPrice(), 2, ',', ' ') }} zł</p>
                <p><strong>Dostępność:</strong> {{ ucfirst($equipment->availability) }}</p>
                <p><strong>Stan techniczny:</strong> {{ ucfirst($equipment->technical_state) }}</p>
                <p class="mb-4"><strong>Opis:</strong> {{ $equipment->description }}</p>

                <form action="{{ route('client.rentals.store') }}" method="POST" class="mt-4 max-w-60">
                    @csrf
                    <input type="hidden" name="equipment_id" value="{{ $equipment->id }}">

                    <label for="start_date" class="block font-medium mb-1">Data rozpoczęcia:</label>
                    <input
                        type="date"
                        name="start_date"
                        id="start_date"
                        required
                        min="{{ now()->toDateString() }}"
                        class="border p-2 rounded block mb-3 w-full"
                    >

                    <label for="end_date" class="block font-medium mb-1">Data zakończenia:</label>
                    <input
                        type="date"
                        name="end_date"
                        id="end_date"
                        required
                        min="{{ now()->toDateString() }}"
                        class="border p-2 rounded block mb-4 w-full"
                    >

                    <div class="mb-4">
                        <input
                            type="checkbox"
                            name="with_operator"
                            id="with_operator"
                            value="1"
                            class="mr-2"
                        >
                        <label for="with_operator" class="font-medium">Potrzebuję operatora</label>
                    </div>

                    <p id="total_price" class="font-semibold text-lg mb-4 hidden"></p>

                    <button type="submit"
                            class="bg-[#f56600] hover:bg-[#f98800] text-white font-semibold py-2 px-6 rounded w-full">
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

            // cena sprzętu i stawka operatora pobrane z bazy
            const dailyPrice    = {{ $equipment->finalPrice() }};
            const operatorDaily = {{ $equipment->operator_daily_rate }};

            function updateTotalPrice() {
                const start = new Date(startInput.value);
                const end   = new Date(endInput.value);

                if (start && end && end >= start) {
                    const diffTime     = end - start;
                    const diffDays     = Math.floor(diffTime / (1000 * 60 * 60 * 24)) + 1;
                    const baseCost     = diffDays * dailyPrice;
                    const operatorCost = withOperatorInput.checked
                        ? diffDays * operatorDaily
                        : 0;
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
            endInput.addEventListener('change', updateTotalPrice);
            withOperatorInput.addEventListener('change', updateTotalPrice);
        });
    </script>
@endsection
