@extends('layouts.admin')

@section('admin-content')
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between">
            <h1 class="text-3xl font-semibold mb-6">Promocje pojedyncze</h1>

            <a href="{{ route('admin.promotions.single.create') }}"
               class="inline-block h-fit  bg-[#f56600] text-white font-bold rounded py-2 px-4">
                Dodaj promocję pojedynczą
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 text-green-800 rounded border border-green-300">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 rounded shadow-sm">
                <thead class="bg-gray-100">
                <tr>
                    <th class="text-left px-4 py-3 border-b border-gray-200">ID</th>
                    <th class="text-left px-4 py-3 border-b border-gray-200">Nazwa sprzętu</th>
                    <th class="text-left px-4 py-3 border-b border-gray-200">Rabat (%)</th>
                    <th class="text-left px-4 py-3 border-b border-gray-200">Status</th>
                    <th class="text-left px-4 py-3 border-b border-gray-200">Okres promocji</th>
                    <th class="text-left px-4 py-3 border-b border-gray-200">Akcje</th>
                </tr>
                </thead>
                <tbody>
                @foreach($promotionsWithStatus as $promotion)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 border-b border-gray-200">{{ $promotion->id }}</td>
                        <td class="px-4 py-3 border-b border-gray-200">{{ $promotion->name }}</td>
                        <td class="px-4 py-3 border-b border-gray-200">{{ $promotion->discount }}</td>
                        <td class="px-4 py-3 border-b border-gray-200">{{ $promotion->status }}</td>
                        <td class="px-4 py-3 border-b border-gray-200 whitespace-nowrap">
                            @if($promotion->start_datetime && $promotion->end_datetime)
                                {{ \Carbon\Carbon::parse($promotion->start_datetime)->format('Y-m-d') }}
                                -
                                {{ \Carbon\Carbon::parse($promotion->end_datetime)->format('Y-m-d') }}
                            @else
                                Bezterminowa
                            @endif
                        </td>
                        <td class="px-4 py-3 border-b border-gray-200 whitespace-nowrap space-x-2">
                            <a href="{{ route('admin.promotions.single.edit', $promotion->id) }}"
                               class="inline-block px-3 py-1 bg-[#f56600] text-white rounded transition text-sm font-medium">
                                Edytuj
                            </a>

                            <form action="{{ route('admin.promotions.single.destroy', $promotion->id) }}" method="POST"
                                  class="inline-block"
                                  onsubmit="return confirm('Czy na pewno chcesz usunąć tę promocję?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition text-sm font-medium">
                                    Usuń
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                @if($promotionsWithStatus->isEmpty())
                    <tr>
                        <td colspan="6" class="text-center py-6 text-gray-500">Brak promocji do wyświetlenia.</td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection
