@extends('layouts.app')

@section('content')
    <div class="max-w-xl mx-auto p-6 bg-white rounded shadow">
        <h1 class="text-2xl font-bold mb-6">Zgłoś reklamację dla: {{ $rental->equipment->name }}</h1>

        <form method="POST" action="{{ route('client.rentals.complaint.store', $rental) }}">
            @csrf

            <div class="mb-4">
                <label for="description" class="block text-gray-700 font-semibold mb-2">Opis reklamacji:</label>
                <textarea name="description" id="description" rows="5" required class="w-full border rounded p-2 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                @error('description')
                <p class="text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded">Wyślij reklamację</button>
            <a href="{{ route('client.rentals.index') }}" class="ml-4 text-gray-600 hover:underline">Anuluj</a>
        </form>
    </div>
@endsection
