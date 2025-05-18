<section class="max-w-7xl mx-auto px-4 py-12">
    <h2 class="text-3xl font-bold mb-8 text-center">Najczęściej wypożyczane</h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
        @foreach ($sprzety as $sprzet)
            <a href="{{ route('sprzety.pokaz', $sprzet->id) }}" class="block bg-white shadow hover:shadow-lg transition rounded-xl overflow-hidden">
                <img src="/{{ $sprzet->zdjecie_glowne }}" alt="{{ $sprzet->nazwa }}" class="w-full h-64 object-scale-down">
                <div class="p-4">
                    <h3 class="text-lg font-semibold mb-1">{{ $sprzet->nazwa }}</h3>
                    <p class="text-sm text-gray-500 mb-1">{{ $sprzet->kategoria }}</p>
                    <p class="font-bold text-orange-500">{{ number_format($sprzet->cena_wynajmu, 2) }} zł</p>
                    <p class="text-xs text-gray-400 mt-2">Wypożyczeń: {{ $sprzet->ilosc_wypozyczen }}</p>
                </div>
            </a>
        @endforeach
    </div>
</section>
