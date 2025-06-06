@extends('layouts.admin')

@section('admin-content')
    <div class="max-w-3xl mx-auto bg-white shadow-md rounded-lg p-6">
        <h1 class="text-4xl font-bold text-[#f56600] mb-6 text-center">Edytuj zamówienie #{{ $rental->id }}</h1>

        <form action="{{ route('admin.rentals.update', $rental) }}" method="POST" class="space-y-6">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-2 gap-6">
                <div class="bg-gray-100 p-4 rounded-md shadow-sm">
                    <p class="text-lg font-semibold">Użytkownik:</p>
                    <p class="text-xl">{{ $rental->user->first_name }} {{ $rental->user->last_name }}</p>
                </div>

                <div class="bg-gray-100 p-4 rounded-md shadow-sm">
                    <p class="text-lg font-semibold">Sprzęt:</p>
                    <p class="text-xl">{{ $rental->equipment->name }}</p>
                </div>

                <div class="bg-gray-100 p-4 rounded-md shadow-sm col-span-2">
                    <label for="status" class="block text-lg font-semibold mb-1">Status:</label>

                    @php
                        $statuses = [
                            'oczekujace' => 'Oczekujące',
                            'nadchodzace' => 'Nadchodzące',
                            'aktualne' => 'Aktualne',
                            'zrealizowane' => 'Zrealizowane',
                            'anulowane' => 'Anulowane',
                        ];

                        $oldStatus = old('status');
                        $selectedStatus = (!is_null($oldStatus) && $oldStatus !== '') ? $oldStatus : $rental->status;
                    @endphp

                    <select name="status" id="status" class="border p-2 rounded w-full">
                        @foreach ($statuses as $key => $label)
                            <option value="{{ $key }}" @selected($selectedStatus === $key)>{{ $label }}</option>
                        @endforeach
                    </select>

                    @error('status')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="bg-gray-100 p-4 rounded-md shadow-sm">
                    <label for="start_date" class="block text-lg font-semibold mb-1">Data rozpoczęcia:</label>
                    <input type="datetime-local" name="start_date" id="start_date"
                           value="{{ old('start_date', $rental->start_date->format('Y-m-d\TH:i')) }}"
                           class="border p-2 rounded w-full">
                    @error('start_date')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="bg-gray-100 p-4 rounded-md shadow-sm">
                    <label for="end_date" class="block text-lg font-semibold mb-1">Data zakończenia:</label>
                    <input type="datetime-local" name="end_date" id="end_date"
                           value="{{ old('end_date', $rental->end_date->format('Y-m-d\TH:i')) }}"
                           class="border p-2 rounded w-full">
                    @error('end_date')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="bg-gray-100 p-4 rounded-md shadow-sm col-span-2">
                    <label for="total_price" class="block text-lg font-semibold mb-1">Cena całkowita (zł):</label>
                    <input type="number" step="0.01" name="total_price" id="total_price"
                           value="{{ old('total_price', $rental->total_price) }}"
                           class="border p-2 rounded w-full" min="0">
                    @error('total_price')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="bg-gray-100 p-4 rounded-md shadow-sm col-span-2">
                    <label for="notes" class="block text-lg font-semibold mb-1">Uwagi:</label>
                    <textarea name="notes" id="notes" rows="4" class="border p-2 rounded w-full">{{ old('notes', $rental->notes) }}</textarea>
                    @error('notes')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-between items-center mt-6">
                <a href="{{ route('admin.rentals.show', $rental) }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded shadow">
                    Anuluj
                </a>

                <button type="submit"
                        class="bg-[#f56600] hover:bg-[#f98800] text-white py-2 px-6 rounded shadow font-semibold">
                    Zapisz zmiany
                </button>
            </div>
        </form>
    </div>
@endsection
