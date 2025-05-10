<!-- resources/views/welcome.blade.php -->

@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-8">
        <!-- Baner -->
        <div class="bg-blue-500 text-white text-center py-12 rounded-lg shadow-lg">
            <h1 class="text-4xl font-bold">Wypożycz sprzęt budowlany w prosty sposób</h1>
            <p class="mt-4 text-lg">Znajdź odpowiedni sprzęt, zarezerwuj online i odbierz go na budowie.</p>
            <a href="/login" class="mt-6 inline-block bg-orange-500 text-white px-6 py-3 rounded-lg hover:bg-orange-600">Zaloguj się</a>
            <a href="/register" class="mt-6 inline-block ml-4 bg-green-500 text-white px-6 py-3 rounded-lg hover:bg-green-600">Zarejestruj się</a>
        </div>

        <!-- Popularne kategorie -->
        <div class="mt-12">
            <h2 class="text-2xl font-semibold text-center">Popularne kategorie</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-8 mt-6">
                <!-- Kategoria: Koparki -->
                <div class="category-card p-4 bg-white rounded-lg shadow-md hover:bg-gray-50">
                    <h3 class="mt-4 text-xl text-center font-medium">Koparki</h3>
                </div>

                <!-- Kategoria: Wiertnice -->
                <div class="category-card p-4 bg-white rounded-lg shadow-md hover:bg-gray-50">
                    <h3 class="mt-4 text-xl text-center font-medium">Wiertnice</h3>
                </div>

                <!-- Kategoria: Dźwigi -->
                <div class="category-card p-4 bg-white rounded-lg shadow-md hover:bg-gray-50">
                    <h3 class="mt-4 text-xl text-center font-medium">Dźwigi</h3>
                </div>

                <!-- Kategoria: Wózki widłowe -->
                <div class="category-card p-4 bg-white rounded-lg shadow-md hover:bg-gray-50">
                    <h3 class="mt-4 text-xl text-center font-medium">Wózki widłowe</h3>
                </div>
            </div>
        </div>

        <!-- Przyciski CTA -->
        <div class="mt-12 text-center">
            <a href="/offer" class="inline-block bg-blue-600 text-white px-8 py-4 rounded-lg hover:bg-blue-700">Zobacz ofertę</a>
        </div>
    </div>
@endsection
