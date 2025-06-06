@extends('layouts.admin')

@section('admin-content')
    <div class="container mx-auto px-4 py-6 max-w-lg">
        <h1 class="text-3xl font-bold mb-6">Płatność</h1>

        @php
            // Pobieramy dane sesji
            $rentalData = session('rental_data', []);
            $userId     = $rentalData['user_id']    ?? null;
            $totalPrice = $rentalData['total_price'] ?? 0;

            // Ładujemy użytkownika po id
            $user = $userId
                ? \App\Models\User::find($userId)
                : null;
            $userBalance = $user->account_balance ?? 0;

            // Generujemy tempKey na bazie session_id (dzięki temu jest unikalny per sesja)
            $tempKey = 'admin_biwo_create_' . session()->getId();
        @endphp

        <p class="mb-4">
            Do zapłaty:
            <span class="text-2xl font-semibold">
                {{ number_format($totalPrice, 2, ',', ' ') }} zł
            </span>
        </p>

        @if($user && $userBalance >= $totalPrice)
            <form method="POST" action="{{ route('admin.rentals.create.finalize') }}" class="mt-6">
                @csrf
                <button
                    type="submit"
                    class="bg-[#f56600] hover:bg-[#f98800] text-white font-semibold py-2 px-6 rounded w-full"
                >
                    Pobierz z konta klienta
                </button>
            </form>
        @else
            <div class="flex flex-col gap-4 mt-6">
                @if($user)
                    <p class="text-sm text-gray-600">
                        Stan konta klienta: {{ number_format($userBalance, 2, ',', ' ') }} zł –
                        potrzebuje {{ number_format($totalPrice, 2, ',', ' ') }} zł
                    </p>

                    {{-- Przycisk „Klient musi doładować” (tylko informacyjnie) --}}
                    <div
                        class="bg-gray-400 text-white font-semibold py-2 px-6 rounded w-full text-center cursor-not-allowed"
                    >
                        Klient musi doładować konto
                    </div>
                @endif
            </div>
        @endif
    </div>
@endsection
