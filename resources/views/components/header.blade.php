{{-- HEADER DESKTOP --}}
<header id="header-desktop" class="hidden md:block sticky top-0 bg-white shadow z-50">
    <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
        <a href="{{ url('/') }}" class="text-[#f56600] text-stroke text-4xl font-bold text-brand">RentBuild</a>

        <nav class="flex items-center space-x-6">
            <a href="#" class="hover:text-brand">O nas</a>
            <a href="#" class="hover:text-brand">Kontakt</a>
            <a href="#" class="hover:text-brand">Sprzęt</a>
            <a href="#" class="hover:text-brand">Oferta</a>
            <a href="#" class="bg-brand px-4 py-2 rounded-md text-sm font-semibold hover:bg-orange-700">
                Zaloguj się
            </a>
        </nav>
    </div>
</header>

{{-- HEADER MOBILE --}}
<header id="header-mobile" class="block md:hidden sticky top-0 bg-white shadow z-50">
    <div class="px-4 py-4 flex items-center justify-between">
        <a href="{{ url('/') }}" class="text-[#f56600] text-stroke text-xl font-bold text-brand">RentBuild</a>
        <button id="mobile-menu-toggle" class="text-2xl">☰</button>
    </div>

    <div id="mobile-menu" class="hidden flex-col px-4 pb-4 space-y-2">
        <a href="#" class="hover:text-brand">O nas</a>
        <a href="#" class="hover:text-brand">Kontakt</a>
        <a href="#" class="hover:text-brand">Sprzęt</a>
        <a href="#" class="hover:text-brand">Oferta</a>
        <a href="#" class="bg-brand text-white px-4 py-2 rounded hover:bg-orange-700">Zaloguj się</a>
    </div>
</header>
