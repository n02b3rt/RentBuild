@extends('layouts.admin') {{-- Twój layout panelu admina --}}

@section('admin-content')
    <div class="container mx-auto py-8">
        <h1 class="text-2xl font-bold mb-4">Stawki operatora wg kategorii</h1>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <table class="min-w-full bg-white shadow rounded">
            <thead>
            <tr>
                <th class="px-4 py-2 text-left">Kategoria sprzętu</th>
                <th class="px-4 py-2 text-right">Operator (zł/dzień)</th>
                <th class="px-4 py-2"></th>
            </tr>
            </thead>
            <tbody>
            @foreach($rates as $rate)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $rate->category }}</td>
                    <td class="px-4 py-2 text-right">{{ number_format($rate->operator_rate,2,',',' ') }}</td>
                    <td class="px-4 py-2 text-right">
                        <a href="{{ route('admin.operator-rates.edit', urlencode($rate->category)) }}"
                           class="text-indigo-600 hover:underline">Edytuj</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
