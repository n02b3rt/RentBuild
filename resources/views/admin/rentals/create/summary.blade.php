@extends('layouts.admin')

@section('admin-content')
    <div class="container mx-auto px-4 py-6 max-w-lg">
        <h1 class="text-3xl font-bold mb-6">Podsumowanie wypożyczenia</h1>

        <div class="mb-4">
            <h2 class="text-xl font-semibold">Użytkownik:</h2>
            <p>{{ $user->first_name }} {{ $user->last_name }} ({{ $user->email }})</p>
        </div>

        <div class="mb-4">
            <h2 class="text-xl font-semibold">Wybrany sprzęt:</h2>
            <p>{{ $equipment->name }}</p>
            <p>Cena za dzień: {{ number_format($equipment->rental_price, 2, ',', ' ') }} zł</p>
            @if($equipment->isPromotionActive())
                <p class="text-red-600">Promocja: {{ $equipment->discount }}%</p>
                <p class="text-2xl font-bold">Cena po rabacie: {{ number_format($equipment->finalPrice(), 2, ',', ' ') }} zł</p>
            @endif
        </div>

        <form method="POST" action="{{ route('admin.rentals.create.finalize') }}" id="rental-form">
            @csrf
            <input type="hidden" name="user_id" value="{{ $user->id }}">
            <input type="hidden" name="equipment_id" value="{{ $equipment->id }}">

            <div class="mb-4">
                <label for="start_date" class="block font-semibold mb-1">Data rozpoczęcia wypożyczenia:</label>
                <input
                    type="date"
                    id="start_date"
                    name="start_date"
                    class="border p-2 rounded w-full"
                    required
                    min="{{ date('Y-m-d') }}"
                    value="{{ old('start_date') }}"
                >
            </div>

            <div class="mb-4">
                <label for="end_date" class="block font-semibold mb-1">Data zakończenia wypożyczenia:</label>
                <input
                    type="date"
                    id="end_date"
                    name="end_date"
                    class="border p-2 rounded w-full"
                    required
                    min="{{ date('Y-m-d') }}"
                    value="{{ old('end_date') }}"
                >
            </div>

            <div class="mb-4 flex items-center">
                <input
                    type="checkbox"
                    name="with_operator"
                    id="with_operator"
                    value="1"
                    class="mr-2"
                    {{ old('with_operator') ? 'checked' : '' }}
                >
                <label for="with_operator" class="font-semibold cursor-pointer">Potrzebuję operatora</label>
            </div>

            <div class="mb-4">
                <label for="notes" class="block font-semibold mb-1">Notatki do wypożyczenia (opcjonalnie):</label>
                <textarea
                    name="notes"
                    id="notes"
                    rows="5"
                    class="border border-gray-300 p-3 rounded-md block w-full resize-none placeholder-gray-400 shadow-sm
                           focus:outline-none focus:ring-2 focus:ring-[#f56600] focus:border-[#f56600]"
                    placeholder="Np. szczególne uwagi, preferencje..."
                >{{ old('notes') }}</textarea>
            </div>

            <p id="total_price" class="font-semibold text-lg mb-6 hidden"></p>

            <button type="submit" class="bg-[#f56600] hover:bg-orange-600 text-white font-semibold py-2 px-6 rounded w-full">
                Przejdź do płatności
            </button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const startInput = document.getElementById('start_date');
            const endInput = document.getElementById('end_date');
            const withOperatorInput = document.getElementById('with_operator');
            const totalPriceEl = document.getElementById('total_price');

            const dailyPrice = {{ $equipment->finalPrice() }};
            const operatorDaily = {{ $equipment->operator_daily_rate ?? 0 }};

            function updateTotalPrice() {
                const start = new Date(startInput.value);
                const end = new Date(endInput.value);

                if (startInput.value && endInput.value && end >= start) {
                    const diffTime = end - start;
                    const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24)) + 1;

                    const baseCost = diffDays * dailyPrice;
                    const operatorCost = withOperatorInput.checked ? diffDays * operatorDaily : 0;
                    const total = (baseCost + operatorCost).toFixed(2);

                    let breakdown = `${diffDays} dni × ${dailyPrice.toFixed(2).replace('.', ',')} zł = ${baseCost.toFixed(2).replace('.', ',')} zł`;
                    if (withOperatorInput.checked) {
                        breakdown += `<br>${diffDays} dni × ${operatorDaily.toFixed(2).replace('.', ',')} zł = ${operatorCost.toFixed(2).replace('.', ',')} zł`;
                    }

                    totalPriceEl.innerHTML = `
                        Cena całkowita: <strong>${total.replace('.', ',')} zł</strong><br>
                        <small>${breakdown}</small>
                    `;
                    totalPriceEl.classList.remove('hidden');
                } else {
                    totalPriceEl.textContent = '';
                    totalPriceEl.classList.add('hidden');
                }
            }

            startInput.addEventListener('change', () => {
                if (endInput.value < startInput.value) {
                    endInput.value = startInput.value;
                }
                endInput.min = startInput.value;
                updateTotalPrice();
            });

            endInput.addEventListener('change', updateTotalPrice);
            withOperatorInput.addEventListener('change', updateTotalPrice);

            // Inicjalne wyliczenie (np. przy błędzie walidacji i powrocie starej wartości)
            updateTotalPrice();
        });
    </script>
@endsection
