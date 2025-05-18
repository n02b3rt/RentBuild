@extends('layouts.app')

@section('content')
    <div class="container mx-auto lg:flex px-4 py-6">
        <!-- Mobile Accordion Toggle -->
        <div class="block lg:hidden mb-4">
            <button onclick="toggleFiltersMobile()" class="bg-[#f56600] text-white py-2 px-4 rounded w-full">Filtry</button>
        </div>

        <!-- Mobile Filter Form (Accordion) -->
        <div id="mobileFilterPanel" class="hidden lg:hidden mb-6">
            <form method="GET" action="{{ route('sprzety.index') }}" class="space-y-4">
                <div>
                    <label class="block mb-1 font-medium">Cena: <output id="mobilePriceOutput" class="ml-1">{{ request('max_price', $maxPrice) }}</output> (zł)</label>
                    <input type="range" id="mobilePriceRange" min="0" max="{{ $maxPrice }}" step="1" value="{{ request('max_price', $maxPrice) }}" oninput="mobilePriceOutput.value=this.value">
                    <div class="flex justify-between text-sm text-gray-500">
                        <span>0 zł</span>
                        <span>{{ $maxPrice }} zł</span>
                    </div>
                    <input type="hidden" name="min_price" value="0">
                    <input type="hidden" name="max_price" id="mobile_max_price" value="{{ request('max_price', $maxPrice) }}">
                </div>

                <div>
                    <label class="block mb-1 font-medium">Sortuj wg</label>
                    <select name="sortuj" class="w-full border rounded p-1">
                        <option value="">Domyślnie</option>
                        <option value="cena_asc" {{ request('sortuj') == 'cena_asc' ? 'selected' : '' }}>Cena rosnąco</option>
                        <option value="cena_desc" {{ request('sortuj') == 'cena_desc' ? 'selected' : '' }}>Cena malejąco</option>
                        <option value="wypozyczenia_desc" {{ request('sortuj') == 'wypozyczenia_desc' ? 'selected' : '' }}>Najczęściej wypożyczane</option>
                    </select>
                </div>

                <div>
                    <label class="block mb-1 font-medium">Dostępność</label>
                    <select name="dostepnosc" class="w-full border rounded p-1">
                        <option value="">--</option>
                        <option value="dostepny" {{ request('dostepnosc') == 'dostepny' ? 'selected' : '' }}>dostępny</option>
                        <option value="niedostepny" {{ request('dostepnosc') == 'niedostepny' ? 'selected' : '' }}>niedostępny</option>
                        <option value="rezerwacja" {{ request('dostepnosc') == 'rezerwacja' ? 'selected' : '' }}>rezerwacja</option>
                    </select>
                </div>

                <div>
                    <label class="block mb-1 font-medium">Stan techniczny</label>
                    <select name="stan_techniczny" class="w-full border rounded p-1">
                        <option value="">--</option>
                        <option value="nowy" {{ request('stan_techniczny') == 'nowy' ? 'selected' : '' }}>nowy</option>
                        <option value="uzywany" {{ request('stan_techniczny') == 'uzywany' ? 'selected' : '' }}>używany</option>
                        <option value="naprawa" {{ request('stan_techniczny') == 'naprawa' ? 'selected' : '' }}>naprawa</option>
                    </select>
                </div>

                <div>
                    <label class="block mb-1 font-medium">Ma upust</label>
                    <select name="upust" class="w-full border rounded p-1">
                        <option value="">--</option>
                        <option value="1" {{ request('upust') ? 'selected' : '' }}>tak</option>
                    </select>
                </div>

                <div>
                    <label class="block mb-1 font-medium">Kategoria</label>
                    <select name="kategoria" class="w-full border rounded p-1">
                        <option value="">--</option>
                        @foreach($kategorie as $kategoria)
                            <option value="{{ $kategoria }}" {{ request('kategoria') == $kategoria ? 'selected' : '' }}>{{ $kategoria }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <button type="submit" class="w-full bg-[#f56600] hover:bg-orange-600 text-white font-bold py-2 px-4 rounded text-sm">Filtruj</button>
                </div>
            </form>
        </div>

        <!-- Desktop/Tablet Filter Form -->
        <div class="hidden lg:flex h-fit bg-white mr-8 py-8 px-4">
            <form method="GET" action="{{ route('sprzety.index') }}" class="grid h-fit gap-4 w-full">
                <div class="flex flex-col ">
                    <label class="block mb-1 font-medium">Cena: <output id="priceOutput" class="ml-1">{{ request('max_price', $maxPrice) }}</output> (zł)</label>
                    <input type="range" id="priceRange" min="0" max="{{ $maxPrice }}" step="1" value="{{ request('max_price', $maxPrice) }}" oninput="priceOutput.value=this.value">
                    <div class="flex justify-between text-sm text-gray-500">
                        <span>0 zł</span>
                        <span>{{ $maxPrice }} zł</span>
                    </div>
                    <input type="hidden" name="min_price" value="0">
                    <input type="hidden" name="max_price" id="max_price" value="{{ request('max_price', $maxPrice) }}">
                </div>

                <div>
                    <label class="block mb-1 font-medium">Sortuj wg</label>
                    <select name="sortuj" class="w-full border rounded p-1">
                        <option value="">Domyślnie</option>
                        <option value="cena_asc" {{ request('sortuj') == 'cena_asc' ? 'selected' : '' }}>Cena rosnąco</option>
                        <option value="cena_desc" {{ request('sortuj') == 'cena_desc' ? 'selected' : '' }}>Cena malejąco</option>
                        <option value="wypozyczenia_desc" {{ request('sortuj') == 'wypozyczenia_desc' ? 'selected' : '' }}>Najczęściej wypożyczane</option>
                    </select>
                </div>


                <div class="flex flex-col ">
                    <label class="block mb-1 font-medium">Dostępność</label>
                    <select name="dostepnosc" class="w-full border rounded p-1">
                        <option value="">--</option>
                        <option value="dostepny" {{ request('dostepnosc') == 'dostepny' ? 'selected' : '' }}>dostępny</option>
                        <option value="niedostepny" {{ request('dostepnosc') == 'niedostepny' ? 'selected' : '' }}>niedostępny</option>
                        <option value="rezerwacja" {{ request('dostepnosc') == 'rezerwacja' ? 'selected' : '' }}>rezerwacja</option>
                    </select>
                </div>

                <div class="flex flex-col ">
                    <label class="block mb-1 font-medium">Stan techniczny</label>
                    <select name="stan_techniczny" class="w-full border rounded p-1">
                        <option value="">--</option>
                        <option value="nowy" {{ request('stan_techniczny') == 'nowy' ? 'selected' : '' }}>nowy</option>
                        <option value="uzywany" {{ request('stan_techniczny') == 'uzywany' ? 'selected' : '' }}>używany</option>
                        <option value="naprawa" {{ request('stan_techniczny') == 'naprawa' ? 'selected' : '' }}>naprawa</option>
                    </select>
                </div>

                <div class="flex flex-col ">
                    <label class="block mb-1 font-medium">Ma upust</label>
                    <select name="upust" class="w-full border rounded p-1">
                        <option value="">--</option>
                        <option value="1" {{ request('upust') ? 'selected' : '' }}>tak</option>
                    </select>
                </div>

                <div class="flex flex-col ">
                    <label class="block mb-1 font-medium">Kategoria</label>
                    <select name="kategoria" class="w-full border rounded p-1">
                        <option value="">--</option>
                        @foreach($kategorie as $kategoria)
                            <option value="{{ $kategoria }}" {{ request('kategoria') == $kategoria ? 'selected' : '' }}>{{ $kategoria }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end ">
                    <button type="submit" class="w-full bg-[#f56600] hover:bg-orange-600 text-white font-bold py-1.5 px-4 rounded text-sm">Filtruj</button>
                </div>
            </form>
        </div>

        <div class="grid 2xl:grid-cols-2 lx:grid-cols-1 gap-4 p-{100px 200px}">
            @foreach($sprzety as $sprzet)
                <a href="{{ route('sprzety.pokaz', $sprzet->id) }}" class="block">
                    <div class="relative border rounded-lg overflow-hidden shadow-sm bg-white flex flex-col md:flex-row">
                        <div class="md:w-1/3 mr-[25px] flex items-center justify-center" style="height:250px">
                            <img src="/{{ $sprzet->zdjecie_glowne }}" alt="{{ $sprzet->nazwa }}" class="object-scale-down h-full w-full">
                        </div>
                        <div class="p-4 grid gap-2 md:w-2/3">
                            <h2 class="text-3xl font-bold">{{ $sprzet->nazwa }}</h2>
                            <div>
                                <p class="text-sm text-gray-500">wypożyczono: {{ $sprzet->ilosc_wypozyczen }}</p>
                                @if($sprzet->upust)
                                    <div class="">
                                        <p>
                                            <span class="line-through text-red-500">{{ number_format($sprzet->cena_wynajmu, 2) }} zł</span>
                                            <span class="text-gray-500"> cena z 30 dni</span>
                                        </p>
                                        <p class="text-4xl blod">{{ number_format($sprzet->cena_wynajmu * (1 - $sprzet->upust / 100), 2) }} zł</p>

                                    </div>
                                @else
                                    <p class="text-4xl blod">Cena: {{ number_format($sprzet->cena_wynajmu, 2) }} zł</p>
                                @endif
                                <p class="text-sm">kategoria: {{ $sprzet->kategoria }}</p>
                                <p class="text-sm">Dostępność: {{ $sprzet->dostepnosc }}</p>
                                <p class="text-sm">Stan: {{ $sprzet->stan_techniczny }}</p>

                                @if($sprzet->upust)
                                    <p class="absolute bg-[#f56600] py-1 pl-4 pr-2 rounded-br-lg rounded-tr-lg top-3 left-0">Upust: {{ $sprzet->upust }}%</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
                <div class="mt-6 2lx:col-span-2 xl:col-span-2">
                    {{ $sprzety->appends(request()->query())->links() }}
                </div>
        </div>


    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const priceRange = document.getElementById('priceRange');
            const maxPriceInput = document.getElementById('max_price');
            const priceOutput = document.getElementById('priceOutput');

            const mobileRange = document.getElementById('mobilePriceRange');
            const mobileMaxInput = document.getElementById('mobile_max_price');
            const mobileOutput = document.getElementById('mobilePriceOutput');

            if (priceRange && maxPriceInput && priceOutput) {
                priceRange.addEventListener('input', function () {
                    maxPriceInput.value = this.value;
                    priceOutput.value = this.value;
                });
            }

            if (mobileRange && mobileMaxInput && mobileOutput) {
                mobileRange.addEventListener('input', function () {
                    mobileMaxInput.value = this.value;
                    mobileOutput.value = this.value;
                });
            }

            window.toggleFiltersMobile = function () {
                const panel = document.getElementById('mobileFilterPanel');
                if (window.innerWidth < 1024 && panel) {
                    panel.classList.toggle('hidden');
                }
            }

            window.addEventListener('resize', () => {
                const panel = document.getElementById('mobileFilterPanel');
                if (window.innerWidth >= 1024 && panel) {
                    panel.classList.add('hidden');
                }
            });
        });
    </script>
@endsection
