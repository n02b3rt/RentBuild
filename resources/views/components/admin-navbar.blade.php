<!-- resources/views/components/admin-navbar.blade.php -->
<nav class="admin-nav shadow-lg bg-[#f56600] h-[404px] mr-8 font-bold" id="admin-nav">
    <ul class="border-b-[1px] border-white w-72">
        <li class="menu-item border-b-[1px] border-white">
            <a href="{{ route('admin.dashboard') }}" id="dashboard" class="block px-4 py-4 text-white border-b-[1px] border-white rounded  transition">Dashboard</a>
        </li>
        <li class="menu-item border-b-[1px] border-white">
            <a href="#" id="settings" class="block px-4 py-2 text-white border-b-[1px] border-white rounded  transition">Sprzęty</a>
            <ul id="submenu-settings" class="submenu pl-4 bg-white space-y-2 max-h-0 overflow-hidden transition-all duration-200 ease-in-out">
                <li><a href="#" class="block px-4 py-2 text-[#f56600] border-b-[1px] border-[#f56600] transition">{Miejsce na tekst}</a></li>
                <li><a href="#" class="block px-4 py-2 text-[#f56600] border-b-[1px] border-[#f56600] transition">{Miejsce na tekst}</a></li>
                <li><a href="#" class="block px-4 py-2 text-[#f56600] transition">{Miejsce na tekst}</a></li>
            </ul>
        </li>
        <li class="menu-item border-b-[1px] border-white">
            <a href="#" id="users" class="block px-4 py-2 text-white border-b-[1px] border-white rounded  transition">Promocje</a>
            <ul id="submenu-users" class="submenu pl-4 bg-white space-y-2 max-h-0 overflow-hidden transition-all duration-200 ease-in-out">
                <li><a href="{{ route('admin.promotions.category') }}" class="block px-4 py-2 text-[#f56600] border-b-[1px] border-[#f56600] transition">Na kategorie</a></li>
                <li><a href="" class="block px-4 py-2 text-[#f56600] transition">{Miejsce na tekst}</a></li>
            </ul>
        </li>
        <li class="menu-item">
            <a href="#" id="reports" class="block px-4 py-2 text-white border-b-[1px] border-white rounded  transition">Raporty</a>
            <ul id="submenu-users" class="submenu pl-4 bg-white space-y-2 max-h-0 overflow-hidden transition-all duration-200 ease-in-out">
                <li><a href="#" class="block px-4 py-2 text-[#f56600] border-b-[1px] border-[#f56600] transition">{Miejsce na tekst}</a></li>
                <li><a href="#" class="block px-4 py-2 text-[#f56600] transition">{Miejsce na tekst}</a></li>
            </ul>
        </li>
    </ul>
</nav>

<script>
    document.querySelectorAll('.menu-item > a').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault(); // Zatrzymuje domyślne działanie (np. przejście do strony)

            // Zamknij wszystkie inne submenu
            document.querySelectorAll('.submenu').forEach(submenu => {
                if (!submenu.contains(item)) {
                    submenu.classList.remove('max-h-40');
                    submenu.classList.add('max-h-0');
                }
            });

            const parentMenuItem = item.parentElement;
            const submenu = parentMenuItem.querySelector('.submenu');

            // Toggle rozwinięcia submenu
            if (submenu.classList.contains('max-h-0')) {
                submenu.classList.remove('max-h-0');
                submenu.classList.add('max-h-40'); // Wysokość dla rozwiniętego menu
            } else {
                submenu.classList.remove('max-h-40');
                submenu.classList.add('max-h-0'); // Zwijanie menu
            }
        });
    });

    // Funkcja do obsługi sticky menu
    window.onscroll = function() {
        stickyNavbar();
    };

    const navbar = document.getElementById('admin-nav');
    const stickyOffset = navbar.offsetTop;

    function stickyNavbar() {
        if (window.pageYOffset > stickyOffset) {
            navbar.classList.add('sticky', 'top-[20px]', 'z-50');
        } else {
            navbar.classList.remove('sticky', 'top-0', 'z-50');
        }
    }
</script>
