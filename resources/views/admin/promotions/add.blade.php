@extends('layouts.admin')

@section('admin-content')
    <div class="container mx-auto p-4">
        <h1 class="text-3xl font-bold mb-4">Dodaj promocję</h1>

        @if(session('success'))
            <div class="text-green-500 mb-4">{{ session('success') }}</div>
        @elseif(session('error'))
            <div class="text-red-500 mb-4">{{ session('error') }}</div>
        @endif

        <form action="{{ route('admin.promotions.store') }}" method="POST">
        @csrf

            <div class="mb-4">
                <label for="category" class="block text-sm font-medium text-gray-700">Wybierz kategorię (która nie ma jeszcze promocji)</label>
                <select name="category" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-[#f56600] focus:border-[#f56600] sm:text-sm" required>
                    <option value="">Wybierz kategorię</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category }}" {{ old('category') == $category ? 'selected' : '' }}>
                            {{ $category }}
                        </option>
                    @endforeach
                </select>
                @error('category')
                <div class="text-red-500 text-sm">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="start_datetime" class="block text-sm font-medium text-gray-700">Data rozpoczęcia promocji</label>
                <input type="datetime-local" id="start_datetime" name="start_datetime" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-[#f56600] focus:border-[#f56600] sm:text-sm" value="{{ old('start_datetime') }}" required>
                @error('start_datetime')
                <div class="text-red-500 text-sm">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="end_datetime" class="block text-sm font-medium text-gray-700">Data zakończenia promocji</label>
                <input type="datetime-local" id="end_datetime" name="end_datetime" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-[#f56600] focus:border-[#f56600] sm:text-sm" value="{{ old('end_datetime') }}" required>
                @error('end_datetime')
                <div class="text-red-500 text-sm">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="discount_percentage" class="block text-sm font-medium text-gray-700">Procent zniżki</label>
                <input type="number" id="discount_percentage" name="discount_percentage" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-[#f56600] focus:border-[#f56600] sm:text-sm" value="{{ old('discount_percentage') }}" min="1" max="100" required>
                @error('discount_percentage')
                <div class="text-red-500 text-sm">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="bg-[#f56600] text-white py-2 px-4 rounded-lg">Dodaj promocję</button>
        </form>
    </div>
@endsection
