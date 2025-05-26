@extends('layouts.app')

@section('content')
    <div class="admin-panel flex admin-content max-w-[1140px] min-h-[500px] mx-auto px-4 py-12">
        <x-admin-navbar />

        <div class="w-full">
            @yield('admin-content')
        </div>
    </div>
@endsection

<script>
    document.querySelectorAll('.menu-item > a').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();  // Zatrzymuje domyślne działanie (np. przejście do strony)

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
</script>
