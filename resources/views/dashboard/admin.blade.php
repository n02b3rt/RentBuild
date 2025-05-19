<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Panel administratora
        </h2>
    </x-slot>

    <div class="p-6">
        <h1 class="text-2xl font-bold">Witaj, {{ Auth::user()->first_name }} (ADMIN)!</h1>
        <p>Masz dostęp do zarządzania systemem.</p>
    </div>
</x-app-layout>
