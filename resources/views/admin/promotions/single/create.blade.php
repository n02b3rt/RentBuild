@extends('layouts.admin')

@section('admin-content')
    <div class="container mx-auto px-4 py-6 max-w-lg relative">
        <h1 class="text-3xl font-semibold mb-6">Dodaj promocję pojedynczą</h1>

        <form action="{{ route('admin.promotions.single.store') }}" method="POST" class="space-y-6" novalidate>
            @csrf

            {{-- Wyszukiwarka sprzętu --}}
            <div class="relative">
                <label for="equipment_search" class="block mb-2 font-medium text-gray-700">Wybierz sprzęt</label>
                <input
                    type="text"
                    id="equipment_search"
                    name="equipment_search"
                    placeholder="Wpisz nazwę sprzętu..."
                    autocomplete="off"
                    class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-[#f56600]"
                    value="{{ old('equipment_search') }}"
                    required
                >
                <input type="hidden" id="equipment_id" name="equipment_id" value="{{ old('equipment_id') }}">

                @error('equipment_id')
                <p class="mt-1 text-red-600 text-sm">{{ $message }}</p>
                @enderror

                <ul id="equipment_list" class="absolute left-0 right-0 border border-gray-300 rounded mt-1 max-h-48 overflow-y-auto bg-white z-50 hidden"></ul>
            </div>

            {{-- Rabat --}}
            <div>
                <label for="discount" class="block mb-2 font-medium text-gray-700">Rabat (%)</label>
                <input
                    type="number"
                    id="discount"
                    name="discount"
                    min="1"
                    max="100"
                    class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-[#f56600]"
                    value="{{ old('discount') }}"
                    required
                >
                @error('discount')
                <p class="mt-1 text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>

            {{-- Checkbox daty --}}
            <div class="flex items-center space-x-2">
                <input type="checkbox" class="form-checkbox h-5 w-5 text-[#f56600]" name="has_dates" id="has_dates" value="1" {{ old('has_dates') ? 'checked' : '' }}>
                <label for="has_dates" class="font-medium text-gray-700">Ustaw datę promocji</label>
            </div>

            {{-- Pola datetime-local --}}
            <div id="date_fields" class="{{ old('has_dates') ? '' : 'hidden' }}">
                <div class="mb-4">
                    <label for="start_datetime" class="block mb-2 font-medium text-gray-700">Data i godzina rozpoczęcia</label>
                    <input
                        type="datetime-local"
                        id="start_datetime"
                        name="start_datetime"
                        class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-[#f56600]"
                        value="{{ old('start_datetime') }}"
                        min="{{ now()->format('Y-m-d\TH:i') }}"
                        {{ old('has_dates') ? 'required' : '' }}
                    >
                    @error('start_datetime')
                    <p class="mt-1 text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="end_datetime" class="block mb-2 font-medium text-gray-700">Data i godzina zakończenia</label>
                    <input
                        type="datetime-local"
                        id="end_datetime"
                        name="end_datetime"
                        class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-[#f56600]"
                        value="{{ old('end_datetime') }}"
                        min="{{ now()->format('Y-m-d\TH:i') }}"
                        {{ old('has_dates') ? 'required' : '' }}
                    >
                    @error('end_datetime')
                    <p class="mt-1 text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Przycisk --}}
            <button
                type="submit"
                class="w-full py-3 bg-[#f56600] text-white font-semibold rounded"
            >
                Dodaj promocję
            </button>
        </form>
    </div>

    <script>
        // Pokazywanie/ukrywanie pól datetime-local
        document.getElementById('has_dates').addEventListener('change', function() {
            const dateFields = document.getElementById('date_fields');
            if (this.checked) {
                dateFields.classList.remove('hidden');
            } else {
                dateFields.classList.add('hidden');
            }
        });

        // Wyszukiwarka sprzętu z podpowiedziami
        const equipmentInput = document.getElementById('equipment_search');
        const equipmentIdInput = document.getElementById('equipment_id');
        const equipmentList = document.getElementById('equipment_list');

        const equipmentData = @json($equipment->map(function($e) {
        return ['id' => $e->id, 'name' => $e->name];
    }));

        equipmentInput.addEventListener('input', () => {
            const val = equipmentInput.value.toLowerCase().trim();
            equipmentList.innerHTML = '';
            equipmentIdInput.value = '';

            if (val.length === 0) {
                equipmentList.classList.add('hidden');
                return;
            }

            const filtered = equipmentData.filter(e => e.name.toLowerCase().includes(val));

            if (filtered.length === 0) {
                equipmentList.classList.add('hidden');
                return;
            }

            filtered.forEach(item => {
                const li = document.createElement('li');
                li.textContent = `ID: ${item.id} - ${item.name}`;
                li.className = 'cursor-pointer px-3 py-2 hover:bg-indigo-100';
                li.addEventListener('click', () => {
                    equipmentInput.value = item.name;
                    equipmentIdInput.value = item.id;
                    equipmentList.classList.add('hidden');
                });
                equipmentList.appendChild(li);
            });

            equipmentList.classList.remove('hidden');
        });

        // Zamknięcie listy podpowiedzi po kliknięciu poza polem
        document.addEventListener('click', (e) => {
            if (!equipmentInput.contains(e.target) && !equipmentList.contains(e.target)) {
                equipmentList.classList.add('hidden');
            }
        });

        // Walidacja dat — start nie może być w przeszłości, koniec musi być po starcie
        function validateDateTimeInputs() {
            const startInput = document.getElementById('start_datetime');
            const endInput = document.getElementById('end_datetime');
            const now = new Date();

            function checkStart() {
                if (!startInput.value) return;
                const startDate = new Date(startInput.value);
                if (startDate < now) {
                    alert('Data i godzina rozpoczęcia nie mogą być w przeszłości!');
                    startInput.value = now.toISOString().slice(0,16);
                }
            }

            function checkEnd() {
                if (!endInput.value || !startInput.value) return;
                const startDate = new Date(startInput.value);
                const endDate = new Date(endInput.value);
                if (endDate <= startDate) {
                    alert('Data i godzina zakończenia muszą być po dacie i godzinie rozpoczęcia!');
                    const newEnd = new Date(startDate.getTime() + 60*60*1000);
                    endInput.value = newEnd.toISOString().slice(0,16);
                }
            }

            startInput.addEventListener('change', () => {
                checkStart();
                checkEnd();
            });
            endInput.addEventListener('change', checkEnd);
        }

        document.addEventListener('DOMContentLoaded', validateDateTimeInputs);
    </script>
@endsection
