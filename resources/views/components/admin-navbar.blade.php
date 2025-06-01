{{-- MOBILE BURGER --}}
<div class="md:hidden flex justify-between items-center bg-[#f56600] px-4 py-3 text-white font-bold">
    <span>Panel admina</span>
    <button id="burger-btn">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
             viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>
</div>

{{-- RESPONSYWNE MENU --}}
<nav id="admin-nav"
     class="admin-nav bg-[#f56600] md:mr-8 md:mb-10 md:w-72 text-white font-bold
            hidden md:block md:max-h-[calc(500px-5rem)] md:overflow-y-auto transition-all duration-300">


    <ul class="w-full md:border-b-[1px] md:border-white space-y-0 md:space-y-0" id="admin-menu">
        <li class="menu-item border-b-[1px] border-white">
            <a href="{{ route('admin.dashboard') }}"
               class="block px-4 py-4 border-b border-white transition">Dashboard</a>
        </li>

        <li class="menu-item border-b-[1px] border-white">
            <a href="#" class="block px-4 py-2 border-b border-white transition">Sprzęty</a>
            <ul class="submenu pl-4 bg-white space-y-1 max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
                <li><a href="{{ route('admin.equipment.create') }}"
                       class="block px-4 py-2 text-[#f56600] border-b border-[#f56600]">Dodaj sprzęt</a></li>
                <li><a href="{{ route('admin.equipment.index') }}"
                       class="block px-4 py-2 text-[#f56600] border-b border-[#f56600]">Lista sprzętu</a></li>
                <li><a href="{{ route('admin.operator-rates.index') }}"
                       class="block px-4 py-2 text-[#f56600] border-b">Pracownicy</a></li>
            </ul>
        </li>
        <li class="menu-item border-b-[1px] border-white">
            <a href="#" class="block px-4 py-2 border-b border-white transition">Użytkownicy</a>
            <ul class="submenu pl-4 bg-white space-y-1 max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
                <li><a href="{{ route('admin.users.index') }}" class="block px-4 py-2 text-[#f56600] border-b border-[#f56600]">Lista użytkowników</a></li>
                <li><a href="{{ route('admin.users.create') }}" class="block px-4 py-2 text-[#f56600] border-b ">Dodaj użytkownika</a></li>
            </ul>
        </li>
        <li class="menu-item border-b-[1px] border-white">
            <a href="#" class="block px-4 py-2 border-b border-white transition">Promocje</a>
            <ul class="submenu pl-4 bg-white space-y-1 max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
                <li><a href="{{ route('admin.promotions.category') }}"
                       class="block px-4 py-2 text-[#f56600] border-b border-[#f56600]">Na kategorie</a></li>
            </ul>
        </li>

        <li class="menu-item border-b-[1px] border-white">
            <a href="#" class="block px-4 py-2 border-b border-white transition">Raporty</a>
            <ul class="submenu pl-4 bg-white space-y-1 max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
                <li><a href="#" class="block px-4 py-2 text-[#f56600] border-b border-[#f56600]">Coś tam</a></li>
            </ul>
        </li>
    </ul>
</nav>

{{-- SCRIPT --}}
<script>
    // BURGER MENU TOGGLE (mobile)
    document.addEventListener('DOMContentLoaded', function () {
        const burger = document.getElementById('burger-btn');
        const nav = document.getElementById('admin-nav');

        burger?.addEventListener('click', () => {
            nav.classList.toggle('hidden');
        });

        // Dropdown submenu
        document.querySelectorAll('.menu-item > a').forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();

                const submenu = this.parentElement.querySelector('.submenu');
                if (!submenu) return;

                // Zamykamy inne submenu
                document.querySelectorAll('.submenu').forEach(sm => {
                    if (sm !== submenu) {
                        sm.classList.remove('max-h-40');
                        sm.classList.add('max-h-0');
                    }
                });

                submenu.classList.toggle('max-h-0');
                submenu.classList.toggle('max-h-40');
            });
        });
    });
</script>
