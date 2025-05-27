@extends('layouts.app')

@section('content')
    <div class="admin-panel md:flex max-w-[1140px] min-h-[500px] mx-auto px-4 py-12 gap-6">
        <x-admin-navbar />

        <div class="w-full">
            @yield('admin-content')
        </div>
    </div>
    <!-- Scroll to Top Button -->
    <button id="scrollToTopBtn"
            class="fixed bottom-6 right-6 z-50 bg-blue-600 text-white p-3 rounded-full shadow-lg hover:bg-blue-700 transition">
        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="M440-160v-487L216-423l-56-57 320-320 320 320-56 57-224-224v487h-80Z"/></svg>
    </button>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const scrollBtn = document.getElementById('scrollToTopBtn');

            // Pokaż/ukryj przycisk na scroll
            window.addEventListener('scroll', () => {
                if (window.scrollY > 300) {
                    scrollBtn.classList.remove('hidden');
                } else {
                    scrollBtn.classList.add('hidden');
                }
            });

            // Przewiń do góry po kliknięciu
            scrollBtn.addEventListener('click', () => {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        });
    </script>

@endsection
