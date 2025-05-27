@extends('layouts.admin')

@section('admin-content')
    <div class="w-full mx-auto">

        {{-- Komunikat sukcesu --}}
        @if(session('success'))
            <div class="mb-6 rounded-md bg-green-100 px-4 py-3 text-sm text-green-800">
                {{ session('success') }}
            </div>
        @endif

        {{-- Nagłówek i przycisk dodania --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
            <h1 class="text-2xl font-bold text-gray-800">Lista sprzętu</h1>
            <a href="{{ route('admin.equipment.create') }}"
               class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-white shadow hover:bg-blue-700 transition">
                Dodaj nowy sprzęt
            </a>
        </div>

        {{-- Search bar --}}
        <form method="GET" action="{{ route('admin.equipment.index') }}" class="mb-4">
            <input type="text" name="search" placeholder="Szukaj po nazwie..." value="{{ request('search') }}"
                   class="w-full md:w-1/3 rounded-lg border border-gray-300 px-4 py-2 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
        </form>

        {{-- Lista kart sprzętu --}}
        <div class="space-y-4">
            @forelse ($equipments as $equipment)
                <div class="bg-white shadow rounded-lg p-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-sm text-gray-800">
                        <div><span class="font-semibold text-gray-600">ID:</span> {{ $equipment->id }}</div>
                        <div><span class="font-semibold text-gray-600">Nazwa:</span> {{ $equipment->name }}</div>
                        <div><span class="font-semibold text-gray-600">Cena:</span> {{ number_format($equipment->rental_price, 2) }} zł</div>
                        <div><span class="font-semibold text-gray-600">Dostępność:</span> {{ $equipment->availability }}</div>
                        <div><span class="font-semibold text-gray-600">Stan techniczny:</span> {{ $equipment->technical_state }}</div>
                        <div>
                            <span class="font-semibold text-gray-600">Miniatura:</span><br>
                            @if($equipment->thumbnail)
                                <img src="{{ asset($equipment->thumbnail) }}" alt="miniatura"
                                     class="h-20 mt-1 object-contain rounded border border-gray-200">
                            @else
                                <span class="text-gray-400 italic">Brak</span>
                            @endif
                        </div>
                    </div>

                    {{-- Akcje --}}
                    <div class="flex flex-wrap justify-end gap-2 mt-4">
                        <a href="{{ route('equipment.show', $equipment->id) }}"
                           class="inline-flex items-center rounded bg-indigo-600 px-4 py-2 text-white text-sm hover:bg-indigo-700">
                            Pokaż
                        </a>

                        <a href="{{ route('admin.equipment.edit', $equipment->id) }}"
                           class="inline-flex items-center rounded bg-yellow-400 px-4 py-2 text-white text-sm hover:bg-yellow-500">
                            Edytuj
                        </a>

                        <form action="{{ route('admin.equipment.destroy', $equipment->id) }}" method="POST"
                              onsubmit="return confirm('Na pewno chcesz usunąć ten sprzęt?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center rounded bg-red-500 px-4 py-2 text-white text-sm hover:bg-red-600">
                                Usuń
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-center text-gray-500 py-8">Brak wyników.</div>
            @endforelse
        </div>

        {{-- Paginacja --}}
        <div class="mt-6">
            {{ $equipments->links() }}
        </div>
    </div>
@endsection

