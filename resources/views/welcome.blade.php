@extends('layouts.app')

@section('content')
    {{-- HERO SEKCJA --}}
    <section class="max-w-[1140px] mx-auto grid md:grid-cols-2 bg-white shadow overflow-hidden">
        {{-- LEWA KOLUMNA --}}
        <div class="bg-[#f56600] text-white flex flex-col justify-center px-8 py-16">
            <h2 class="text-2xl md:text-3xl font-semibold">Wypożyczalnia narzędzi</h2>
            <h1 class="text-4xl md:text-5xl font-extrabold">Budowlanych</h1>
        </div>

        {{-- PRAWA KOLUMNA --}}
        <div class="bg-cover bg-center bg-no-repeat h-full"
             style="background-image: url('images/heroImage.jpg'); min-height: 250px;">
            {{-- Można też zrobić <img src="/images/koparka.jpg" class="object-cover w-full h-full" /> jeśli wolisz --}}
        </div>
    </section>

    {{-- TEKST POD HERO --}}
    <section class="max-w-[1140px] mx-auto px-4 py-12 text-center">
        <h2 class="text-2xl md:text-3xl font-bold mb-6">Nie masz odpowiedniego narzędzia?</h2>
        <p class="text-gray-700 mb-4 max-w-2xl mx-auto">
            W domu zawsze jest co robić. Do przeprowadzania regularnych konserwacji i drobnych napraw warto wyposażyć się w niezbędne narzędzia, żeby zawsze mieć je pod ręką.
        </p>
        <p class="text-gray-700 max-w-2xl mx-auto">
            Jeśli do budowy, remontu czy montażu potrzebujesz sprzętu, który nie jest niezbędny na co dzień – wypożycz go. Po co kupować coś, co przyda się tylko raz?
        </p>
    </section>


    @include('components.features-section')

    @php
        $faqs = [
            [
                'question' => 'Jaki jest koszt wynajmu sprzętu?',
                'answer' => "Stawki za wynajem sprzętów (zgrzewarka, zaciskarka...) różnią się pomiędzy marketami.\nKoszt zależy od rodzaju sprzętu oraz zadeklarowanego czasu najmu."
            ],
            [
                'question' => 'Jakie są warunki wynajmu sprzętu?',
                'answer' => "Różnią się w zależności od sprzętu.\nNa przykładzie przyczepy:\n- Nie ma możliwości rezerwacji\n- Wymagane 2 dokumenty\n- Trzeba oddać w tym samym miejscu"
            ],
            [
                'question' => 'Jakie są formalności przy wynajmie sprzętu?',
                'answer' => "Umowa najmu określająca warunki użytkowania i zwrotu sprzętu."
            ],
        ];
    @endphp

    <section class="max-w-[1140px] mx-auto px-4 py-12 text-center">
            <h2 class="text-2xl md:text-3xl font-bold mb-6">Tutaj będzie sekcja</h2>
            <p class="text-gray-700 max-w-2xl mx-auto">
                        z najczęściej wynajmowanymi sprzętami
                        </p>
        </section>

    {{-- FAQ Section --}}
    <x-faq-accordion :faqs="$faqs" />

@endsection
