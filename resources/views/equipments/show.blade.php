@extends('layouts.app')

@section('content')
    <section class="max-w-[1140px] mx-auto px-4 py-12">
        <div class="flex  bg-white p-10 rounded-md shadow-md">
            <div class="max-h-[500px] mr-14 w-1/2">
                <h1 class="text-4xl font-bold mb-4 text-center">{{ $equipment->name }}</h1>
                <div id="slider" class="relative w-full max-w-xl mx-auto">
                    <img id="slider-image"
                         src="/{{ $equipment->thumbnail }}"
                         class="h-[400px] w-auto mx-auto rounded shadow mb-4"
                         alt="Zdjęcie sprzętu">

                    @if(count($additionalPhotos) > 0)
                        <button onclick="prevSlide()"
                                class="absolute top-1/2 left-2 transform -translate-y-1/2 bg-black/50 text-white px-3 py-1 rounded-l">
                            ‹
                        </button>

                        <button onclick="nextSlide()"
                                class="absolute top-1/2 right-2 transform -translate-y-1/2 bg-black/50 text-white px-3 py-1 rounded-r">
                            ›
                        </button>
                    @endif
                </div>
            </div>

            <div class="w-1/2 flex flex-col justify-center p-12">
                <div>
                    <span class="text-xs text-gray-500">
                        Najniższa cena z 30 dni {{ number_format($equipment->rental_price, 2) }} zł
                    </span>
                    <p class="text-xl mb-2">
                        <strong>Cena:</strong>
                        @if($equipment->isPromotionActive())
                            <span>
                                <span class="line-through">
                                    {{ number_format($equipment->rental_price, 2, ',', ' ') }} zł
                                </span>
                                <span class="text-green-600 text-3xl ml-3">
                                    {{ number_format($equipment->finalPrice(), 2, ',', ' ') }} zł
                                </span>
                            </span>
                        @else
                            <span>
                                {{ number_format($equipment->rental_price, 2, ',', ' ') }} zł
                            </span>
                        @endif
                    </p>

                    <p class="text-lg mb-2">
                        <strong>Dostępność:</strong> {{ ucfirst($equipment->availability) }}
                    </p>
                    <p class="text-lg mb-2">
                        <strong>Stan techniczny:</strong> {{ ucfirst($equipment->technical_state) }}
                    </p>
                    <p class="text-lg mb-2">
                        <strong>Kategoria:</strong> {{ $equipment->category }}
                    </p>
                    <p class="text-lg mb-4">
                        <strong>Liczba wypożyczeń:</strong> {{ $equipment->number_of_rentals }}
                    </p>

                    <p class="text-xl mb-6">
                        {{ $equipment->description }}
                    </p>

                    @auth
                        @if(!$equipment->isAvailable())
                            <span class="italic">Produkt niedostępny</span>
                        @elseif(!auth()->user()->hasRole("administrator"))
                            <form action="{{ route('client.rentals.summary', $equipment->id) }}" method="GET" class="mb-4 flex justify-center">
                                <button type="submit"
                                        class="bg-[#f56600] hover:bg-[#f98800] text-white font-semibold py-2 px-6 rounded">
                                    ZAMÓW
                                </button>
                            </form>
                        @else
                            <span class="text-sm italic text-gray-500">Administratorzy nie mogą zamawiać sprzętu.</span>
                        @endif
                    @else
                        <span class="italic">Zaloguj się, aby złożyć zamówienie.</span>
                    @endauth

                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const photos = [
                '/{{ $equipment->thumbnail }}',
                @foreach($additionalPhotos as $photo)
                    '{{ $photo }}',
                @endforeach
            ];

            console.log('Photos array:', photos); // logujemy tablicę

            let current = 0;

            function showSlide(index) {
                console.log('Trying to show slide index:', index);
                const img = document.getElementById('slider-image');
                if (!img) {
                    console.error('Slider image element not found!');
                    return;
                }

                if (!photos[index]) {
                    console.warn('No photo at index:', index);
                    return;
                }

                console.log('Setting image source to:', photos[index]);
                img.src = photos[index];
            }

            function nextSlide() {
                current = (current + 1) % photos.length;
                console.log('Next slide:', current);
                showSlide(current);
            }

            function prevSlide() {
                current = (current - 1 + photos.length) % photos.length;
                console.log('Previous slide:', current);
                showSlide(current);
            }

            window.nextSlide = nextSlide;
            window.prevSlide = prevSlide;

            showSlide(current); // inicjalizacja
        });
    </script>

@endpush

