<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name', 'RentBuild') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-800 font-sans">

{{-- HEADER (komponent Blade) --}}
<x-header />

{{-- MAIN --}}
<x-container>
    @yield('content')
</x-container>


</body>
</html>
