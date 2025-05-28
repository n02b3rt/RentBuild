@extends('layouts.app')

@section('content')
    <div class="container mx-auto lg:flex px-4 py-6">
        <!-- Mobile Accordion Toggle -->
        <div class="block lg:hidden mb-4">
            <button onclick="toggleFiltersMobile()" class="bg-[#f56600] text-white py-2 px-4 rounded w-full">Filtry</button>
        </div>

        <!-- Mobile Filter Form (Accordion) -->
        <div id="mobileFilterPanel" class="hidden lg:hidden mb-6">
            <form method="GET" action="{{ route('equipments.index') }}" class="space-y-4">
                <div>
                    <label class="block mb-1 font-medium">Price: <output id="mobilePriceOutput" class="ml-1">{{ request('max_price', $maxPrice) }}</output> (zł)</label>
                    <input type="range" id="mobilePriceRange" min="0" max="{{ $maxPrice }}" step="1" value="{{ request('max_price', $maxPrice) }}" oninput="mobilePriceOutput.value=this.value">
                    <div class="flex justify-between text-sm text-gray-500">
                        <span>0 zł</span>
                        <span>{{ $maxPrice }} zł</span>
                    </div>
                    <input type="hidden" name="min_price" value="0">
                    <input type="hidden" name="max_price" id="mobile_max_price" value="{{ request('max_price', $maxPrice) }}">
                </div>

                <div>
                    <label class="block mb-1 font-medium">Sort by</label>
                    <select name="sort" class="w-full border rounded p-1">
                        <option value="">Default</option>
                        <option value="cena_asc" {{ request('sort') == 'cena_asc' ? 'selected' : '' }}>Price ascending</option>
                        <option value="cena_desc" {{ request('sort') == 'cena_desc' ? 'selected' : '' }}>Price descending</option>
                        <option value="wypozyczenia_desc" {{ request('sort') == 'wypozyczenia_desc' ? 'selected' : '' }}>Most rented</option>
                    </select>
                </div>

                <div>
                    <label class="block mb-1 font-medium">Availability</label>
                    <select name="availability" class="w-full border rounded p-1">
                        <option value="">--</option>
                        <option value="dostepny" {{ request('availability') == 'dostepny' ? 'selected' : '' }}>Available</option>
                        <option value="niedostepny" {{ request('availability') == 'niedostepny' ? 'selected' : '' }}>Unavailable</option>
                        <option value="rezerwacja" {{ request('availability') == 'rezerwacja' ? 'selected' : '' }}>Reserved</option>
                    </select>
                </div>

                <div>
                    <label class="block mb-1 font-medium">Technical condition</label>
                    <select name="technical_state" class="w-full border rounded p-1">
                        <option value="">--</option>
                        <option value="nowy" {{ request('technical_state') == 'nowy' ? 'selected' : '' }}>New</option>
                        <option value="uzywany" {{ request('technical_state') == 'uzywany' ? 'selected' : '' }}>Used</option>
                        <option value="naprawa" {{ request('technical_state') == 'naprawa' ? 'selected' : '' }}>In repair</option>
                    </select>
                </div>

                <div>
                    <label class="block mb-1 font-medium">Has discount</label>
                    <select name="discount" class="w-full border rounded p-1">
                        <option value="">--</option>
                        <option value="1" {{ request('discount') ? 'selected' : '' }}>Yes</option>
                    </select>
                </div>

                <div>
                    <label class="block mb-1 font-medium">Category</label>
                    <select name="category" class="w-full border rounded p-1">
                        <option value="">--</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>{{ $category }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <button type="submit" class="w-full bg-[#f56600] hover:bg-orange-600 text-white font-bold py-2 px-4 rounded text-sm">Filter</button>
                </div>
            </form>
        </div>

        <!-- Desktop/Tablet Filter Form -->
        <div class="hidden lg:flex h-fit bg-white mr-8 py-8 px-4">
            <form method="GET" action="{{ route('equipments.index') }}" class="grid h-fit gap-4 w-full">
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
                    <select name="sort" class="w-full border rounded p-1">
                        <option value="">Domyślnie</option>
                        <option value="cena_asc" {{ request('sort') == 'cena_asc' ? 'selected' : '' }}>Cena rosnąco</option>
                        <option value="cena_desc" {{ request('sort') == 'cena_desc' ? 'selected' : '' }}>Cena malejąco</option>
                        <option value="wypozyczenia_desc" {{ request('sort') == 'wypozyczenia_desc' ? 'selected' : '' }}>Najczęściej wypożyczane</option>
                    </select>
                </div>


                <div class="flex flex-col ">
                    <label class="block mb-1 font-medium">Dostępność</label>
                    <select name="availability" class="w-full border rounded p-1">
                        <option value="">--</option>
                        <option value="dostepny" {{ request('availability') == 'dostepny' ? 'selected' : '' }}>dostępny</option>
                        <option value="niedostepny" {{ request('availability') == 'niedostepny' ? 'selected' : '' }}>niedostępny</option>
                        <option value="rezerwacja" {{ request('availability') == 'rezerwacja' ? 'selected' : '' }}>rezerwacja</option>
                    </select>
                </div>

                <div class="flex flex-col ">
                    <label class="block mb-1 font-medium">Stan techniczny</label>
                    <select name="technical_state" class="w-full border rounded p-1">
                        <option value="">--</option>
                        <option value="nowy" {{ request('technical_state') == 'nowy' ? 'selected' : '' }}>nowy</option>
                        <option value="uzywany" {{ request('technical_state') == 'uzywany' ? 'selected' : '' }}>używany</option>
                        <option value="naprawa" {{ request('technical_state') == 'naprawa' ? 'selected' : '' }}>naprawa</option>
                    </select>
                </div>

                <div class="flex flex-col ">
                    <label class="block mb-1 font-medium">Ma upust</label>
                    <select name="discount" class="w-full border rounded p-1">
                        <option value="">--</option>
                        <option value="1" {{ request('discount') ? 'selected' : '' }}>tak</option>
                    </select>
                </div>

                <div class="flex flex-col ">
                    <label class="block mb-1 font-medium">Kategoria</label>
                    <select name="category" class="w-full border rounded p-1">
                        <option value="">--</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>{{ $category }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end ">
                    <button type="submit" class="w-full bg-[#f56600] hover:bg-orange-600 text-white font-bold py-1.5 px-4 rounded text-sm">Filtruj</button>
                </div>
            </form>
        </div>

        <div class="grid 2xl:grid-cols-2 lx:grid-cols-1 gap-4 p-{100px 200px} w-full">
            @if($equipments->isEmpty())
                <div class="flex flex-col items-center text-gray-500 text-xl mt-10 w-full col-span-2">
                    <svg xmlns="http://www.w3.org/2000/svg" height="126px" viewBox="0 -960 960 960" width="126px" fill="#f56600">
                        <path d="M620-520q25 0 42.5-17.5T680-580q0-25-17.5-42.5T620-640q-25 0-42.5 17.5T560-580q0 25 17.5 42.5T620-520Zm-280 0q25 0 42.5-17.5T400-580q0-25-17.5-42.5T340-640q-25 0-42.5 17.5T280-580q0 25 17.5 42.5T340-520Zm140 100q-68 0-123.5 38.5T276-280h66q22-37 58.5-58.5T480-360q43 0 79.5 21.5T618-280h66q-25-63-80.5-101.5T480-420Zm0 340q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-400Zm0 320q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Z"/>
                    </svg>
                    Żaden sprzęt nie pasuje do wybranych filtrów.
                </div>
            @else
                @foreach($equipments as $equipment)
                    <a href="{{ route('equipment.show', $equipment->id) }}" class="block">
                        <div class="relative border rounded-lg overflow-hidden shadow-sm bg-white flex flex-col md:flex-row">
                            <div class="md:w-1/3 mr-[25px] flex items-center justify-center" style="height:250px">
                                <img src="/{{ $equipment->thumbnail }}" alt="{{ $equipment->name }}" class="object-scale-down h-full w-full">
                            </div>
                            <div class="p-4 grid gap-2 md:w-2/3">
                                <h2 class="text-3xl font-bold">{{ $equipment->name }}</h2>
                                <div>
                                    <p class="text-sm text-gray-500">wypożyczono: {{ $equipment->number_of_rentals }}</p>
                                    @if($equipment->discount)
                                        <div class="">
                                            <p>
                                                <span class="line-through text-red-500">{{ number_format($equipment->rental_price, 2) }} zł</span>
                                                <span class="text-gray-500"> cena z 30 dni</span>
                                            </p>
                                            <p class="text-4xl blod">{{ number_format($equipment->finalPrice(), 2) }} zł</p>
                                        </div>
                                    @else
                                        <p class="text-4xl blod">Cena: {{ number_format($equipment->rental_price, 2) }} zł</p>
                                    @endif
                                    <p class="text-sm">kategoria: {{ $equipment->category }}</p>
                                    <p class="text-sm">Dostępność: {{ $equipment->availability }}</p>
                                    <p class="text-sm">Stan: {{ $equipment->technical_state }}</p>

                                    @if($equipment->discount)
                                        <p class="absolute bg-[#f56600] py-1 pl-4 pr-2 rounded-br-lg rounded-tr-lg top-3 left-0">Upust: {{ $equipment->discount }}%</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            @endif


            <div class="mt-6 2lx:col-span-2 xl:col-span-2">
                    {{ $equipments->appends(request()->query())->links() }}
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
