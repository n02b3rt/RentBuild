@extends('layouts.admin')

@section('admin-content')
    <h1 class="text-3xl font-semibold mb-6">Wybierz użytkownika</h1>

    {{-- Pasek wyszukiwania --}}
    <form method="GET" action="{{ route('admin.rentals.create.step1') }}" class="mb-6 flex items-center space-x-2 max-w-md">
        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Szukaj użytkownika (imię, nazwisko, email)"
            class="border p-2 rounded flex-grow"
            autocomplete="off"
        />
        <button type="submit" class="bg-orange-600 text-white rounded px-4 py-2 hover:bg-orange-700 transition">Szukaj</button>
    </form>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <div class="space-y-4 max-w-xl">
            @forelse ($users as $user)
                <div class="bg-white border border-gray-200 rounded-lg p-4 flex justify-between items-center hover:shadow-md transition">
                    <div>
                        <div class="text-lg font-semibold">{{ $user->first_name }} {{ $user->last_name }}</div>
                        <div class="text-sm text-gray-600">Email: <span class="font-medium">{{ $user->email }}</span></div>
                    </div>
                    <form method="POST" action="{{ route('admin.rentals.create.step1.select') }}">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                        <button type="submit" class="bg-orange-600 text-white rounded px-4 py-2 hover:bg-orange-700 transition">
                            Wybierz
                        </button>
                    </form>

                </div>
            @empty
                <div class="text-center text-gray-500">Brak użytkowników</div>
            @endforelse
        </div>
    </div>

    <div class="mt-6">
        {{ $users->appends(['search' => request('search')])->links() }}
    </div>
@endsection
