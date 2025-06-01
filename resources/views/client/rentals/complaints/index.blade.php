@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow-lg">

        <h1 class="text-3xl font-extrabold mb-8 text-[#f56600]">Twoje reklamacje</h1>

        @if($complaints->isEmpty())
            <p class="text-gray-600 italic">Nie masz żadnych zgłoszonych reklamacji.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-300 rounded-lg divide-y divide-gray-200">
                    <thead class="bg-[#f56600] text-white">
                    <tr>
                        <th class="px-4 py-3 text-left">ID</th>
                        <th class="px-4 py-3 text-left">Sprzęt</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Podstatus</th>
                        <th class="px-4 py-3 text-left">Data zgłoszenia</th>
                        <th class="px-4 py-3 text-left">Szczegóły</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($complaints as $rental)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 border">{{ $rental->id }}</td>
                            <td class="px-4 py-3 border">{{ $rental->equipment->name }}</td>
                            <td class="px-4 py-3 border capitalize">{{ str_replace('_', ' ', $rental->status) }}</td>
                            <td class="px-4 py-3 border capitalize">{{ $rental->complaintStatus() ?? 'Brak' }}</td>
                            <td class="px-4 py-3 border">{{ optional($rental->updated_at)->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-3 border">
                                <a href="{{ route('client.rentals.complaints.show', $rental) }}" class="text-[#f56600] hover:underline font-semibold">Zobacz</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $complaints->links() }}
            </div>
        @endif
    </div>
@endsection
