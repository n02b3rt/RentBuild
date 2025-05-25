<nav class="bg-white border-b border-gray-100">
    <!-- Desktop Header -->
    <header class="hidden md:block sticky top-0 bg-white shadow z-50">
        <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
            <a href="{{ url('/') }}" class="text-[#f56600] text-stroke text-4xl font-bold text-brand">RentBuild</a>

            <nav class="flex items-center space-x-6">
                <a href="#" class="hover:text-brand">O nas</a>
                <a href="#" class="hover:text-brand">Kontakt</a>
                <a href="#" class="hover:text-brand">Oferta</a>

                @auth
                    <!-- User Dropdown -->
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition">
                                <div>{{ Auth::user()->first_name }}</div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('dashboard')">
                                Dashboard
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('profile.edit')">
                                Profil
                            </x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                                 onclick="event.preventDefault(); this.closest('form').submit();">
                                    Wyloguj się
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <a href="{{ route('login') }}" class="bg-[#f56600] px-4 py-2 rounded-md text-sm font-semibold hover:bg-orange-700 text-white">
                        Zaloguj się
                    </a>
                @endauth
            </nav>
        </div>
    </header>

    <!-- Mobile Header -->
    <header class="block md:hidden sticky top-0 bg-white shadow z-50">
        <div class="px-4 py-4 flex items-center justify-between">
            <a href="{{ url('/') }}" class="text-[#f56600] text-stroke text-xl font-bold text-brand">RentBuild</a>
            <button id="mobile-menu-toggle" class="text-2xl">☰</button>
        </div>

        <div id="mobile-menu" class="hidden grid gap-4 px-4 pb-4">
            <a href="#" class="hover:text-brand">O nas</a>
            <a href="#" class="hover:text-brand">Kontakt</a>
            <a href="#" class="hover:text-brand">Oferta</a>

            @auth
                <a href="{{ route('dashboard') }}" class="hover:text-brand">Dashboard</a>
                <a href="{{ route('profile.edit') }}" class="hover:text-brand">Profil</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="hover:text-brand">Wyloguj się</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="hover:text-brand">Zaloguj się</a>
            @endauth
        </div>
    </header>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleButton = document.getElementById('mobile-menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');

        toggleButton.addEventListener('click', function () {
            mobileMenu.classList.toggle('hidden');
        });
    });
</script>

