@extends('layouts.admin')

@section('admin-content')
    <div class="container mx-auto py-8 max-w-md">
        <h1 class="text-2xl font-bold mb-4">Edytuj stawkę operatora</h1>

        <form method="POST"
              action="{{ route('admin.operator-rates.update', urlencode($category)) }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block font-medium mb-1">Kategoria</label>
                <input type="text"
                       value="{{ $category }}"
                       disabled
                       class="w-full border p-2 rounded bg-gray-100 cursor-not-allowed">
            </div>

            <div class="mb-4">
                <label for="operator_rate" class="block font-medium mb-1">Stawka operatora (zł/dzień)</label>
                <input type="number"
                       step="0.01"
                       min="0"
                       name="operator_rate"
                       id="operator_rate"
                       value="{{ old('operator_rate', $current_operator_rate) }}"
                       class="w-full border p-2 rounded @error('operator_rate') border-red-500 @enderror">
                @error('operator_rate')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">
                Zapisz
            </button>
            <a href="{{ route('admin.operator-rates.index') }}"
               class="ml-4 text-gray-600 hover:underline">Anuluj</a>
        </form>
    </div>
@endsection
