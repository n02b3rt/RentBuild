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

        <form action="{{ route('admin.equipment.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label for="name" class="block font-medium mb-1">Nazwa</label>
                <input type="text" name="name" required value="{{ old('name') }}"
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#f56600] focus:ring-[#f56600] px-4 py-2">
            </div>

            <div>
                <label for="description" class="block font-medium mb-1">Opis</label>
                <textarea name="description" rows="4" required
                          class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#f56600] focus:ring-[#f56600] px-4 py-2">{{ old('description') }}</textarea>
            </div>

            <div>
                <label for="availability" class="block font-medium mb-1">Dostępność</label>
                <select name="availability" required
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#f56600] focus:ring-[#f56600] px-4 py-2">
                    <option value="">-- Wybierz --</option>
                    <option value="dostepny" {{ old('availability') == 'dostepny' ? 'selected' : '' }}>Dostępny</option>
                    <option value="niedostepny" {{ old('availability') == 'niedostepny' ? 'selected' : '' }}>Niedostępny</option>
                    <option value="rezerwacja" {{ old('availability') == 'rezerwacja' ? 'selected' : '' }}>Rezerwacja</option>
                </select>
            </div>

            <div>
                <label for="rental_price" class="block font-medium mb-1">Cena wynajmu (zł)</label>
                <input type="number" name="rental_price" step="0.01" required value="{{ old('rental_price') }}"
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#f56600] focus:ring-[#f56600] px-4 py-2">
            </div>

            <div>
                <label for="thumbnail" class="block font-medium mb-1">Miniatura (1 plik)</label>
                <input type="file" name="thumbnail" accept="image/*" required
                       class="w-full rounded-lg border-gray-300 px-3 py-2 file:mr-4 file:rounded file:border-0 file:bg-[#f56600] file:px-4 file:py-2 file:text-white">
            </div>

            <div>
                <label for="photos[]" class="block font-medium mb-1">Zdjęcia dodatkowe (można wiele)</label>
                <input type="file" name="photos[]" multiple accept="image/*"
                       class="w-full rounded-lg border-gray-300 px-3 py-2 file:mr-4 file:rounded file:border-0 file:bg-[#f56600] file:px-4 file:py-2 file:text-white">
            </div>

            <div>
                <label for="technical_state" class="block font-medium mb-1">Stan techniczny</label>
                <select name="technical_state" required
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#f56600] focus:ring-[#f56600] px-4 py-2">
                    <option value="">-- Wybierz --</option>
                    <option value="nowy" {{ old('technical_state') == 'nowy' ? 'selected' : '' }}>Nowy</option>
                    <option value="uzywany" {{ old('technical_state') == 'uzywany' ? 'selected' : '' }}>Używany</option>
                    <option value="naprawa" {{ old('technical_state') == 'naprawa' ? 'selected' : '' }}>W naprawie</option>
                </select>
            </div>

            <div>
                <label class="block font-medium mb-1">Kategoria</label>

                <button type="button" id="toggle-category-mode"
                        class="mb-2 inline-flex items-center rounded-lg bg-blue-100 px-3 py-1 text-sm text-blue-700 hover:bg-blue-200">
                    Wybierz z listy
                </button>

                <select id="existing-category"
                        class="w-full hidden rounded-lg border-gray-300 shadow-sm focus:border-[#f56600] focus:ring-[#f56600] px-4 py-2 mb-2">
                    <option value="">-- Wybierz istniejącą kategorię --</option>
                    @foreach(\App\Models\Equipment::select('category')->distinct()->pluck('category') as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                    @endforeach
                </select>

                <input type="text" name="category" id="category"
                       placeholder="Wpisz kategorię"
                       value="{{ old('category') }}" required
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#f56600] focus:ring-[#f56600] px-4 py-2">
            </div>

            <div class="flex items-center gap-4 pt-4">
                <button type="submit"
                        class="inline-flex items-center rounded-lg bg-[#f56600]  px-5 py-2 text-white hover:bg-[#ff6900] ">
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
            const toggleBtn = document.getElementById('toggle-category-mode');
            const selectCategory = document.getElementById('existing-category');
            const inputCategory = document.getElementById('category');

            let usingSelect = false;

            toggleBtn.addEventListener('click', function () {
                usingSelect = !usingSelect;

                if (usingSelect) {
                    inputCategory.classList.add('hidden');
                    selectCategory.classList.remove('hidden');
                    inputCategory.value = '';
                    toggleBtn.textContent = 'Wpisz kategorię';
                } else {
                    selectCategory.classList.add('hidden');
                    inputCategory.classList.remove('hidden');
                    selectCategory.value = '';
                    toggleBtn.textContent = 'Wybierz z listy';
                }
            });

            selectCategory.addEventListener('change', function () {
                inputCategory.value = this.value;
            });
        });
    </script>
@endpush
