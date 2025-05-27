@extends('layouts.app')

@section('content')
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

        <form action="{{ route('admin.equipment.update', $equipment->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block font-medium mb-1">Nazwa</label>
                <input type="text" name="name" value="{{ old('name', $equipment->name) }}" required
                       class="w-full rounded-lg border-gray-300 px-4 py-2 shadow-sm">
            </div>

            <div>
                <label class="block font-medium mb-1">Opis</label>
                <textarea name="description" rows="4" required
                          class="w-full rounded-lg border-gray-300 px-4 py-2 shadow-sm">{{ old('description', $equipment->description) }}</textarea>
            </div>

            <div>
                <label class="block font-medium mb-1">Dostępność</label>
                <select name="availability" required
                        class="w-full rounded-lg border-gray-300 px-4 py-2 shadow-sm">
                    @foreach (['dostepny', 'niedostepny', 'rezerwacja'] as $option)
                        <option value="{{ $option }}" {{ old('availability', $equipment->availability) === $option ? 'selected' : '' }}>
                            {{ ucfirst($option) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-medium mb-1">Cena wynajmu (zł)</label>
                <input type="number" step="0.01" name="rental_price" value="{{ old('rental_price', $equipment->rental_price) }}" required
                       class="w-full rounded-lg border-gray-300 px-4 py-2 shadow-sm">
            </div>

            <div>
                <label class="block font-medium mb-1">Miniatura (podmień jeśli chcesz)</label>
                <input type="file" name="thumbnail" accept=".webp"
                       class="w-full rounded-lg border-gray-300 px-3 py-2 file:bg-indigo-600 file:text-white file:px-4 file:py-2 file:rounded">
                <p class="text-sm text-gray-500 mt-1">Obecna miniatura:</p>
                <img src="{{ asset($equipment->thumbnail) }}" alt="miniatura" class="h-16 mt-2 rounded shadow">
            </div>

            <div>
                <label class="block font-medium mb-1">Dodaj więcej zdjęć (opcjonalnie)</label>
                <input type="file" name="photos[]" accept=".webp" multiple
                       class="w-full rounded-lg border-gray-300 px-3 py-2 file:bg-indigo-600 file:text-white file:px-4 file:py-2 file:rounded">
            </div>

            <div>
                <label class="block font-medium mb-1">Stan techniczny</label>
                <select name="technical_state" required
                        class="w-full rounded-lg border-gray-300 px-4 py-2 shadow-sm">
                    @foreach (['nowy', 'uzywany', 'naprawa'] as $state)
                        <option value="{{ $state }}" {{ old('technical_state', $equipment->technical_state) === $state ? 'selected' : '' }}>
                            {{ ucfirst($state) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-medium mb-1">Kategoria</label>
                <input type="text" name="category" value="{{ old('category', $equipment->category) }}" required
                       class="w-full rounded-lg border-gray-300 px-4 py-2 shadow-sm">
            </div>

            <div>
                <label class="block font-medium mb-1">Typ promocji</label>
                <select name="promotion_type" id="promotion_type"
                        class="w-full rounded-lg border-gray-300 px-4 py-2 shadow-sm"
                    {{ $equipment->promotion_type === 'kategoria' ? 'disabled' : '' }}>
                    <option value="">Brak</option>
                    <option value="pojedyncza" {{ old('promotion_type', $equipment->promotion_type) === 'pojedyncza' ? 'selected' : '' }}>Pojedyncza</option>
                    <option value="kategoria" {{ old('promotion_type', $equipment->promotion_type) === 'kategoria' ? 'selected' : '' }}>Kategoria (zablokowana)</option>
                </select>
            </div>

            <div id="promo-fields"
                 class="{{ $equipment->promotion_type === 'kategoria' ? 'hidden' : '' }}">
                <div>
                    <label class="block font-medium mb-1">Rabat (%)</label>
                    <input type="number" name="discount" value="{{ old('discount', $equipment->discount) }}"
                           class="w-full rounded-lg border-gray-300 px-4 py-2 shadow-sm">
                </div>

                <div>
                    <label class="block font-medium mb-1">Data rozpoczęcia promocji</label>
                    <input type="datetime-local" name="start_datetime"
                           value="{{ old('start_datetime', $equipment->start_datetime ? \Carbon\Carbon::parse($equipment->start_datetime)->format('Y-m-d\TH:i') : '') }}"
                           class="w-full rounded-lg border-gray-300 px-4 py-2 shadow-sm">
                </div>

                <div>
                    <label class="block font-medium mb-1">Data zakończenia promocji</label>
                    <input type="datetime-local" name="end_datetime"
                           value="{{ old('end_datetime', $equipment->end_datetime ? \Carbon\Carbon::parse($equipment->end_datetime)->format('Y-m-d\TH:i') : '') }}"
                           class="w-full rounded-lg border-gray-300 px-4 py-2 shadow-sm">
                </div>
            </div>

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
            const promoType = document.getElementById('promotion_type');
            const promoFields = document.getElementById('promo-fields');

            if (promoType) {
                promoType.addEventListener('change', function () {
                    if (this.value === 'pojedyncza' || this.value === '') {
                        promoFields.classList.remove('hidden');
                    } else {
                        promoFields.classList.add('hidden');
                    }
                });
            }
        });
    </script>
@endpush
