@extends('layouts.admin')

@section('admin-content')
    <h1 class="text-3xl font-bold mb-8 text-[#f56600]">Lista reklamacji</h1>

    @if($complaints->isEmpty())
        <p class="text-gray-600 italic">Brak zgłoszonych reklamacji.</p>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-300 rounded-lg divide-y divide-gray-200">
                <thead class="bg-[#f56600] text-white">
                <tr>
                    <th class="px-4 py-3 text-left">
                        <x-sort-link field="id" label="ID" />
                    </th>
                    <th class="px-4 py-3 text-left">
                        Sprzęt
                    </th>
                    <th class="px-4 py-3 text-left">
                        <x-sort-link field="user" label="Użytkownik" />
                    </th>
                    <th class="px-4 py-3 text-left">
                        <x-sort-link field="status" label="Status" />
                    </th>
                    <th class="px-4 py-3 text-left">
                        <x-sort-link field="updated_at" label="Data zgłoszenia" />
                    </th>
                    <th class="px-4 py-3 text-left">
                        Akcje
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @foreach($complaints as $rental)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 border">{{ $rental->id }}</td>
                        <td class="px-4 py-3 border">{{ $rental->equipment->name }}</td>
                        <td class="px-4 py-3 border">
                            {{ $rental->user->first_name }} {{ $rental->user->last_name }}
                        </td>
                        <td class="px-4 py-3 border capitalize">{{ str_replace('_', ' ', $rental->status) }}</td>
                        <td class="px-4 py-3 border">{{ optional($rental->updated_at)->format('Y-m-d H:i') }}</td>
                        <td class="px-4 py-3 border">
                            <a href="{{ route('admin.rentals.complaints.show', $rental) }}"
                               class="text-[#f56600] hover:underline font-semibold">
                                Szczegóły
                            </a>
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
@endsection
