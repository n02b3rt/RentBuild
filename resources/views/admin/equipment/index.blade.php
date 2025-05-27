@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

        @if(session('success'))
            <div class="mb-6 rounded-md bg-green-100 px-4 py-3 text-sm text-green-800">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Lista sprzętu</h1>
            <a href="{{ route('admin.equipment.create') }}"
               class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-white shadow hover:bg-blue-700 transition">
                Dodaj nowy sprzęt
            </a>
        </div>

        <div class="overflow-x-auto bg-white shadow-md rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100 text-gray-700 text-left text-sm font-semibold">
                <tr>
                    <th class="px-4 py-3">ID</th>
                    <th class="px-4 py-3">Nazwa</th>
                    <th class="px-4 py-3">Cena</th>
                    <th class="px-4 py-3">Dostępność</th>
                    <th class="px-4 py-3">Stan techniczny</th>
                    <th class="px-4 py-3">Miniatura</th>
                    <th class="px-4 py-3">Akcje</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm text-gray-800">
                @foreach ($equipments as $equipment)
                    <tr>
                        <td class="px-4 py-3">{{ $equipment->id }}</td>
                        <td class="px-4 py-3">{{ $equipment->name }}</td>
                        <td class="px-4 py-3">{{ number_format($equipment->rental_price, 2) }} zł</td>
                        <td class="px-4 py-3 capitalize">{{ $equipment->availability }}</td>
                        <td class="px-4 py-3 capitalize">{{ $equipment->technical_state }}</td>
                        <td class="px-4 py-3">
                            @if($equipment->thumbnail)
                                <img src="{{ asset($equipment->thumbnail) }}" alt="miniatura" class="h-14 object-contain rounded">
                            @else
                                <span class="text-gray-400 italic">Brak</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 space-x-1">
                            <a href="{{ route('equipment.show', $equipment->id) }}"
                               class="inline-flex items-center rounded bg-indigo-600 px-3 py-1.5 text-white text-xs hover:bg-indigo-700">
                                Pokaż
                            </a>
                            
                            <a href="{{ route('admin.equipment.edit', $equipment->id) }}"
                               class="inline-flex items-center rounded bg-yellow-400 px-3 py-1.5 text-white text-xs hover:bg-yellow-500">
                                Edytuj
                            </a>

                            <form action="{{ route('admin.equipment.destroy', $equipment->id) }}" method="POST"
                                  class="inline-block"
                                  onsubmit="return confirm('Na pewno chcesz usunąć ten sprzęt?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center rounded bg-red-500 px-3 py-1.5 text-white text-xs hover:bg-red-600">
                                    Usuń
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
