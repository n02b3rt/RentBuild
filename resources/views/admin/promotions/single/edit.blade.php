@extends('layouts.admin')

@section('admin-content')
    <div class="max-w-xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold mb-8 text-center sm:text-left">Edytuj promocję pojedynczą</h1>

        <form action="{{ route('admin.promotions.single.update', $promotion->id) }}" method="POST" class="space-y-6" novalidate>
            @csrf
            @method('PUT')

            {{-- Sprzęt (readonly) --}}
            <div>
                <label class="block mb-2 font-semibold text-gray-700">Sprzęt</label>
                <input type="text" class="w-full rounded-lg border border-gray-300 px-4 py-2 bg-gray-100 cursor-not-allowed" value="{{ $promotion->name }}" disabled>
            </div>

            {{-- Rabat --}}
            <div>
                <label for="discount" class="block mb-2 font-semibold text-gray-700">Rabat (%)</label>
                <input
                    type="number"
                    name="discount"
                    id="discount"
                    min="1"
                    max="100"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[#f56600]"
                    value="{{ old('discount', $promotion->discount) }}"
                    required
                >
                @error('discount')
                <p class="mt-1 text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>

            {{-- Checkbox daty --}}
            <div class="flex items-center space-x-2">
                <input
                    type="checkbox"
                    name="has_dates"
                    id="has_dates"
                    class="form-checkbox h-5 w-5 text-[#f56600]"
                    value="1"
                    {{ (old('has_dates', ($promotion->start_datetime && $promotion->end_datetime)) ? 'checked' : '') }}
                >
                <label for="has_dates" class="font-medium text-gray-700">Ustaw datę promocji</label>
            </div>

            {{-- Pola daty i czasu --}}
            <div id="date_fields" class="{{ old('has_dates', ($promotion->start_datetime && $promotion->end_datetime)) ? '' : 'hidden' }}">
                <div class="mb-4">
                    <label for="start_datetime" class="block mb-2 font-semibold text-gray-700">Data i godzina rozpoczęcia</label>
                    <input
                        type="datetime-local"
                        name="start_datetime"
                        id="start_datetime"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[#f56600]"
                        value="{{ old('start_datetime', $promotion->start_datetime ? \Carbon\Carbon::parse($promotion->start_datetime)->format('Y-m-d\TH:i') : '') }}"
                    >
                    @error('start_datetime')
                    <p class="mt-1 text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="end_datetime" class="block mb-2 font-semibold text-gray-700">Data i godzina zakończenia</label>
                    <input
                        type="datetime-local"
                        name="end_datetime"
                        id="end_datetime"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[#f56600]"
                        value="{{ old('end_datetime', $promotion->end_datetime ? \Carbon\Carbon::parse($promotion->end_datetime)->format('Y-m-d\TH:i') : '') }}"
                    >
                    @error('end_datetime')
                    <p class="mt-1 text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <button type="submit" class="w-full py-3 mt-6 bg-[#f56600] text-white font-semibold rounded">
                Zapisz zmiany
            </button>
        </form>
    </div>

    <script>
        const hasDatesCheckbox = document.getElementById('has_dates');
        const dateFields = document.getElementById('date_fields');

        hasDatesCheckbox.addEventListener('change', () => {
            if (hasDatesCheckbox.checked) {
                dateFields.classList.remove('hidden');
            } else {
                dateFields.classList.add('hidden');
            }
        });
    </script>
@endsection
