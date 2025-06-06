<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 shadow-inner">
    {{-- Płatność kodem BIWO --}}
    <form
        method="POST"
        action="{{ route('client.rentals.payWithBiwo') }}"
        class="space-y-4"
    >
        @csrf

        <label for="code" class="block text-gray-800 font-medium mb-1">
            Zapłać kodem BIWO
        </label>
        <input
            type="text"
            name="code"
            id="code"
            required
            maxlength="6"
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-400"
            placeholder="XXXXXX"
            value="{{ old('code') }}"
        >
        @error('code')
        <p class="text-red-600 text-sm">{{ $message }}</p>
        @enderror

        <button
            type="submit"
            class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2 rounded-lg transition"
        >
            Zapłać kodem BIWO
        </button>
    </form>

    <div class="mt-6 text-center">
        <a
            href="{{ route('client.rentals.generateBiwo') }}"
            class="inline-block bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-2 px-4 rounded-lg transition"
        >
            Wygeneruj kod BIWO
        </a>
    </div>
</div>
