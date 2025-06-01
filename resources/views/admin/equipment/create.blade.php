@extends('layouts.admin')

@section('admin-content')
    <div class="max-w-3xl mx-auto py-8">
        <h1 class="text-2xl font-bold mb-6">Dodaj nowy sprzęt</h1>

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

        <form action="{{ route('admin.equipment.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            {{-- Nazwa --}}
            <div>
                <label for="name" class="block font-medium mb-1">Nazwa</label>
                <input type="text" name="name" id="name" required value="{{ old('name') }}"
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#f56600] focus:ring-[#f56600] px-4 py-2">
            </div>

            {{-- Opis --}}
            <div>
                <label for="description" class="block font-medium mb-1">Opis</label>
                <textarea name="description" id="description" rows="4" required
                          class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#f56600] focus:ring-[#f56600] px-4 py-2">{{ old('description') }}</textarea>
            </div>

            {{-- Dostępność --}}
            <div>
                <label for="availability" class="block font-medium mb-1">Dostępność</label>
                <select name="availability" id="availability" required
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#f56600] focus:ring-[#f56600] px-4 py-2">
                    <option value="">-- Wybierz --</option>
                    <option value="dostepny" {{ old('availability') == 'dostepny' ? 'selected' : '' }}>Dostępny</option>
                    <option value="niedostepny" {{ old('availability') == 'niedostepny' ? 'selected' : '' }}>Niedostępny</option>
                    <option value="rezerwacja" {{ old('availability') == 'rezerwacja' ? 'selected' : '' }}>Rezerwacja</option>
                </select>
            </div>

            {{-- Cena wynajmu --}}
            <div>
                <label for="rental_price" class="block font-medium mb-1">Cena wynajmu (zł)</label>
                <input type="number" name="rental_price" id="rental_price" step="0.01" required
                       value="{{ old('rental_price') }}"
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#f56600] focus:ring-[#f56600] px-4 py-2">
            </div>

            {{-- Miniatura --}}
            <div>
                <label for="thumbnail" class="block font-medium mb-1">Miniatura (1 plik)</label>
                <input type="file" name="thumbnail" id="thumbnail" accept="image/*" required
                       class="w-full rounded-lg border-gray-300 px-3 py-2 file:mr-4 file:rounded file:border-0 file:bg-[#f56600] file:px-4 file:py-2 file:text-white">
            </div>

            {{-- Dodatkowe zdjęcia --}}
            <div>
                <label for="photos[]" class="block font-medium mb-1">Zdjęcia dodatkowe (można wiele)</label>
                <input type="file" name="photos[]" id="photos[]" multiple accept="image/*"
                       class="w-full rounded-lg border-gray-300 px-3 py-2 file:mr-4 file:rounded file:border-0 file:bg-[#f56600] file:px-4 file:py-2 file:text-white">
            </div>

            {{-- Stan techniczny --}}
            <div>
                <label for="technical_state" class="block font-medium mb-1">Stan techniczny</label>
                <select name="technical_state" id="technical_state" required
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#f56600] focus:ring-[#f56600] px-4 py-2">
                    <option value="">-- Wybierz --</option>
                    <option value="nowy" {{ old('technical_state') == 'nowy' ? 'selected' : '' }}>Nowy</option>
                    <option value="uzywany" {{ old('technical_state') == 'uzywany' ? 'selected' : '' }}>Używany</option>
                    <option value="naprawa" {{ old('technical_state') == 'naprawa' ? 'selected' : '' }}>W naprawie</option>
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
                        class="w-full hidden rounded-lg border-gray-300 shadow-sm focus:border-[#f56600] focus:ring-[#f56600] px-4 py-2 mb-2">
                    <option value="">-- Wybierz istniejącą kategorię --</option>
                    @foreach($categoryRates->keys() as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                    @endforeach
                </select>

                <input type="text" name="category" id="category"
                       placeholder="Wpisz kategorię"
                       value="{{ old('category') }}" required
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#f56600] focus:ring-[#f56600] px-4 py-2">
            </div>

            {{-- Pole stawki operatora --}}
            <div id="operator-rate-group">
                <label for="operator_rate" class="block font-medium mb-1">Stawka operatora (zł/dzień)</label>
                <input type="number"
                       name="operator_rate"
                       id="operator_rate"
                       step="0.01"
                       min="0"
                       value="{{ old('operator_rate') }}"
                       required
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#f56600] focus:ring-[#f56600] px-4 py-2 @error('operator_rate') border-red-500 @enderror">
                @error('operator_rate')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit --}}
            <div class="flex items-center gap-4 pt-4">
                <button type="submit"
                        class="inline-flex items-center rounded-lg bg-[#f56600] px-5 py-2 text-white hover:bg-[#ff6900]">
                    Dodaj sprzęt
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
