@extends('layouts.admin')

@section('admin-content')
    <div class="container mx-auto">
        <div class="flex w-full">
            <h1 class="w-4/5 text-3xl font-bold mb-4">Wszystkie promocje w kategoriach</h1>
            <a href="{{ route('admin.promotions.add') }}" class="w-1/5 h-fit text-right bg-[#f56600] text-white font-bold rounded py-2 px-4"> Dodaj promocje</a>
        </div>

        @foreach ($promotionsByCategory as $categories => $promotions)
            <div class="mb-4">
                <!-- Nagłówek akordeonu -->
                <div class="w-full flex bg-[#f56600] text-white font-semibold rounded-t-lg px-4 py-2 cursor-pointer"
                     onclick="toggleAccordion('accordion-{{ implode('-', explode(',', $categories)) }}')">
                    <div class="w-4/5">
                        <p class="text-2xl">{{ implode(', ', explode(',', $categories)) }}</p>
                        <p>
                             {{ \Carbon\Carbon::parse($promotions->first()->start_datetime)->format('Y-m-d H:i') }}
                            -
                             {{ \Carbon\Carbon::parse($promotions->first()->end_datetime)->format('Y-m-d H:i') }}
                        </p>
                    </div>
                    <div class="w-1/5 text-right">
                        <p class="text-2xl">{{ $promotions->first()->discount }} %</p>
                        <p>{{ $promotions->first()->status }}</p>

                    </div>
                </div>

                <!-- Zawartość rozwijana -->
                <div id="accordion-{{ implode('-', explode(',', $categories)) }}" class="accordion-content hidden bg-white p-4 rounded-b-lg">
                        <table class="table w-full">
                            <thead>
                            <tr>
                                <th class="px-4 py-2 text-left">Nazwa</th>
                                <th class="px-4 py-2 text-left">Status</th>
                                <th class="px-4 py-2 text-left">Cena z promocją</th>
                                <th class="px-4 py-2 text-left">Różnica</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($promotions as $equipment)
                                <tr>
                                    <td class="border px-4 py-2">{{ $equipment->name }}</td>
                                    <td class="border px-4 py-2">
                                            <span class="badge badge-{{ $equipment->status == 'Aktywna' ? 'success' : ($equipment->status == 'Nadchodząca' ? 'warning' : 'danger') }}">
                                                {{ $equipment->status }}
                                            </span>
                                    </td>
                                    <td class="border px-4 py-2">{{ $equipment->rental_price }} PLN</td>
                                    <td class="border px-4 py-2">-{{ $equipment->rental_price - $equipment->finalPrice() }} PLN</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                </div>
            </div>
        @endforeach
    </div>

    <script>
        // Funkcja do togglowania widoczności sekcji akordeonu
        function toggleAccordion(accordionId) {
            const accordionContent = document.getElementById(accordionId);
            accordionContent.classList.toggle('hidden');
        }
    </script>
@endsection
