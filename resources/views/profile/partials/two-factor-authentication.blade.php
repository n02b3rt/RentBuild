<div>
    <h2 class="text-lg font-medium text-gray-900">
        Weryfikacja dwuetapowa (2FA)
    </h2>
    <p class="mt-1 text-sm text-gray-600">
        Zwiększ bezpieczeństwo swojego konta za pomocą aplikacji Google Authenticator.
    </p>

    @if(auth()->user()->two_factor_enabled)
        <div class="mt-4">
            <p class="text-green-600 font-semibold">2FA jest włączona</p>

            <form method="POST" action="{{ route('2fa.disable') }}" class="mt-3 space-y-3">
                @csrf
                <label for="code" class="block text-sm font-medium text-gray-700">Podaj kod 2FA, aby wyłączyć:</label>
                <input id="code" name="code" type="text" maxlength="6"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                       placeholder="123456" required>
                @error('code')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror

                <div class="flex">
                    <button type="submit"
                            class="inline-flex mr-4 items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-white hover:bg-red-700">
                        Wyłącz 2FA
                    </button>
                    <a href="{{ route('2fa.recovery') }}"
                       class="text-sm px-4 py-2 rounded-md border border-[#f56600] text-[#f56600] hover:bg-[#f56600] hover:text-white transition text-center">
                        🔐 Zobacz kody zapasowe 2FA
                    </a>
                </div>

            </form>
        </div>
    @else
        <div class="mt-4">
            <button
                onclick="window.location='{{ route('2fa.setup') }}'"
                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700">
                Włącz Weryfikację Dwuetapową
            </button>
        </div>
    @endif
</div>
