@extends('layouts.admin')

@section('admin-content')
    <div class="max-w-3xl mx-auto py-8">
        <h1 class="text-2xl font-bold mb-6">Edytuj sprzęt: {{ $equipment->name }}</h1>

        @if ($errors->any())
            <div class="bg-red-100 text-red-800 px-4 py-3 rounded mb-6">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Przygotuj mapę kategorii → stawek --}}
        @php
            use Illuminate\Support\Facades\DB;
            $categoryRates = DB::table('equipment')
                ->select('category', DB::raw('MAX(operator_rate) as rate'))
                ->groupBy('category')
                ->pluck('rate', 'category');
        @endphp

        <form action="{{ route('admin.equipment.update', $equipment->id) }}"
              method="POST"
              enctype="multipart/form-data"
              class="space-y-5">
            @csrf
            @method('PUT')

            {{-- Nazwa --}}
            <div>
                <label class="block font-medium mb-1">Nazwa</label>
                <input type="text"
                       name="name"
                       value="{{ old('name', $equipment->name) }}"
                       required
                       class="w-full rounded-lg border-gray-300 px-4 py-2 shadow-sm">
            </div>

            {{-- Opis --}}
            <div>
                <label class="block font-medium mb-1">Opis</label>
                <textarea name="description"
                          rows="4"
                          required
                          class="w-full rounded-lg border-gray-300 px-4 py-2 shadow-sm">{{ old('description', $equipment->description) }}</textarea>
            </div>

            {{-- Dostępność --}}
            <div>
                <label class="block font-medium mb-1">Dostępność</label>
                <select name="availability" required
                        class="w-full rounded-lg border-gray-300 px-4 py-2 shadow-sm">
                    @foreach (['dostepny','niedostepny','rezerwacja'] as $opt)
                        <option value="{{ $opt }}"
                            {{ old('availability', $equipment->availability) === $opt ? 'selected' : '' }}>
                            {{ ucfirst($opt) }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Cena wynajmu --}}
            <div>
                <label class="block font-medium mb-1">Cena wynajmu (zł)</label>
                <input type="number" name="rental_price" step="0.01"
                       value="{{ old('rental_price', $equipment->rental_price) }}"
                       required
                       class="w-full rounded-lg border-gray-300 px-4 py-2 shadow-sm">
            </div>

            {{-- Miniatura --}}
            <div>
                <label class="block font-medium mb-1">Miniatura (zmień jeśli chcesz)</label>
                <input type="file" name="thumbnail" accept=".webp"
                       class="w-full rounded-lg border-gray-300 px-3 py-2 file:bg-indigo-600 file:text-white file:px-4 file:py-2 file:rounded">
                <p class="text-sm text-gray-500 mt-2">Obecna:</p>
                <img src="{{ asset($equipment->thumbnail) }}"
                     class="h-16 rounded shadow mt-1" alt="miniatura">
            </div>

            {{-- Zdjęcia dodatkowe --}}
            <div>
                <label class="block font-medium mb-1">Dodaj zdjęcia (opcjonalnie)</label>
                <input type="file" name="photos[]" accept=".webp" multiple
                       class="w-full rounded-lg border-gray-300 px-3 py-2 file:bg-indigo-600 file:text-white file:px-4 file:py-2 file:rounded">
            </div>

            {{-- Stan techniczny --}}
            <div>
                <label class="block font-medium mb-1">Stan techniczny</label>
                <select name="technical_state" required
                        class="w-full rounded-lg border-gray-300 px-4 py-2 shadow-sm">
                    @foreach (['nowy','uzywany','naprawa'] as $st)
                        <option value="{{ $st }}"
                            {{ old('technical_state', $equipment->technical_state) === $st ? 'selected' : '' }}>
                            {{ ucfirst($st) }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Kategoria + stawka operatora --}}
            <div>
                <label class="block font-medium mb-1">Kategoria</label>

                <button type="button" id="toggle-category-mode"
                        class="mb-2 inline-flex items-center rounded-lg bg-blue-100 px-3 py-1 text-sm text-blue-700 hover:bg-blue-200">
                    Wybierz z listy
                </button>

                <select id="existing-category"
                        class="w-full hidden rounded-lg border-gray-300 px-4 py-2 mb-2 shadow-sm">
                    <option value="">-- wybierz istniejącą --</option>
                    @foreach($categoryRates->keys() as $cat)
                        <option value="{{ $cat }}"
                            {{ old('category', $equipment->category) === $cat ? 'selected' : '' }}>
                            {{ $cat }}
                        </option>
                    @endforeach
                </select>

                <input type="text" name="category" id="category"
                       placeholder="Wpisz kategorię"
                       value="{{ old('category', $equipment->category) }}"
                       required
                       class="w-full rounded-lg border-gray-300 px-4 py-2 shadow-sm">
            </div>

            <div>
                <label class="block font-medium mb-1">Stawka operatora (zł/dzień)</label>
                <input type="number"
                       name="operator_rate"
                       id="operator_rate"
                       step="0.01"
                       min="0"
                       value="{{ old('operator_rate', $equipment->operator_rate) }}"
                       required
                       class="w-full rounded-lg border-gray-300 px-4 py-2 shadow-sm @error('operator_rate') border-red-500 @enderror">
                @error('operator_rate')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit --}}
            <div class="flex items-center gap-4 pt-4">
                <button type="submit"
                        class="inline-flex items-center rounded-lg bg-green-600 px-5 py-2 text-white hover:bg-green-700">
                    Zapisz zmiany
                </button>
                <a href="{{ route('admin.equipment.index') }}"
                   class="inline-flex items-center rounded-lg bg-gray-300 px-4 py-2 text-gray-800 hover:bg-gray-400">
                    Anuluj
                </a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const categoryRates = @json($categoryRates);
            const toggleBtn      = document.getElementById('toggle-category-mode');
            const selectCategory = document.getElementById('existing-category');
            const inputCategory  = document.getElementById('category');
            const rateInput      = document.getElementById('operator_rate');

            let usingSelect = false;

            toggleBtn.addEventListener('click', () => {
                usingSelect = !usingSelect;

                if (usingSelect) {
                    // Tryb "lista"
                    inputCategory.classList.add('hidden');
                    selectCategory.classList.remove('hidden');
                    // Przywróć poprzednią lub pustą wartość
                    selectCategory.value = '';
                    inputCategory.value  = '';
                    rateInput.value      = '';
                    rateInput.readOnly   = true;
                    toggleBtn.textContent = 'Wpisz kategorię';
                } else {
                    // Tryb "ręcznie"
                    selectCategory.classList.add('hidden');
                    inputCategory.classList.remove('hidden');
                    // Wyczyść wszystko, żeby wymusić manualne wpisanie
                    selectCategory.value = '';
                    inputCategory.value  = '';
                    rateInput.value      = '';
                    rateInput.readOnly   = false;
                    toggleBtn.textContent = 'Wybierz z listy';
                }
            });

            selectCategory.addEventListener('change', () => {
                const cat = selectCategory.value;
                inputCategory.value = cat;
                rateInput.value     = categoryRates[cat] ?? '';
                rateInput.readOnly  = true;
            });

            // Dodatkowo: jeśli wpiszesz ręcznie nazwę istniejącej kategorii,
            // to zablokujemy stawkę i podmienimy ją na tę z mapy.
            inputCategory.addEventListener('blur', () => {
                const cat = inputCategory.value.trim();
                if (!usingSelect && categoryRates.hasOwnProperty(cat)) {
                    rateInput.value     = categoryRates[cat];
                    rateInput.readOnly  = true;
                } else if (!usingSelect) {
                    rateInput.readOnly  = false;
                }
            });
        });
    </script>
@endpush
